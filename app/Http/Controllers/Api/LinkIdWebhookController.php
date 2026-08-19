<?php

namespace App\Http\Controllers\Api;

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\Booking;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\OrderPaidNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LinkIdWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $merchantKey = config('linkid.merchant_key');
        if (empty($merchantKey)) {
            Log::warning('Lynk webhook: merchant key not configured');

            return response()->json(['message' => 'Server misconfigured'], 500);
        }


        $payload = $request->all();
        Log::info('Lynk webhook: received', ['payload' => $payload]);
        
        $providedSignature = $request->header('X-Lynk-Signature', '');

        $data = $payload['data'] ?? [];
        $messageData = $data['message_data'] ?? [];
        $refId = $messageData['refId'] ?? null;
        $grandTotal = (int) ($messageData['totals']['grandTotal'] ?? 0);
        $transactionId = $data['message_id'] ?? '';
        $item = $messageData['items'][0] ?? [];

        // Signature tetap wajib, tidak peduli jalur activity atau order biasa
        $signatureString = (string) $grandTotal.$refId.$transactionId.$merchantKey;
        $expectedSignature = hash('sha256', $signatureString);

        if (! hash_equals($expectedSignature, $providedSignature)) {
            Log::warning('Lynk webhook: invalid signature', [
                'expected' => substr($expectedSignature, 0, 8).'...',
                'provided' => substr($providedSignature, 0, 8).'...',
            ]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (($payload['event'] ?? null) !== 'payment.received') {
            return response()->json(['message' => 'Event ignored']);
        }
        if (($data['message_action'] ?? null) !== 'SUCCESS') {
            return response()->json(['message' => 'Payment not successful']);
        }

        // Coba resolve sebagai activity dulu (uuid)
        $activity = $this->resolveActivity($item);
        if ($activity) {
            return $this->handleActivityRegistration($grandTotal, $activity, $transactionId, $payload, $messageData);
        }

        // Bukan activity → treat sebagai order biasa (kelas/booking via refId)
        if (! $refId) {
            Log::warning('Lynk webhook: missing refId and no matching activity', $payload);

            return response()->json(['message' => 'Order not found'], 404);
        }

        $order = Order::where('invoice_number', $refId)->first();
        if (! $order) {
            Log::warning("Lynk webhook: order not found for refId={$refId}");

            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->status === OrderStatus::Paid) {
            return response()->json(['message' => 'OK (order already paid)']);
        }

        if (Payment::where('linkid_transaction_id', $transactionId)->exists()) {
            return response()->json(['message' => 'OK (already processed)']);
        }

        Payment::create([
            'order_id' => $order->id,
            'linkid_transaction_id' => $transactionId,
            'payment_type' => 'lynk',
            'amount' => $grandTotal,
            'status' => PaymentStatus::Settlement,
            'linkid_response' => $payload,
            'paid_at' => now(),
        ]);

        $order->update(['status' => OrderStatus::Paid]);
        $this->handleSuccess($order);

        Log::info("Lynk webhook: processed refId={$refId} grandTotal={$grandTotal}");

        return response()->json(['message' => 'OK']);
    }

    private function handleActivityRegistration(
        int $grandTotal,
        Activity $activity,
        ?string $transactionId,
        array $payload,
        array $messageData
    ): JsonResponse {
        $customer = $messageData['customer'] ?? [];
        $email = $customer['email'] ?? null;

        if (! $email) {
            Log::warning('Lynk webhook: missing customer email for activity registration');

            return response()->json(['message' => 'Missing customer email'], 422);
        }

        // Idempotent check di awal, sebelum efek samping apapun
        $existing = ActivityRegistration::where('linkid_transaction_id', $transactionId)->first();
        if ($existing) {
            Log::info("Lynk webhook: activity registration already processed transactionId={$transactionId}");

            return response()->json(['message' => 'OK (already processed)']);
        }

        $user = $this->findOrCreateUser($email, $customer);

        $order = Order::create([
            'user_id' => $user->id,
            'orderable_type' => Activity::class,
            'orderable_id' => $activity->id,
            'item_name' => $activity->name,
            'original_price' => $grandTotal,
            'discount_amount' => 0,
            'final_amount' => $grandTotal,
            'status' => OrderStatus::Paid,
            'invoice_number' => 'ACT-'.date('Ymd').'-'.strtoupper(Str::random(6)),
        ]);

        $registration = ActivityRegistration::create([
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'order_id' => $order->id,
            'linkid_transaction_id' => $transactionId,
            'linkid_response' => $payload,
            'registered_at' => now(),
        ]);

        Payment::create([
            'order_id' => $order->id,
            'linkid_transaction_id' => $transactionId,
            'payment_type' => 'lynk',
            'amount' => $grandTotal, // 0 tetap dicatat, bukan di-skip
            'status' => PaymentStatus::Settlement,
            'linkid_response' => $payload,
            'paid_at' => now(),
        ]);

        Log::info("Lynk webhook: activity registration created user_id={$user->id} activity_id={$activity->id} amount={$grandTotal}");

        return response()->json(['message' => 'OK']);
    }

    private function findOrCreateUser(string $email, array $customer): User
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            return $user;
        }

        $name = trim($customer['name'] ?? '');
        $parts = preg_split('/\s+/', $name, 2);
        $firstName = $parts[0] ?? 'Guest';
        $lastName = $parts[1] ?? '';

        $user = User::create([
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $customer['phone'] ?? null,
            'role' => 'student',
        ]);

        Log::info("Lynk webhook: created new user user_id={$user->id} email={$email}");

        return $user;
    }

    private function handleSuccess(Order $order): void
    {
        if ($order->orderable_type === Course::class) {
            $this->activateEnrollment($order);
            $order->user->notify(new OrderPaidNotification($order));
        } elseif ($order->orderable_type === Booking::class) {
            $this->confirmBooking($order);
        }
    }

    private function activateEnrollment(Order $order): void
    {
        Enrollment::firstOrCreate(
            [
                'user_id' => $order->user_id,
                'course_id' => $order->orderable_id,
            ],
            [
                'order_id' => $order->id,
                'status' => 'active',
                'enrolled_at' => now(),
            ]
        );
    }

    private function confirmBooking(Order $order): void
    {
        $booking = Booking::find($order->orderable_id);

        if (! $booking) {
            return;
        }

        $booking->update([
            'status' => BookingStatus::Confirmed,
            'confirmed_at' => now(),
        ]);

        $booking->student->notify(new BookingConfirmedNotification($booking));
    }

    private function resolveActivity(array $item): ?Activity
    {
        if (! empty($item['uuid'])) {
            return Activity::where('linkid_product_uuid', $item['uuid'])->first();
        }

        return null;
    }
}
