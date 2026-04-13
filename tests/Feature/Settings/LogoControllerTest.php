<?php

use App\Enums\Roles;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('halaman logo tidak bisa diakses non-admin', function () {
    $user = User::factory()->create(['role' => Roles::User]);

    $this->actingAs($user)
        ->get(route('settings.logo.edit'))
        ->assertForbidden();
});

test('admin bisa melihat halaman logo', function () {
    $admin = User::factory()->create(['role' => Roles::Admin]);

    $this->actingAs($admin)
        ->get(route('settings.logo.edit'))
        ->assertOk();
});

test('admin bisa upload logo baru', function () {
    $admin = User::factory()->create(['role' => Roles::Admin]);
    $file = UploadedFile::fake()->image('logo.png', 200, 200);

    $this->actingAs($admin)
        ->post(route('settings.logo.update'), [
            'logo' => $file,
            'app_name' => 'DifaFriends Test',
        ])
        ->assertRedirect(route('settings.logo.edit'));

    expect(SiteSetting::get('app_logo'))->not->toBeNull();
    expect(SiteSetting::get('app_name'))->toBe('DifaFriends Test');
    Storage::disk('public')->assertExists(SiteSetting::get('app_logo'));
});

test('upload logo harus berupa gambar', function () {
    $admin = User::factory()->create(['role' => Roles::Admin]);
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $this->actingAs($admin)
        ->post(route('settings.logo.update'), ['logo' => $file])
        ->assertSessionHasErrors('logo');
});

test('admin bisa hapus logo', function () {
    $admin = User::factory()->create(['role' => Roles::Admin]);
    $file = UploadedFile::fake()->image('logo.png');
    $path = $file->store('logos', 'public');
    SiteSetting::set('app_logo', $path);

    $this->actingAs($admin)
        ->delete(route('settings.logo.destroy'))
        ->assertRedirect(route('settings.logo.edit'));

    expect(SiteSetting::get('app_logo'))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('logo lama dihapus saat upload logo baru', function () {
    $admin = User::factory()->create(['role' => Roles::Admin]);
    $old = UploadedFile::fake()->image('old.png')->store('logos', 'public');
    SiteSetting::set('app_logo', $old);

    $newFile = UploadedFile::fake()->image('new.png', 200, 200);

    $this->actingAs($admin)
        ->post(route('settings.logo.update'), ['logo' => $newFile]);

    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists(SiteSetting::get('app_logo'));
});
