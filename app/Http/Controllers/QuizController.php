<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    // Halaman kerjakan kuis
    public function show(Request $request, Course $course, Quiz $quiz): Response
    {
        $this->checkEnrollment($request, $course);

        $quiz->load(['questions.options', 'section']);

        // Cek sudah ada attempt yang belum submit
        $pendingAttempt = QuizAttempt::where('user_id', $request->user()->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('submitted_at')
            ->first();

        // Cek sudah ada attempt yang sudah graded
        $lastAttempt = QuizAttempt::where('user_id', $request->user()->id)
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('submitted_at')
            ->latest()
            ->first();

        return Inertia::render('user/Quiz', [
            'course' => $course,
            'quiz' => $quiz,
            'pendingAttempt' => $pendingAttempt,
            'lastAttempt' => $lastAttempt?->load('answers.question'),
            'canRetake' => $lastAttempt?->isGraded() ?? false,
        ]);
    }

    // Mulai attempt baru
    public function start(Request $request, Course $course, Quiz $quiz)
    {
        $this->checkEnrollment($request, $course);

        // Hapus attempt yang belum submit jika ada
        QuizAttempt::where('user_id', $request->user()->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('submitted_at')
            ->delete();

        $attempt = QuizAttempt::create([
            'user_id' => $request->user()->id,
            'quiz_id' => $quiz->id,
            'status' => 'pending',
            'started_at' => now(),
        ]);

        return response()->json([
            'attempt_id' => $attempt->id,
        ]);
    }

    // Submit jawaban
    public function submit(Request $request, Course $course, Quiz $quiz)
    {
        $this->checkEnrollment($request, $course);

        $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:quiz_questions,id',
            'answers.*.selected_option_id' => 'nullable|exists:quiz_options,id',
            'answers.*.essay_answer' => 'nullable|string',
        ]);

        $attempt = QuizAttempt::where('id', $request->attempt_id)
            ->where('user_id', $request->user()->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('submitted_at')
            ->firstOrFail();

        $quiz->load('questions.options');

        $totalPointsEarned = 0;
        $hasEssay = false;

        foreach ($request->answers as $ans) {
            $question = $quiz->questions->find($ans['question_id']);
            if (! $question) {
                continue;
            }

            $pointsEarned = null;

            if ($question->isMultipleChoice()) {
                // Auto-score PG
                $correct = $question->correctOption();
                $isCorrect = $correct && $correct->id == $ans['selected_option_id'];
                $pointsEarned = $isCorrect ? $question->points : 0;
                $totalPointsEarned += $pointsEarned;
            } else {
                // Esai — tunggu instruktur nilai
                $hasEssay = true;
            }

            QuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_option_id' => $ans['selected_option_id'] ?? null,
                'essay_answer' => $ans['essay_answer'] ?? null,
                'points_earned' => $pointsEarned,
                'instructor_note' => null,
            ]);
        }

        // Hitung score
        if (! $hasEssay) {
            // Semua PG — langsung graded
            $totalPoints = $quiz->totalPoints();
            $scorePercent = $totalPoints > 0
                ? round(($totalPointsEarned / $totalPoints) * 100)
                : 0;

            $attempt->update([
                'score' => $scorePercent,
                'status' => 'graded',
                'submitted_at' => now(),
            ]);
        } else {
            // Ada esai — status pending, score dihitung setelah instruktur nilai
            // Hitung dulu score dari PG saja
            $attempt->update([
                'submitted_at' => now(),
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'success' => true,
            'has_essay' => $hasEssay,
            'status' => $attempt->fresh()->status,
            'score' => $attempt->fresh()->score,
        ]);
    }

    // Halaman hasil kuis
    public function result(Request $request, Course $course, Quiz $quiz): Response
    {
        $this->checkEnrollment($request, $course);

        $attempt = QuizAttempt::where('user_id', $request->user()->id)
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('submitted_at')
            ->with([
                'answers' => fn ($q) => $q->with([
                    'question.options',
                    'selectedOption',
                ]),
            ])
            ->latest()
            ->firstOrFail();

        return Inertia::render('user/QuizResult', [
            'course' => $course,
            'quiz' => $quiz->load('questions'),
            'attempt' => $attempt,
            'passed' => $attempt->isPassed(),
        ]);
    }

    private function checkEnrollment(Request $request, Course $course): void
    {
        $enrolled = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        abort_if(! $enrolled, 403, 'Kamu belum terdaftar di kelas ini.');
    }
}
