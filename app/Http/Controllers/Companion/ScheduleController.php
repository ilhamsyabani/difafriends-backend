<?php

namespace App\Http\Controllers\Companion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\TutorSchedule;
use Inertia\Response;
use Inertia\Inertia;


class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $schedules = TutorSchedule::where('tutor_id', $request->user()->id)
            ->withCount('bookings')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return Inertia::render('companion/schedules/Index', [
            'schedules' => $schedules,
        ]);
    }

    public function create()
    {
        return Inertia::render('companion/schedules/Form', [
            'schedule' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week'      => 'required|integer|between:0,6',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'session_duration' => 'required|integer|min:15',
            'break_time'       => 'required|integer|min:0',
            'price'            => 'required|numeric|min:0',
            'max_participants' => 'required|integer|min:1|max:10',
            'is_active'        => 'boolean',
        ]);

        $validated['tutor_id'] = $request->user()->id;
        TutorSchedule::create($validated);

        return redirect()->route('companion.schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Request $request, TutorSchedule $schedule)
    {
        abort_if($schedule->tutor_id !== $request->user()->id, 403);

        return Inertia::render('companion/schedules/Form', [
            'schedule' => $schedule,
        ]);
    }

    public function update(Request $request, TutorSchedule $schedule)
    {
        abort_if($schedule->tutor_id !== $request->user()->id, 403);

        $validated = $request->validate([
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

        return redirect()->route('companion.schedules.index')
            ->with('success', 'Jadwal berhasil diupdate.');
         }

    public function destroy(Request $request, TutorSchedule $schedule)
    {
        abort_if($schedule->tutor_id !== $request->user()->id, 403);

        $schedule->delete();

        return redirect()->route('companion.schedules.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    public function toggleStatus(Request $request, TutorSchedule $schedule)
    {
        abort_if($schedule->tutor_id !== $request->user()->id, 403);

        $schedule->update([
            'is_active' => !$schedule->is_active,
        ]);

        return redirect()->route('companion.schedules.index')
            ->with('success', 'Status jadwal berhasil diubah.');
    }
}
