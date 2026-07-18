<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(): Response
    {
        $activities = Activity::withCount('attendanceForms')
            ->latest()
            ->paginate(10);

        return Inertia::render('admin/activities/Index', [
            'activities' => $activities,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/activities/Form', [
            'activity' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $validated['created_by'] = $request->user()->id;

        Activity::create($validated);

        return redirect()->route('admin.activities.index')
            ->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Activity $activity): Response
    {
        $activity->load([
            'attendanceForms.sessions' => function ($query) {
                $query->withCount('attendances')->orderBy('session_date');
            },
        ]);

        return Inertia::render('admin/activities/Show', [
            'activity' => $activity,
        ]);
    }

    public function edit(Activity $activity): Response
    {
        return Inertia::render('admin/activities/Form', [
            'activity' => $activity,
        ]);
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $this->validateData($request);

        $activity->update($validated);

        return redirect()->route('admin.activities.show', $activity)
            ->with('success', 'Kegiatan berhasil diupdate.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('admin.activities.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    }
}
