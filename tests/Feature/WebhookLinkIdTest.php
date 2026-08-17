<?php

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;

const LINKID_TEST_SECRET = 'test-linkid-secret';

function linkidSignature(array $payload, string $secret = LINKID_TEST_SECRET): string
{
    return hash_hmac('sha256', json_encode($payload), $secret);
}

function linkidPayload(Order $order, array $overrides = []): array
{
    return array_merge([
        'external_id' => $order->invoice_number,
        'transaction_id' => 'LNK-'.strtoupper(fake()->unique()->bothify('????-####')),
        'status' => 'paid',
        'amount' => (int) $order->final_amount,
        'payment_type' => 'bank_transfer',
        'paid_at' => now()->toIso8601String(),
    ], $overrides);
}

function linkidPost($test, string $url, array $payload): TestResponse
{
    $signature = linkidSignature($payload);

    return $test->withHeaders(['X-Linkid-Signature' => $signature])
        ->postJson($url, $payload);
}

beforeEach(function () {
    config(['linkid.webhook_secret' => LINKID_TEST_SECRET]);

    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();

    $this->course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
    ]);

    $this->user = User::factory()->create();

    $this->order = Order::factory()->create([
        'user_id' => $this->user->id,
        'orderable_type' => Course::class,
        'orderable_id' => $this->course->id,
        'status' => OrderStatus::Pending,
    ]);
});

// ── Status paid / settlement ──────────────────────────────────────────────

test('linkid webhook membuat payment record saat status paid', function () {
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order))
        ->assertOk();

    expect(Payment::where('order_id', $this->order->id)->exists())->toBeTrue();
});

test('linkid webhook update order ke Paid saat status paid', function () {
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order))
        ->assertOk();

    $this->order->refresh();
    expect($this->order->status)->toBe(OrderStatus::Paid);
});

test('linkid webhook aktifkan enrollment course saat order course', function () {
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order))
        ->assertOk();

    expect(Enrollment::where('user_id', $this->user->id)
        ->where('course_id', $this->course->id)->exists())->toBeTrue();
});

test('linkid webhook tidak duplicate payment record (idempotent)', function () {
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order))->assertOk();
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order))->assertOk();

    expect(Payment::where('order_id', $this->order->id)->count())->toBe(1);
});

test('linkid webhook tidak duplicate enrollment saat dipanggil ulang', function () {
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order))->assertOk();
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order))->assertOk();

    expect(Enrollment::where('user_id', $this->user->id)
        ->where('course_id', $this->course->id)->count())->toBe(1);
});

test('linkid webhook set paid_at saat status paid', function () {
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order))
        ->assertOk();

    $payment = Payment::where('order_id', $this->order->id)->first();
    expect($payment->paid_at)->not->toBeNull();
});

// ── Status expired ────────────────────────────────────────────────────────

test('linkid webhook update order ke Expired saat status expired', function () {
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order, ['status' => 'expired']))
        ->assertOk();

    $this->order->refresh();
    expect($this->order->status)->toBe(OrderStatus::Expired);
});

// ── Status failed / cancelled ────────────────────────────────────────────

test('linkid webhook update order ke Cancelled saat status failed', function () {
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order, ['status' => 'failed']))
        ->assertOk();

    $this->order->refresh();
    expect($this->order->status)->toBe(OrderStatus::Cancelled);
});

test('linkid webhook update order ke Cancelled saat status cancelled', function () {
    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($this->order, ['status' => 'cancelled']))
        ->assertOk();

    $this->order->refresh();
    expect($this->order->status)->toBe(OrderStatus::Cancelled);
});

// ── Validasi ─────────────────────────────────────────────────────────────

test('linkid webhook tolak request dengan signature salah', function () {
    $this->withHeaders(['X-Linkid-Signature' => 'wrong-signature'])
        ->postJson('/api/v1/payments/linkid/webhook', linkidPayload($this->order))
        ->assertUnauthorized();
});

test('linkid webhook tolak request jika secret tidak diset', function () {
    config(['linkid.webhook_secret' => null]);

    $this->withHeaders(['X-Linkid-Signature' => 'apapun'])
        ->postJson('/api/v1/payments/linkid/webhook', linkidPayload($this->order))
        ->assertStatus(500);
});

test('linkid webhook tolak request tanpa signature header', function () {
    $this->postJson('/api/v1/payments/linkid/webhook', linkidPayload($this->order))
        ->assertUnauthorized();
});

test('linkid webhook return 404 jika order tidak ditemukan', function () {
    $payload = linkidPayload($this->order, ['external_id' => 'NONEXISTENT-INV-999']);

    linkidPost($this, '/api/v1/payments/linkid/webhook', $payload)
        ->assertNotFound();
});

test('linkid webhook return 422 jika missing external_id', function () {
    $payload = linkidPayload($this->order);
    unset($payload['external_id']);

    linkidPost($this, '/api/v1/payments/linkid/webhook', $payload)
        ->assertStatus(422);
});

test('linkid webhook return 422 jika missing status', function () {
    $payload = linkidPayload($this->order);
    unset($payload['status']);

    linkidPost($this, '/api/v1/payments/linkid/webhook', $payload)
        ->assertStatus(422);
});

// ── Booking orders ────────────────────────────────────────────────────────

test('linkid webhook confirm booking saat order adalah booking', function () {
    Notification::fake();

    $student = User::factory()->create();
    $companion = User::factory()->companion()->create();

    $booking = Booking::factory()->create([
        'student_id' => $student->id,
        'tutor_id' => $companion->id,
        'status' => BookingStatus::Pending,
    ]);

    $order = Order::factory()->create([
        'user_id' => $student->id,
        'orderable_type' => Booking::class,
        'orderable_id' => $booking->id,
        'status' => OrderStatus::Pending,
    ]);

    linkidPost($this, '/api/v1/payments/linkid/webhook', linkidPayload($order))->assertOk();

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Confirmed);
});
