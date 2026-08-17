<?php

namespace App\Http\Controllers\Api;

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\OrderPaidNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LinkIdWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        Log::info('LinkId webhook: received request', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ]);

        // ── 1. Verifikasi signature ─────────────────────────────────────────────
        // $secret = config('linkid.webhook_secret');

        // if (empty($secret)) {
        //     Log::warning('LinkId webhook: webhook secret not configured');

        //     return response()->json(['message' => 'Server misconfigured'], 500);
        // }

        // $providedSignature = $request->header('X-Linkid-Signature', '');
        $payload = $request->all();

        // // NOTE: Sesuaikan nama field dengan dokumentasi Link.id yang sebenarnya.
        // // Signature method di bawah asumsi: HMAC SHA256 dari raw JSON body.
        // $expectedSignature = hash_hmac('sha256', json_encode($payload), $secret);

        // if (! hash_equals($expectedSignature, $providedSignature)) {
        //     Log::warning('LinkId webhook: invalid signature', [
        //         'provided' => substr($providedSignature, 0, 8).'...',
        //         'expected' => substr($expectedSignature, 0, 8).'...',
        //     ]);

        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }

        // ── 2. Parse payload ────────────────────────────────────────────────────
        // NOTE: Sesuaikan nama field dengan dokumentasi Link.id yang sebenarnya.
        $externalId = $payload['external_id'] ?? $payload['order_id'] ?? null;
        $transactionId = $payload['transaction_id'] ?? $payload['id'] ?? null;
        $status = $payload['status'] ?? $payload['payment_status'] ?? null;
        $amount = $payload['amount'] ?? $payload['gross_amount'] ?? null;
        $paymentType = $payload['payment_type'] ?? $payload['payment_method'] ?? null;

        if (! $externalId) {
            Log::warning('LinkId webhook: missing external_id', $payload);

            return response()->json(['message' => 'Missing external_id'], 422);
        }

        if (! $status) {
            Log::warning('LinkId webhook: missing status', $payload);

            return response()->json(['message' => 'Missing status'], 422);
        }

        // ── 3. Cari order ──────────────────────────────────────────────────────
        $order = Order::where('invoice_number', $externalId)->first();

        if (! $order) {
            Log::warning("LinkId webhook: order not found for external_id={$externalId}");

            return response()->json(['message' => 'Order not found'], 404);
        }

        // ── 4. Update / buat payment record ────────────────────────────────────
        $linkidResponse = $payload['response'] ?? $payload;

        Payment::updateOrCreate(
            ['linkid_transaction_id' => $transactionId],
            [
                'order_id' => $order->id,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'status' => $this->mapStatus($status),
                'linkid_response' => $linkidResponse,
                'paid_at' => $this->isSuccessStatus($status) ? now() : null,
            ]
        );

        // ── 5. Handle status ───────────────────────────────────────────────────
        $this->handleStatus($order, $status);

        Log::info("LinkId webhook: processed external_id={$externalId} status={$status}");

        return response()->json(['message' => 'OK']);
    }

    private function isSuccessStatus(string $status): bool
    {
        return in_array(strtolower($status), ['paid', 'settlement', 'success', 'completed'], true);
    }

    private function isExpiredStatus(string $status): bool
    {
        return in_array(strtolower($status), ['expired', 'expire'], true);
    }

    private function isFailedStatus(string $status): bool
    {
        return in_array(strtolower($status), ['failed', 'cancel', 'cancelled', 'denied', 'reject'], true);
    }

    private function mapStatus(string $status): string
    {
        return match (strtolower($status)) {
            'paid', 'settlement', 'success', 'completed' => 'settlement',
            'pending', 'waiting' => 'pending',
            'expired', 'expire' => 'expire',
            'failed', 'denied', 'reject' => 'deny',
            'cancelled', 'cancel' => 'cancel',
            default => 'pending',
        };
    }

    private function handleStatus(Order $order, string $status): void
    {
        if ($this->isSuccessStatus($status)) {
            $this->handleSuccess($order);
        } elseif ($this->isExpiredStatus($status)) {
            $this->handleExpired($order);
        } elseif ($this->isFailedStatus($status)) {
            $this->handleFailed($order);
        }
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

    private function handleExpired(Order $order): void
    {
        $order->update(['status' => OrderStatus::Expired]);
    }

    private function handleFailed(Order $order): void
    {
        $order->update(['status' => OrderStatus::Cancelled]);
        Log::warning("LinkId webhook: payment failed on order: {$order->invoice_number}");
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
