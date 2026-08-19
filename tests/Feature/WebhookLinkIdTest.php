<?php

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;

const LYNK_TEST_KEY = 'test-lynk-merchant-key';

function lynkSignature(string $grandTotal, string $refId, string $messageId, string $key = LYNK_TEST_KEY): string
{
    // Signature: SHA256(amount + refId + message_id + merchantKey)
    return hash('sha256', $grandTotal.$refId.$messageId.$key);
}

function lynkSignatureActivity(int $amount, string $messageId, string $key = LYNK_TEST_KEY): string
{
    // Signature: SHA256(amount + message_id + merchantKey)
    return hash('sha256', (string) $amount.$messageId.$key);
}

function lynkPayload(Order $order, array $overrides = [], bool $reuseMessageId = false): array
{
    static $messageIdCache = [];

    $refId = $order->invoice_number;
    $grandTotal = (int) $order->final_amount;

    if ($reuseMessageId && isset($messageIdCache[$refId])) {
        $messageId = $messageIdCache[$refId];
    } else {
        $messageId = 'API_CALL_'.fake()->unique()->numerify('#############_######');
        $messageIdCache[$refId] = $messageId;
    }

    return array_merge([
        'event' => 'payment.received',
        'data' => [
            'message_action' => 'SUCCESS',
            'message_code' => '0',
            'message_data' => [
                'refId' => $refId,
                'totals' => [
                    'grandTotal' => $grandTotal,
                    'totalPrice' => $grandTotal,
                    'totalAddon' => 0,
                    'convenienceFee' => 0,
                ],
                'customer' => [
                    'email' => $order->user->email,
                    'name' => trim($order->user->first_name.' '.$order->user->last_name),
                    'phone' => $order->user->phone ?? '08123456789',
                ],
                'createdAt' => now()->toIso8601String(),
            ],
            'message_id' => $messageId,
        ],
    ], $overrides);
}

function lynkActivityPayload(int $activityCode, int $encodedAmount, array $customer = [], array $overrides = []): array
{
    static $messageIdCounter = 0;
    $messageId = 'ACT_'.now()->timestamp.'_'.(++$messageIdCounter);

    return array_merge([
        'event' => 'payment.received',
        'data' => [
            'message_action' => 'SUCCESS',
            'message_code' => '0',
            'message_data' => [
                'totals' => [
                    'grandTotal' => $encodedAmount,
                    'totalPrice' => $encodedAmount,
                    'totalAddon' => 0,
                    'convenienceFee' => 0,
                ],
                'customer' => array_merge([
                    'email' => 'buyer@example.com',
                    'name' => 'John Buyer',
                    'phone' => '08123456789',
                ], $customer),
                'createdAt' => now()->toIso8601String(),
            ],
            'message_id' => $messageId,
        ],
    ], $overrides);
}

function lynkPost($test, string $url, array $payload): TestResponse
{
    $messageData = $payload['data']['message_data'] ?? [];
    $grandTotal = (string) ($messageData['totals']['grandTotal'] ?? '');
    $refId = $messageData['refId'] ?? '';
    $messageId = $payload['data']['message_id'] ?? '';
    $signature = lynkSignature($grandTotal, $refId, $messageId);

    return $test->withHeaders(['X-Lynk-Signature' => $signature])
        ->postJson($url, $payload);
}

function lynkActivityPost($test, string $url, array $payload): TestResponse
{
    $messageData = $payload['data']['message_data'] ?? [];
    $amount = (int) ($messageData['totals']['grandTotal'] ?? 0);
    $messageId = $payload['data']['message_id'] ?? '';
    $signature = lynkSignatureActivity($amount, $messageId);

    return $test->withHeaders(['X-Lynk-Signature' => $signature])
        ->postJson($url, $payload);
}

beforeEach(function () {
    config(['linkid.merchant_key' => LYNK_TEST_KEY]);

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

// ── Payment success ───────────────────────────────────────────────────────

test('lynk webhook membuat payment record saat payment.received + SUCCESS', function () {
    lynkPost($this, '/api/v1/payments/linkid/webhook', lynkPayload($this->order))
        ->assertOk();

    expect(Payment::where('order_id', $this->order->id)->exists())->toBeTrue();
});

test('lynk webhook update order ke Paid', function () {
    lynkPost($this, '/api/v1/payments/linkid/webhook', lynkPayload($this->order))
        ->assertOk();

    $this->order->refresh();
    expect($this->order->status)->toBe(OrderStatus::Paid);
});

test('lynk webhook aktifkan enrollment course saat order course', function () {
    lynkPost($this, '/api/v1/payments/linkid/webhook', lynkPayload($this->order))
        ->assertOk();

    expect(Enrollment::where('user_id', $this->user->id)
        ->where('course_id', $this->course->id)->exists())->toBeTrue();
});

test('lynk webhook tidak duplicate payment record (idempotent)', function () {
    lynkPost($this, '/api/v1/payments/linkid/webhook', lynkPayload($this->order, [], true))->assertOk();
    lynkPost($this, '/api/v1/payments/linkid/webhook', lynkPayload($this->order, [], true))->assertOk();

    expect(Payment::where('order_id', $this->order->id)->count())->toBe(1);
});

test('lynk webhook set paid_at', function () {
    lynkPost($this, '/api/v1/payments/linkid/webhook', lynkPayload($this->order))
        ->assertOk();

    $payment = Payment::where('order_id', $this->order->id)->first();
    expect($payment->paid_at)->not->toBeNull();
});

// ── Event & action filtering ──────────────────────────────────────────────

test('lynk webhook ignore event selain payment.received', function () {
    lynkPost($this, '/api/v1/payments/linkid/webhook', lynkPayload($this->order, [
        'event' => 'payment.pending',
    ]))->assertOk();

    // Order tidak berubah karena event diabaikan
    $this->order->refresh();
    expect($this->order->status)->toBe(OrderStatus::Pending);
});

test('lynk webhook ignore message_action selain SUCCESS', function () {
    lynkPost($this, '/api/v1/payments/linkid/webhook', lynkPayload($this->order, [
        'data' => array_merge(lynkPayload($this->order)['data'], ['message_action' => 'PENDING']),
    ]))->assertOk();

    $this->order->refresh();
    expect($this->order->status)->toBe(OrderStatus::Pending);
});

// ── Validasi ─────────────────────────────────────────────────────────────

test('lynk webhook tolak signature salah', function () {
    $this->withHeaders(['X-Lynk-Signature' => 'wrong-signature'])
        ->postJson('/api/v1/payments/linkid/webhook', lynkPayload($this->order))
        ->assertUnauthorized();
});

test('lynk webhook tolak jika merchant key tidak diset', function () {
    config(['linkid.merchant_key' => null]);

    $this->withHeaders(['X-Lynk-Signature' => 'apapun'])
        ->postJson('/api/v1/payments/linkid/webhook', lynkPayload($this->order))
        ->assertStatus(500);
});

test('lynk webhook tolak tanpa signature header', function () {
    $this->postJson('/api/v1/payments/linkid/webhook', lynkPayload($this->order))
        ->assertUnauthorized();
});

test('lynk webhook return 404 jika order tidak ditemukan', function () {
    $payload = lynkPayload($this->order);
    $payload['data']['message_data']['refId'] = 'NONEXISTENT-INV-999';

    lynkPost($this, '/api/v1/payments/linkid/webhook', $payload)
        ->assertNotFound();
});

test('lynk webhook return 422 jika missing refId', function () {
    $payload = lynkPayload($this->order);
    unset($payload['data']['message_data']['refId']);

    lynkPost($this, '/api/v1/payments/linkid/webhook', $payload)
        ->assertStatus(422);
});

// ── Booking orders ────────────────────────────────────────────────────────

test('lynk webhook confirm booking saat order adalah booking', function () {
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

    lynkPost($this, '/api/v1/payments/linkid/webhook', lynkPayload($order))->assertOk();

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Confirmed);
});

// ── Activity registrations ───────────────────────────────────────────────

test('lynk webhook buat activity registration saat encoded amount match', function () {
    $activity = Activity::factory()->create([
        'price' => 50000,
        'registration_code' => 3,
    ]);
    $encodedAmount = $activity->encodedAmount(); // 5000003

    lynkActivityPost($this, '/api/v1/payments/linkid/webhook', lynkActivityPayload(3, $encodedAmount))
        ->assertOk();

    expect(ActivityRegistration::where('activity_id', $activity->id)->exists())->toBeTrue();
});

test('lynk webhook buat user baru jika email belum ada', function () {
    $activity = Activity::factory()->create([
        'price' => 75000,
        'registration_code' => 12,
    ]);
    $encodedAmount = $activity->encodedAmount(); // 7500012

    lynkActivityPost($this, '/api/v1/payments/linkid/webhook', lynkActivityPayload(12, $encodedAmount, [
        'email' => 'newbuyer@mail.com',
        'name' => 'New Buyer',
    ]))->assertOk();

    $user = User::where('email', 'newbuyer@mail.com')->first();
    expect($user)->not->toBeNull();

    $registration = ActivityRegistration::where('activity_id', $activity->id)->first();
    expect($registration->user_id)->toBe($user->id);
});

test('lynk webhook reuse user existing saat email sudah ada', function () {
    $existingUser = User::factory()->create(['email' => 'reuse-test@mail.com']);
    $activity = Activity::factory()->create([
        'price' => 30000,
        'registration_code' => 5,
    ]);
    $encodedAmount = $activity->encodedAmount();

    lynkActivityPost($this, '/api/v1/payments/linkid/webhook', lynkActivityPayload(5, $encodedAmount, [
        'email' => 'reuse-test@mail.com',
        'name' => 'Existing User',
    ]))->assertOk();

    $registration = ActivityRegistration::where('activity_id', $activity->id)->first();
    expect($registration->user_id)->toBe($existingUser->id);

    // Tidak buat user baru (hanya $existingUser + user dari beforeEach)
    expect(User::where('email', 'reuse-test@mail.com')->count())->toBe(1);
});

test('lynk webhook tolak duplicate registration', function () {
    $activity = Activity::factory()->create([
        'price' => 100000,
        'registration_code' => 7,
    ]);
    $encodedAmount = $activity->encodedAmount();

    lynkActivityPost($this, '/api/v1/payments/linkid/webhook', lynkActivityPayload(7, $encodedAmount, [
        'email' => 'repeat@mail.com',
    ]))->assertOk();

    // Kirim lagi dengan email sama
    lynkActivityPost($this, '/api/v1/payments/linkid/webhook', lynkActivityPayload(7, $encodedAmount, [
        'email' => 'repeat@mail.com',
    ]))->assertOk(); // Tidak error, tapi tetap 1 registration

    expect(ActivityRegistration::where('activity_id', $activity->id)->count())->toBe(1);
});

test('lynk webhook return 404 saat activity tidak ditemukan', function () {
    lynkActivityPost($this, '/api/v1/payments/linkid/webhook', lynkActivityPayload(99, 999999, [
        'email' => 'nobody@mail.com',
    ]))->assertNotFound();
});

test('lynk webhook return 422 saat missing grandTotal', function () {
    lynkActivityPost($this, '/api/v1/payments/linkid/webhook', lynkActivityPayload(3, 0))->assertStatus(422);
});

test('lynk webhook return 422 saat missing customer email', function () {
    $activity = Activity::factory()->create([
        'price' => 50000,
        'registration_code' => 3,
    ]);
    $encodedAmount = $activity->encodedAmount();

    lynkActivityPost($this, '/api/v1/payments/linkid/webhook', lynkActivityPayload(3, $encodedAmount, [
        'email' => null,
        'name' => 'No Email User',
    ]))->assertStatus(422);
});

test('lynk webhook idempotent untuk activity registration', function () {
    $activity = Activity::factory()->create([
        'price' => 60000,
        'registration_code' => 8,
    ]);
    $encodedAmount = $activity->encodedAmount();
    $payload = lynkActivityPayload(8, $encodedAmount);

    lynkActivityPost($this, '/api/v1/payments/linkid/webhook', $payload)->assertOk();
    lynkActivityPost($this, '/api/v1/payments/linkid/webhook', $payload)->assertOk();

    expect(ActivityRegistration::where('activity_id', $activity->id)->count())->toBe(1);
});

test('activity encodedAmount dan decodeRegistrationCode roundtrip', function () {
    $activity = Activity::factory()->create([
        'price' => 50000,
        'registration_code' => 32,
    ]);

    $encoded = $activity->encodedAmount();
    expect($encoded)->toBe(5000032);

    $decoded = Activity::decodeRegistrationCode($encoded);
    expect($decoded)->toBe(32);
});

test('activity encodedAmount nol jika tidak ada price atau code', function () {
    $activity = Activity::factory()->create([
        'price' => null,
        'registration_code' => null,
    ]);

    expect($activity->encodedAmount())->toBe(0);
});
