<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\TutorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    public function index(): Response
    {
        $schedules = TutorSchedule::with('tutor')
            ->withCount('bookings')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(20);

        return Inertia::render('admin/schedules/Index', [
            'schedules' => $schedules,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/schedules/Form', [
            'schedule'   => null,
            'companions' => $this->getCompanions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tutor_id'         => 'required|exists:users,id',
            'day_of_week'      => 'required|integer|between:0,6',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'session_duration' => 'required|integer|min:15',
            'break_time'       => 'required|integer|min:0',
            'price'            => 'required|numeric|min:0',
            'max_participants' => 'required|integer|min:1|max:10',
            'is_active'        => 'boolean',
        ]);

        TutorSchedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dibuat.');
    }

    public function edit(TutorSchedule $schedule): Response
    {
        return Inertia::render('admin/schedules/Form', [
            'schedule'   => $schedule->load('tutor'),
            'companions' => $this->getCompanions(),
        ]);
    }

    public function update(Request $request, TutorSchedule $schedule)
    {
        $validated = $request->validate([
            'tutor_id'         => 'required|exists:users,id',
            'day_of_week'      => 'required|integer|between:0,6',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'session_duration' => 'required|integer|min:15',
            'break_time'       => 'required|integer|min:0',
            'price'            => 'required|numeric|min:0',
            'max_participants' => 'required|integer|min:1|max:10',
            'is_active'        => 'boolean',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil diupdate.');
    }

    public function destroy(TutorSchedule $schedule)
    {
        if ($schedule->bookings()->count() > 0) {
            return back()->with('error', 'Jadwal tidak bisa dihapus karena sudah ada booking.');
        }

        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    private function getCompanions()
    {
        return User::where('role', Roles::Companion->value)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);
    }
}
