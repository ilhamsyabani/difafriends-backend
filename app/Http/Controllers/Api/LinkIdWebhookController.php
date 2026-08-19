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
        $providedSignature = $request->header('X-Lynk-Signature', '');

        $data = $payload['data'] ?? [];
        $messageData = $data['message_data'] ?? [];
        $refId = $messageData['refId'] ?? null;
        $amount = (int) ($messageData['totals']['grandTotal'] ?? 0);
        $messageId = $data['message_id'] ?? '';
        $transactionId = $messageId;

        // Try Activity registration first (price * 100 + registration_code)
        // Skip refId signature validation for activity payments
        $activityCode = Activity::decodeRegistrationCode($amount);
        if ($activityCode) {
            return $this->handleActivityRegistration(
                $amount,
                $activityCode,
                $transactionId,
                $payload,
                $messageData
            );
        }

        // Order-based signature: requires refId
        $signatureString = (string) $amount.$refId.$messageId.$merchantKey;
        $expectedSignature = hash('sha256', $signatureString);

        if (! hash_equals($expectedSignature, $providedSignature)) {
            Log::warning('Lynk webhook: invalid signature', [
                'expected' => substr($expectedSignature, 0, 8).'...',
                'provided' => substr($providedSignature, 0, 8).'...',
            ]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $event = $payload['event'] ?? null;
        $messageAction = $data['message_action'] ?? null;

        if ($event !== 'payment.received') {
            return response()->json(['message' => 'Event ignored']);
        }

        if ($messageAction !== 'SUCCESS') {
            return response()->json(['message' => 'Payment not successful']);
        }

        if (! $refId) {
            Log::warning('Lynk webhook: missing refId', $payload);

            return response()->json(['message' => 'Missing refId'], 422);
        }

        $order = Order::where('invoice_number', $refId)->first();

        if (! $order) {
            Log::warning("Lynk webhook: order not found for refId={$refId}");

            return response()->json(['message' => 'Order not found'], 404);
        }

        // Idempotent
        $existingPayment = Payment::where('linkid_transaction_id', $transactionId)->first();
        if ($existingPayment) {
            Log::info("Lynk webhook: payment already processed transactionId={$transactionId}");

            return response()->json(['message' => 'OK (already processed)']);
        }

        $totals = $messageData['totals'] ?? [];

        Payment::updateOrCreate(
            ['linkid_transaction_id' => $transactionId],
            [
                'order_id' => $order->id,
                'payment_type' => 'lynk',
                'amount' => $totals['grandTotal'] ?? $order->final_amount,
                'status' => PaymentStatus::Settlement,
                'linkid_response' => $payload,
                'paid_at' => now(),
            ]
        );

        $this->handleSuccess($order);

        Log::info("Lynk webhook: processed refId={$refId} grandTotal={$totals['grandTotal']}");

        return response()->json(['message' => 'OK']);
    }

    private function handleActivityRegistration(
        int $amount,
        int $activityCode,
        ?string $transactionId,
        array $payload,
        array $messageData
    ): JsonResponse {
        $activity = Activity::where('registration_code', $activityCode)->first();

        if (! $activity) {
            Log::warning("Lynk webhook: activity not found for code={$activityCode}");

            return response()->json(['message' => 'Activity not found'], 404);
        }

        $customer = $messageData['customer'] ?? [];
        $email = $customer['email'] ?? null;

        if (! $email) {
            Log::warning('Lynk webhook: missing customer email for activity registration');

            return response()->json(['message' => 'Missing customer email'], 422);
        }

        $user = $this->findOrCreateUser($email, $customer);

        $registration = ActivityRegistration::where('user_id', $user->id)
            ->where('activity_id', $activity->id)
            ->first();

        if ($registration) {
            Log::info("Lynk webhook: registration already exists user_id={$user->id} activity_id={$activity->id}");

            if (! $registration->linkid_transaction_id) {
                $registration->update([
                    'linkid_transaction_id' => $transactionId,
                    'linkid_response' => $payload,
                ]);
            }

            return response()->json(['message' => 'OK (already registered)']);
        }

        ActivityRegistration::create([
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'linkid_transaction_id' => $transactionId,
            'linkid_response' => $payload,
            'registered_at' => now(),
        ]);

        Log::info("Lynk webhook: activity registration created user_id={$user->id} activity_id={$activity->id}");

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
        if ($order->status === OrderStatus::Paid) {
            return;
        }

        $order->update(['status' => OrderStatus::Paid]);

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
}
