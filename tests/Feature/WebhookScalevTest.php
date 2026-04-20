<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\ScalevWelcomeNotification;
use Illuminate\Support\Facades\Notification;

function scalevPayload(Course $course, array $customer = []): array
{
    return [
        'payment_status' => 'paid',
        'product_id' => $course->scalev_product_id,
        'customer' => array_merge([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ], $customer),
    ];
}

beforeEach(function () {
    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();

    $this->course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
        'scalev_product_id' => 'SCALEV-PROD-'.uniqid(),
    ]);
});

// ── User baru ──────────────────────────────────────────────────────────────

test('scalev webhook membuat user baru dan enrollment', function () {
    Notification::fake();

    $this->postJson('/webhook/scalev', scalevPayload($this->course))
        ->assertOk();

    $user = User::where('email', 'budi@example.com')->first();
    expect($user)->not->toBeNull();
    expect(Enrollment::where('user_id', $user->id)->where('course_id', $this->course->id)->exists())->toBeTrue();
});

test('scalev webhook mengirim email dengan password ke user baru', function () {
    Notification::fake();

    $this->postJson('/webhook/scalev', scalevPayload($this->course))
        ->assertOk();

    $user = User::where('email', 'budi@example.com')->first();
    Notification::assertSentTo($user, ScalevWelcomeNotification::class);
});

test('scalev webhook memverifikasi email user baru secara otomatis', function () {
    Notification::fake();

    $this->postJson('/webhook/scalev', scalevPayload($this->course))
        ->assertOk();

    $user = User::where('email', 'budi@example.com')->first();
    expect($user->email_verified_at)->not->toBeNull();
});

// ── User sudah ada ─────────────────────────────────────────────────────────

test('scalev webhook tidak membuat user baru jika email sudah terdaftar', function () {
    Notification::fake();

    $existingUser = User::factory()->create(['email' => 'budi@example.com']);

    $this->postJson('/webhook/scalev', scalevPayload($this->course))
        ->assertOk();

    expect(User::where('email', 'budi@example.com')->count())->toBe(1);
    Notification::assertNothingSentTo($existingUser, ScalevWelcomeNotification::class);
});

test('scalev webhook idempoten — tidak buat enrollment ganda', function () {
    Notification::fake();

    // Kirim dua kali
    $this->postJson('/webhook/scalev', scalevPayload($this->course))->assertOk();
    $this->postJson('/webhook/scalev', scalevPayload($this->course))->assertOk();

    $user = User::where('email', 'budi@example.com')->first();
    expect(Enrollment::where('user_id', $user->id)->where('course_id', $this->course->id)->count())->toBe(1);
});

// ── Validasi payload ───────────────────────────────────────────────────────

test('scalev webhook diabaikan jika payment_status bukan paid', function () {
    Notification::fake();

    $this->postJson('/webhook/scalev', [
        'payment_status' => 'pending',
        'product_id' => $this->course->scalev_product_id,
        'customer' => ['name' => 'Test', 'email' => 'test@example.com'],
    ])->assertOk();

    expect(User::where('email', 'test@example.com')->exists())->toBeFalse();
});

test('scalev webhook menolak jika secret tidak valid', function () {
    Notification::fake();

    config(['services.scalev.webhook_secret' => 'rahasia-123']);

    $this->postJson('/webhook/scalev', scalevPayload($this->course), [
        'X-Scalev-Secret' => 'salah',
    ])->assertUnauthorized();
});
