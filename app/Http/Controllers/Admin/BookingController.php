<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(): Response
    {
        $bookings = Booking::with(['student', 'tutor', 'schedule'])
            ->latest()
            ->paginate(5);

        return Inertia::render('admin/bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function generateMeet(Request $request, Booking $booking)
    {
        $request->validate([
            'meeting_link' => 'required|url',
        ]);

        $booking->update([
            'meeting_link' => $request->meeting_link,
        ]);

        // TODO: Kirim notifikasi ke student & tutor dengan zoom link
        return back()->with('success', 'Meeting link berhasil disimpan dan dikirim ke peserta.');
    }
}
