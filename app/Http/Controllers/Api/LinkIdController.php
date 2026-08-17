<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Services\LinkIdService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LinkIdController extends Controller
{
    public function __construct(
        private LinkIdService $linkIdService,
        private OrderService $orderService,
    ) {}

    public function createPaymentLink(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = $request->user();
        $course = Course::findOrFail($request->course_id);

        if ($user->isEnrolledIn($course)) {
            return response()->json([
                'message' => 'Kamu sudah terdaftar di kelas ini.',
            ], 422);
        }

        $order = $this->orderService->createForCourse($user, $course);

        try {
            $paymentLink = $this->linkIdService->createPaymentLink($order);

            Payment::updateOrCreate(
                ['linkid_transaction_id' => $paymentLink['id'] ?? null],
                [
                    'order_id' => $order->id,
                    'payment_type' => 'linkid',
                    'amount' => $order->final_amount,
                    'status' => PaymentStatus::Pending,
                ]
            );

            return response()->json([
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'amount' => (int) $order->final_amount,
                'payment_link' => $paymentLink['payment_url']
                    ?? $paymentLink['checkout_url']
                    ?? $paymentLink['url']
                    ?? null,
                'linkid_payment_id' => $paymentLink['id'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('LinkId create payment link failed: '.$e->getMessage());

            return response()->json([
                'message' => 'Gagal membuat payment link. Coba lagi.',
            ], 500);
        }
    }

    public function getStatus(Request $request, string $externalId): JsonResponse
    {
        $order = Order::where('invoice_number', $externalId)
            // ->where('user_id', $request->user()->id)
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $gatewayStatus = $this->linkIdService->getPaymentLinkStatus($externalId);

        return response()->json([
            'order_id' => $order->id,
            'invoice_number' => $order->invoice_number,
            'status' => $order->status->value,
            'final_amount' => (int) $order->final_amount,
            'gateway_status' => $gatewayStatus,
        ]);
    }
}
