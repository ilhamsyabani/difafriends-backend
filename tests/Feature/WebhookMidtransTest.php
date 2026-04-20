<?php

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\OrderPaidNotification;
use Illuminate\Support\Facades\Notification;

function midtransPayload(Order $order, string $status = 'settlement'): array
{
    $serverKey = config('midtrans.server_key', 'test-server-key');
    $grossAmount = number_format((float) $order->final_amount, 2, '.', '');

    return [
        'transaction_id' => 'TRX-'.uniqid(),
        'order_id' => $order->invoice_number,
        'status_code' => '200',
        'gross_amount' => $grossAmount,
        'transaction_status' => $status,
        'payment_type' => 'bank_transfer',
        'signature_key' => hash('sha512', $order->invoice_number.'200'.$grossAmount.$serverKey),
    ];
}

// ── Payment Course ─────────────────────────────────────────────────────────

test('webhook settlement mengaktifkan enrollment course', function () {
    Notification::fake();

    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
    ]);
    $order = Order::factory()->pending()->create([
        'orderable_type' => Course::class,
        'orderable_id' => $course->id,
    ]);

    $this->postJson('/webhook/midtrans', midtransPayload($order))
        ->assertOk();

    expect($order->fresh()->status)->toBe(OrderStatus::Paid)
        ->and(Enrollment::where('user_id', $order->user_id)->where('course_id', $course->id)->exists())->toBeTrue();

    Notification::assertSentTo($order->user, OrderPaidNotification::class);
});

test('webhook capture juga mengaktifkan enrollment', function () {
    Notification::fake();

    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
    ]);
    $order = Order::factory()->pending()->create([
        'orderable_type' => Course::class,
        'orderable_id' => $course->id,
    ]);

    $this->postJson('/webhook/midtrans', midtransPayload($order, 'capture'))
        ->assertOk();

    expect($order->fresh()->status)->toBe(OrderStatus::Paid);
});

test('webhook idempoten jika order sudah paid', function () {
    Notification::fake();

    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
    ]);
    $order = Order::factory()->paid()->create([
        'orderable_type' => Course::class,
        'orderable_id' => $course->id,
    ]);

    $this->postJson('/webhook/midtrans', midtransPayload($order))
        ->assertOk();

    expect(Enrollment::where('user_id', $order->user_id)->where('course_id', $course->id)->count())
        ->toBeLessThanOrEqual(1);
    Notification::assertNothingSent();
});

test('webhook expire mengubah status ke expired', function () {
    Notification::fake();

    $order = Order::factory()->pending()->create();

    $this->postJson('/webhook/midtrans', midtransPayload($order, 'expire'))
        ->assertOk();

    expect($order->fresh()->status)->toBe(OrderStatus::Expired);
    Notification::assertNothingSent();
});

// ── Payment Booking ────────────────────────────────────────────────────────

test('webhook booking settlement mengkonfirmasi booking dan kirim notifikasi', function () {
    Notification::fake();

    $student = User::factory()->create();
    $booking = Booking::factory()->pending()->create(['student_id' => $student->id]);
    $order = Order::factory()->pending()->create([
        'user_id' => $student->id,
        'orderable_type' => Booking::class,
        'orderable_id' => $booking->id,
    ]);

    $this->postJson('/webhook/midtrans', midtransPayload($order))
        ->assertOk();

    expect($booking->fresh()->status)->toBe(BookingStatus::Confirmed)
        ->and($booking->fresh()->confirmed_at)->not->toBeNull();

    Notification::assertSentTo($student, BookingConfirmedNotification::class);
});

// ── Validasi signature ─────────────────────────────────────────────────────

test('webhook ditolak jika signature tidak valid', function () {
    Notification::fake();

    $order = Order::factory()->pending()->create();
    $payload = midtransPayload($order);
    $payload['signature_key'] = 'invalid-signature';

    $this->postJson('/webhook/midtrans', $payload)
        ->assertForbidden();

    expect($order->fresh()->status)->toBe(OrderStatus::Pending);
    Notification::assertNothingSent();
});
