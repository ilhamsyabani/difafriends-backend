<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $activities = Activity::with('causer', 'subject')
            ->when($request->causer_id, fn ($q) => $q->where('causer_id', $request->causer_id))
            ->when($request->log_name, fn ($q) => $q->where('log_name', $request->log_name))
            ->when($request->event, fn ($q) => $q->where('event', $request->event))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($activity) => [
                'id' => $activity->id,
                'log_name' => $activity->log_name,
                'description' => $activity->description,
                'event' => $activity->event,
                'subject_type' => $activity->subject_type ? class_basename($activity->subject_type) : null,
                'subject_id' => $activity->subject_id,
                'subject' => $activity->subject ? $this->resolveSubject($activity->subject) : null,
                'causer' => $activity->causer ? [
                    'id' => $activity->causer->id,
                    'name' => $activity->causer->first_name.' '.$activity->causer->last_name,
                    'email' => $activity->causer->email,
                ] : null,
                'properties' => $activity->properties,
                'created_at' => $activity->created_at->format('d M Y H:i'),
            ]);

        $causers = User::select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->first_name.' '.$u->last_name,
            ]);

        return Inertia::render('admin/activity-log/Index', [
            'activities' => $activities,
            'causers' => $causers,
            'filters' => $request->only(['causer_id', 'log_name', 'event', 'date_from', 'date_to']),
        ]);
    }

    private function resolveSubject(mixed $subject): array
    {
        return [
            'id' => $subject->id,
            'label' => match (true) {
                isset($subject->title) => $subject->title,
                isset($subject->first_name) => $subject->first_name.' '.$subject->last_name,
                isset($subject->name) => $subject->name,
                default => '#'.$subject->id,
            },
        ];
    }
}
