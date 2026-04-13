<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuizGradeController extends Controller
{
    public function index(Course $course, Quiz $quiz): Response
    {
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->with([
                'user',
                'answers' => fn ($q) => $q->with(['question', 'selectedOption']),
            ])
            ->latest()
            ->paginate(5);

        return Inertia::render('admin/quiz/Grade', [
            'course' => $course,
            'quiz' => $quiz->load('questions'),
            'attempts' => $attempts,
        ]);
    }

    public function grade(Request $request, QuizAnswer $answer): RedirectResponse
    {
        abort_if($answer->question->isMultipleChoice(), 422);

        $validated = $request->validate([
            'points_earned' => ['required', 'integer', 'min:0', 'max:'.$answer->question->points],
            'instructor_note' => ['nullable', 'string', 'max:500'],
        ]);

        $answer->update($validated);

        // Cek apakah semua esai sudah dinilai → update status attempt
        $attempt = $answer->attempt;
        $essayAnswers = $attempt->answers()
            ->whereHas('question', fn ($q) => $q->where('type', 'essay'))
            ->get();

        $allGraded = $essayAnswers->every(fn ($a) => $a->points_earned !== null);

        if ($allGraded) {
            $totalScore = $attempt->answers()->sum('points_earned');
            $totalPoints = $attempt->quiz->totalPoints();
            $scorePercent = $totalPoints > 0
                ? round(($totalScore / $totalPoints) * 100)
                : 0;

            $attempt->update([
                'score' => $scorePercent,
                'status' => 'graded',
            ]);
        }

        return back()->with('success', 'Penilaian berhasil disimpan.');
    }
}
