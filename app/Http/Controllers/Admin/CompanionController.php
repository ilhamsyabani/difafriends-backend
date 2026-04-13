<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\TutorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanionController extends Controller
{
    public function index(): Response
    {
        $companions = User::where('role', Roles::Companion->value)
            ->withCount(['bookingsAsTutor', 'schedules'])
            ->with('schedules')
            ->paginate(5);

        return Inertia::render('admin/companions/Index', [
            'companions' => $companions,
        ]);
    }

    public function schedules(User $user): Response
    {
        abort_if($user->role !== Roles::Companion, 404);

        $schedules = $user->schedules()
            ->withCount('bookings')
            ->orderBy('day_of_week')
            ->get();

        return Inertia::render('admin/companions/Schedules', [
            'companion' => $user,
            'schedules' => $schedules,
        ]);
    }

    public function updateSchedule(Request $request, TutorSchedule $schedule)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'session_duration' => 'required|integer|min:15',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $schedule->update($validated);

        return back()->with('success', 'Jadwal berhasil diupdate.');
    }
}
