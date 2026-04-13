<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuizGradeController extends Controller
{
    public function index(Request $request, Course $course, Quiz $quiz): Response
    {
        abort_if($course->instructor_id !== $request->user()->id, 403);

        // Ambil semua attempt yang ada esai belum dinilai
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->with([
                'user',
                'answers' => fn ($q) => $q->with(['question', 'selectedOption']),
            ])
            ->latest()
            ->paginate(10);

        return Inertia::render('instructor/quiz/Grade', [
            'course' => $course,
            'quiz' => $quiz->load('questions'),
            'attempts' => $attempts,
        ]);
    }

    public function grade(Request $request, QuizAnswer $answer)
    {
        // Pastikan hanya instruktur course yang bisa nilai
        $course = $answer->attempt->quiz->course;
        abort_if($course->instructor_id !== $request->user()->id, 403);

        // Hanya esai yang bisa di-grade manual
        abort_if($answer->question->isMultipleChoice(), 422);

        $validated = $request->validate([
            'points_earned' => 'required|integer|min:0|max:'.$answer->question->points,
            'instructor_note' => 'nullable|string|max:500',
        ]);

        $answer->update($validated);

        // Cek apakah semua esai di attempt ini sudah dinilai
        $attempt = $answer->attempt;
        $essayAnswers = $attempt->answers()
            ->whereHas('question', fn ($q) => $q->where('type', 'essay'))
            ->get();

        $allGraded = $essayAnswers->every(
            fn ($a) => $a->points_earned !== null
        );

        if ($allGraded) {
            // Hitung total score
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
