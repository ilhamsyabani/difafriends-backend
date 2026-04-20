<?php

namespace App\Http\Controllers\Companion;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->get('status');

        $bookings = Booking::with(['student', 'schedule'])
            ->where('tutor_id', $request->user()->id)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('start_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('companion/bookings/Index', [
            'bookings' => $bookings,
            'filters' => ['status' => $status],
        ]);
    }
}
