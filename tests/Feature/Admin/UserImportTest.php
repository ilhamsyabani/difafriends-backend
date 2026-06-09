<?php

use App\Models\User;
use App\Notifications\AccountCredentialsNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

function csvUpload(string $content): UploadedFile
{
    return UploadedFile::fake()->createWithContent('users.csv', $content);
}

test('admin can download the import template', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.users.template'));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))
        ->toContain('template-import-pengguna.xlsx');
});

test('admin can import users and credentials are emailed', function () {
    Notification::fake();

    $csv = "first_name,last_name,email,role,phone,city,gender\n"
        ."Budi,Santoso,budi@example.com,user,081,Bandung,male\n"
        ."Siti,Aminah,siti@example.com,instructor,082,Surabaya,female\n";

    $response = $this->actingAs($this->admin)
        ->post(route('admin.users.import'), ['file' => csvUpload($csv)]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $budi = User::where('email', 'budi@example.com')->first();
    expect($budi)->not->toBeNull()
        ->and($budi->first_name)->toBe('Budi')
        ->and($budi->role->value)->toBe('user')
        ->and($budi->is_active)->toBeTrue();

    expect(User::where('email', 'siti@example.com')->first()->role->value)
        ->toBe('instructor');

    Notification::assertSentTo($budi, AccountCredentialsNotification::class);
    Notification::assertSentTo(
        User::where('email', 'siti@example.com')->first(),
        AccountCredentialsNotification::class,
    );
});

test('invalid rows are skipped and reported without aborting valid ones', function () {
    Notification::fake();
    User::factory()->create(['email' => 'taken@example.com']);

    $csv = "first_name,last_name,email,role,phone,city,gender\n"
        .'Valid,User,valid@example.com,user,081,Bandung,male'."\n"
        .'Dupe,User,taken@example.com,user,082,Medan,male'."\n"   // email sudah ada
        .',NoName,noname@example.com,user,083,Solo,male'."\n";      // first_name kosong

    $response = $this->actingAs($this->admin)
        ->post(route('admin.users.import'), ['file' => csvUpload($csv)]);

    $response->assertSessionHas('success');
    $response->assertSessionHas('error');

    expect(User::where('email', 'valid@example.com')->exists())->toBeTrue();
    expect(User::where('email', 'noname@example.com')->exists())->toBeFalse();
    expect(User::where('email', 'taken@example.com')->count())->toBe(1);
});

test('import rejects non-spreadsheet files', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.users.import'), [
            'file' => UploadedFile::fake()->create('malware.pdf', 10, 'application/pdf'),
        ]);

    $response->assertSessionHasErrors('file');
});

test('non-admin cannot access import', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.users.import'), ['file' => csvUpload("first_name\nBudi\n")])
        ->assertForbidden();
});
