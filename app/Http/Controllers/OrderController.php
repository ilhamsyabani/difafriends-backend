<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Models\Booking;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\OrderPaidNotification;
use App\Services\MidtransService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private MidtransService $midtransService,
    ) {}

    // Buat order & ambil snap token
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $user = $request->user();

        // Cek sudah enroll
        if ($user->isEnrolledIn($course)) {
            return response()->json([
                'message' => 'Kamu sudah terdaftar di kelas ini.',
            ], 422);
        }

        $order = $this->orderService->createForCourse($user, $course);

        // Kalau gratis — langsung aktifkan
        if ($order->final_amount == 0) {
            $this->activateEnrollment($order);

            return response()->json([
                'free' => true,
                'redirect' => "/courses/{$course->slug}",
            ]);
        }

        // Ambil snap token dari Midtrans
        try {
            $snapToken = $this->midtransService->createSnapToken($order);

            return response()->json([
                'snap_token' => $snapToken,
                'client_key' => config('midtrans.client_key'),
                'order_id' => $order->id,
                'invoice' => $order->invoice_number,
                'amount' => $order->final_amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans error: '.$e->getMessage());

            return response()->json([
                'message' => 'Gagal menghubungi payment gateway. Coba lagi.',
            ], 500);
        }
    }

    // Halaman riwayat order user
    public function index(Request $request): Response
    {
        $orders = Order::with(['orderable'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return Inertia::render('orders/Index', [
            'orders' => $orders,
        ]);
    }

    // Webhook dari Midtrans
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        // Verifikasi signature
        $serverKey = config('midtrans.server_key');
        $signatureKey = hash('sha512',
            $payload['order_id'].
            $payload['status_code'].
            $payload['gross_amount'].
            $serverKey
        );

        if ($signatureKey !== $payload['signature_key']) {
            Log::warning('Midtrans webhook: invalid signature');

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('invoice_number', $payload['order_id'])->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Simpan payment record
        Payment::updateOrCreate(
            ['midtrans_transaction_id' => $payload['transaction_id']],
            [
                'order_id' => $order->id,
                'payment_type' => $payload['payment_type'] ?? null,
                'amount' => $payload['gross_amount'],
                'status' => $payload['transaction_status'],
                'midtrans_response' => $payload,
                'paid_at' => $payload['transaction_status'] === 'settlement'
                    ? now() : null,
            ]
        );

        // Handle status
        match ($payload['transaction_status']) {
            'settlement', 'capture' => $this->handleSuccess($order),
            'expire', 'cancel' => $this->handleExpired($order),
            'fraud' => $this->handleFraud($order),
            default => null,
        };

        return response()->json(['message' => 'OK']);
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

    private function handleFraud(Order $order): void
    {
        $order->update(['status' => OrderStatus::Cancelled]);
        Log::warning("Fraud detected on order: {$order->invoice_number}");
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
