<?php

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

function fakeGoogleUser(string $id, string $email, string $name = 'Budi Santoso', ?string $avatar = 'https://lh3.googleusercontent.com/a/photo'): void
{
    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn($id);
    $abstractUser->shouldReceive('getEmail')->andReturn($email);
    $abstractUser->shouldReceive('getName')->andReturn($name);
    $abstractUser->shouldReceive('getAvatar')->andReturn($avatar);

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
}

test('redirect endpoint sends the user to google', function () {
    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('google.redirect'));

    $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
});

test('new user is created and logged in via google callback', function () {
    fakeGoogleUser('google-123', 'newuser@gmail.com', 'Budi Santoso');

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $user = User::where('email', 'newuser@gmail.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->google_id)->toBe('google-123')
        ->and($user->first_name)->toBe('Budi')
        ->and($user->last_name)->toBe('Santoso')
        ->and($user->email_verified_at)->not->toBeNull();
});

test('existing email account gets linked to google', function () {
    $user = User::factory()->create([
        'email' => 'existing@gmail.com',
        'google_id' => null,
    ]);

    fakeGoogleUser('google-999', 'existing@gmail.com');

    $this->get(route('google.callback'));

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->google_id)->toBe('google-999');
    expect(User::where('email', 'existing@gmail.com')->count())->toBe(1);
});

test('failed google callback redirects to login with readable error', function () {
    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andThrow(new RuntimeException('invalid grant'));
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});
