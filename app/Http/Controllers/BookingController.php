<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Models\Booking;
use App\Models\Order;
use App\Models\TutorSchedule;
use App\Services\MidtransService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(
        private MidtransService $midtransService,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:tutor_schedules,id',
            'start_at'    => 'required|date|after:now',
            'notes'       => 'nullable|string|max:500',
        ]);

        $schedule = TutorSchedule::with('tutor')->findOrFail($validated['schedule_id']);

        // Cek slot masih tersedia
        $isAvailable = $schedule->isAvailableOn(
            date('Y-m-d', strtotime($validated['start_at']))
        );

        if (!$isAvailable) {
            return response()->json([
                'message' => 'Slot waktu ini sudah penuh. Pilih jadwal lain.',
            ], 422);
        }

        // Hitung end_at
        $startAt = \Carbon\Carbon::parse($validated['start_at']);
        $endAt   = $startAt->copy()->addMinutes($schedule->session_duration);

        // Buat booking
        $booking = Booking::create([
            'student_id'       => $request->user()->id,
            'tutor_id'         => $schedule->tutor_id,
            'tutor_schedule_id'=> $schedule->id,
            'start_at'         => $startAt,
            'end_at'           => $endAt,
            'type'             => 'online',
            'status'           => BookingStatus::Pending->value,
            'notes'            => $validated['notes'] ?? null,
        ]);

        // Buat order untuk booking
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $order = Order::create([
            'user_id'        => $request->user()->id,
            'orderable_type' => Booking::class,
            'orderable_id'   => $booking->id,
            'item_name'      => "Sesi dengan {$schedule->tutor->full_name}",
            'original_price' => $schedule->price,
            'discount_amount'=> 0,
            'final_amount'   => $schedule->price,
            'status'         => OrderStatus::Pending->value,
            'expired_at'     => now()->addHour(),
            'invoice_number' => $invoiceNumber,
        ]);

        // Kalau gratis
        if ($schedule->price == 0) {
            $booking->update(['status' => BookingStatus::Confirmed->value]);
            $order->update(['status' => OrderStatus::Paid->value]);

            return response()->json([
                'free'     => true,
                'redirect' => "/bookings/{$booking->id}",
            ]);
        }

        // Ambil snap token
        try {
            $snapToken = $this->midtransService->createSnapToken($order);

            return response()->json([
                'snap_token' => $snapToken,
                'client_key' => config('midtrans.client_key'),
                'order_id'   => $order->id,
                'invoice'    => $order->invoice_number,
                'amount'     => $order->final_amount,
            ]);
        } catch (\Exception $e) {
            $booking->delete();
            $order->delete();

            return response()->json([
                'message' => 'Gagal memproses pembayaran. Coba lagi.',
            ], 500);
        }
    }

    public function show(Booking $booking): Response
    {
        abort_if($booking->student_id !== auth()->id(), 403);

        $booking->load(['tutor', 'schedule']);

        $order = Order::where('orderable_type', Booking::class)
            ->where('orderable_id', $booking->id)
            ->with('payments')
            ->first();

        return Inertia::render('bookings/Show', [
            'booking' => $booking,
            'order'   => $order,
        ]);
    }
}
