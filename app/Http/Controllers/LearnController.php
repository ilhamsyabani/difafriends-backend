<?php

namespace App\Http\Controllers;

use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LearnController extends Controller
{
    public function show(Request $request, Course $course): Response
    {
        $user = $request->user();

        // Cek enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment || !$enrollment->isAccessible()) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Kamu belum terdaftar di kelas ini.');
        }

        // Load course dengan semua konten
        $course->load([
            'sections' => fn($q) => $q->orderBy('sort_order'),
            'sections.lectures' => fn($q) => $q->orderBy('sort_order'),
            'sections.quiz',
            'instructor',

        ]);

        // Ambil progress user
        $progress = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->get()
            ->keyBy('lecture_id');

        // Lecture yang aktif — pertama yang belum selesai
        $activeLectureId = $progress
            ->where('is_completed', false)
            ->keys()
            ->first()
            ?? $course->sections->first()?->lectures->first()?->id;

        // Hitung progress percentage
        $totalLectures     = $course->sections->sum(fn($s) => $s->lectures->count());
        $completedLectures = $progress->where('is_completed', true)->count();
        $progressPercent   = $totalLectures > 0
            ? round(($completedLectures / $totalLectures) * 100)
            : 0;

        return Inertia::render('learn/Show', [
            'course'          => $course,
            'enrollment'      => $enrollment,
            'progress'        => $progress->values(),
            'activeLectureId' => $activeLectureId,
            'progressPercent' => $progressPercent,
            'totalLectures'   => $totalLectures,
            'completedLectures' => $completedLectures,
        ]);
    }

    public function updateProgress(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'lecture_id'    => 'required|exists:course_lectures,id',
            'watch_seconds' => 'required|integer|min:0',
            'is_completed'  => 'required|boolean',
        ]);

        $user = $request->user();

        $progressRecord = CourseProgress::updateOrCreate(
            [
                'user_id'   => $user->id,
                'course_id' => $course->id,
                'lecture_id'=> $request->lecture_id,
            ],
            [
                'watch_seconds' => $request->watch_seconds,
                'is_completed'  => $request->is_completed,
                'completed_at'  => $request->is_completed ? now() : null,
            ]
        );

        return response()->json([
            'success'          => true,
            'progress_percent' => $this->calculateProgress($user->id, $course->id),
        ]);
    }

    private function calculateProgress(int $userId, int $courseId): int
    {
        $course = Course::with('sections.lectures')->find($courseId);
        $total  = $course->sections->sum(fn($s) => $s->lectures->count());

        if ($total === 0) return 0;

        $completed = CourseProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('is_completed', true)
            ->count();

        return round(($completed / $total) * 100);
    }
}
