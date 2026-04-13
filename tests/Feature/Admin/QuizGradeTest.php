<?php

use App\Enums\Roles;
use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;

// ── Helpers ────────────────────────────────────────────────────────

/**
 * @return array{admin: User, instructor: User, student: User, course: Course, quiz: Quiz}
 */
function setupAdminQuizContext(): array
{
    $admin = User::factory()->create(['role' => Roles::Admin->value]);
    $instructor = User::factory()->create(['role' => Roles::Instructor->value]);
    $student = User::factory()->create(['role' => Roles::User->value]);
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    $quiz = Quiz::create([
        'course_id' => $course->id,
        'section_id' => null,
        'title' => 'Kuis Admin Test',
        'passing_score' => 70,
        'is_required' => true,
    ]);

    return compact('admin', 'instructor', 'student', 'course', 'quiz');
}

function makeEssayQuestion(Quiz $quiz, int $points = 100): QuizQuestion
{
    return QuizQuestion::create([
        'quiz_id' => $quiz->id,
        'type' => 'essay',
        'question' => 'Jelaskan dengan singkat!',
        'points' => $points,
        'sort_order' => 1,
    ]);
}

function makeMcQuestion(Quiz $quiz, int $points = 50): QuizQuestion
{
    $q = QuizQuestion::create([
        'quiz_id' => $quiz->id,
        'type' => 'multiple_choice',
        'question' => 'Pilih yang benar!',
        'points' => $points,
        'sort_order' => 2,
    ]);

    QuizOption::create(['question_id' => $q->id, 'option_text' => 'Benar', 'is_correct' => true]);
    QuizOption::create(['question_id' => $q->id, 'option_text' => 'Salah', 'is_correct' => false]);

    return $q;
}

// ── Tests ──────────────────────────────────────────────────────────

test('admin dapat membuka halaman penilaian kuis', function () {
    ['admin' => $admin, 'course' => $course, 'quiz' => $quiz] = setupAdminQuizContext();

    $this->actingAs($admin)
        ->get(route('admin.courses.quiz.grade', [$course, $quiz]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/quiz/Grade')
            ->has('course')
            ->has('quiz')
            ->has('attempts')
        );
});

test('instruktur tidak bisa mengakses halaman penilaian kuis admin', function () {
    ['instructor' => $instructor, 'course' => $course, 'quiz' => $quiz] = setupAdminQuizContext();

    $this->actingAs($instructor)
        ->get(route('admin.courses.quiz.grade', [$course, $quiz]))
        ->assertForbidden();
});

test('guest diarahkan ke halaman login', function () {
    ['course' => $course, 'quiz' => $quiz] = setupAdminQuizContext();

    $this->get(route('admin.courses.quiz.grade', [$course, $quiz]))
        ->assertRedirect(route('login'));
});

test('admin dapat melihat semua attempt peserta di halaman penilaian', function () {
    ['admin' => $admin, 'student' => $student, 'course' => $course, 'quiz' => $quiz] = setupAdminQuizContext();

    QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
        'started_at' => now()->subMinutes(10),
        'submitted_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.courses.quiz.grade', [$course, $quiz]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('attempts.data', 1));
});

test('admin dapat memberikan nilai pada jawaban esai', function () {
    ['admin' => $admin, 'student' => $student, 'course' => $course, 'quiz' => $quiz] = setupAdminQuizContext();

    $question = makeEssayQuestion($quiz, 100);

    $attempt = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
        'started_at' => now()->subMinutes(5),
        'submitted_at' => now(),
    ]);

    $answer = QuizAnswer::create([
        'attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'essay_answer' => 'Jawaban esai dari siswa.',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.quiz.answer.grade', $answer), [
            'points_earned' => 80,
            'instructor_note' => 'Bagus, cukup jelas.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('quiz_answers', [
        'id' => $answer->id,
        'points_earned' => 80,
        'instructor_note' => 'Bagus, cukup jelas.',
    ]);
});

test('status attempt menjadi graded setelah semua esai dinilai', function () {
    ['admin' => $admin, 'student' => $student, 'course' => $course, 'quiz' => $quiz] = setupAdminQuizContext();

    $question = makeEssayQuestion($quiz, 100);

    $attempt = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
        'started_at' => now()->subMinutes(5),
        'submitted_at' => now(),
    ]);

    $answer = QuizAnswer::create([
        'attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'essay_answer' => 'Jawaban esai dari siswa.',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.quiz.answer.grade', $answer), [
            'points_earned' => 85,
            'instructor_note' => null,
        ]);

    // score = 85/100 = 85%
    $this->assertDatabaseHas('quiz_attempts', [
        'id' => $attempt->id,
        'status' => 'graded',
        'score' => 85,
    ]);
});

test('attempt tetap pending jika masih ada esai yang belum dinilai', function () {
    ['admin' => $admin, 'student' => $student, 'course' => $course, 'quiz' => $quiz] = setupAdminQuizContext();

    $q1 = makeEssayQuestion($quiz, 50);
    $q2 = QuizQuestion::create([
        'quiz_id' => $quiz->id,
        'type' => 'essay',
        'question' => 'Soal esai kedua.',
        'points' => 50,
        'sort_order' => 2,
    ]);

    $attempt = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
        'started_at' => now()->subMinutes(5),
        'submitted_at' => now(),
    ]);

    $answer1 = QuizAnswer::create([
        'attempt_id' => $attempt->id,
        'question_id' => $q1->id,
        'essay_answer' => 'Jawaban pertama.',
    ]);

    // Jawaban kedua sengaja belum dinilai
    QuizAnswer::create([
        'attempt_id' => $attempt->id,
        'question_id' => $q2->id,
        'essay_answer' => 'Jawaban kedua.',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.quiz.answer.grade', $answer1), [
            'points_earned' => 40,
        ]);

    $this->assertDatabaseHas('quiz_attempts', [
        'id' => $attempt->id,
        'status' => 'pending',
    ]);
});

test('admin tidak bisa menilai jawaban pilihan ganda', function () {
    ['admin' => $admin, 'student' => $student, 'course' => $course, 'quiz' => $quiz] = setupAdminQuizContext();

    $mcQuestion = makeMcQuestion($quiz);
    $correctOption = $mcQuestion->options()->where('is_correct', true)->first();

    $attempt = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
        'started_at' => now(),
        'submitted_at' => now(),
    ]);

    $mcAnswer = QuizAnswer::create([
        'attempt_id' => $attempt->id,
        'question_id' => $mcQuestion->id,
        'selected_option_id' => $correctOption->id,
        'points_earned' => $mcQuestion->points,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.quiz.answer.grade', $mcAnswer), [
            'points_earned' => 30,
        ])
        ->assertStatus(422);
});

test('nilai tidak boleh melebihi poin maksimal soal', function () {
    ['admin' => $admin, 'student' => $student, 'course' => $course, 'quiz' => $quiz] = setupAdminQuizContext();

    $question = makeEssayQuestion($quiz, 50);

    $attempt = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
        'started_at' => now(),
        'submitted_at' => now(),
    ]);

    $answer = QuizAnswer::create([
        'attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'essay_answer' => 'Jawaban esai.',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.quiz.answer.grade', $answer), [
            'points_earned' => 999, // melebihi max 50
        ])
        ->assertSessionHasErrors('points_earned');
});
