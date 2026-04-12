<?php

use App\Enums\OrderStatus;
use App\Enums\Roles;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

// ── Helpers ────────────────────────────────────────────────────────

/**
 * Buat kelas + kuis lengkap (3 soal PG) dan return semuanya.
 *
 * @return array{course: Course, quiz: Quiz, questions: Collection}
 */
function setupCourseWithQuiz(): array
{
    $instructor = User::factory()->create(['role' => Roles::Instructor->value]);
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    $quiz = Quiz::create([
        'course_id' => $course->id,
        'section_id' => null,
        'title' => 'Kuis Uji Coba',
        'passing_score' => 70,
        'is_required' => true,
    ]);

    $questions = collect(range(1, 3))->map(function ($i) use ($quiz) {
        $q = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'type' => 'multiple_choice',
            'question' => "Pertanyaan {$i}?",
            'points' => 34,
            'sort_order' => $i,
        ]);

        $options = collect(range(1, 4))->map(fn ($j) => QuizOption::create([
            'question_id' => $q->id,
            'option_text' => "Pilihan {$j}",
            'is_correct' => $j === 1, // pilihan pertama = benar
        ]));

        $q->setRelation('options', $options);

        return $q;
    });

    return compact('course', 'quiz', 'questions');
}

function enrollUser(User $user, Course $course): void
{
    $order = Order::create([
        'user_id' => $user->id,
        'orderable_type' => Course::class,
        'orderable_id' => $course->id,
        'item_name' => $course->title,
        'original_price' => $course->price,
        'final_amount' => $course->price,
        'status' => OrderStatus::Paid->value,
        'invoice_number' => 'INV-TEST-'.fake()->unique()->numberBetween(1000, 9999),
    ]);

    Enrollment::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'order_id' => $order->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);
}

// ── Tests ──────────────────────────────────────────────────────────

test('siswa terdaftar dapat membuka halaman kuis', function () {
    $student = User::factory()->create(['role' => Roles::User->value]);
    ['course' => $course, 'quiz' => $quiz] = setupCourseWithQuiz();
    enrollUser($student, $course);

    $this->actingAs($student)
        ->get(route('quiz.show', [$course, $quiz]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('user/Quiz'));
});

test('siswa yang belum terdaftar tidak bisa membuka kuis', function () {
    $stranger = User::factory()->create(['role' => Roles::User->value]);
    ['course' => $course, 'quiz' => $quiz] = setupCourseWithQuiz();

    $this->actingAs($stranger)
        ->get(route('quiz.show', [$course, $quiz]))
        ->assertForbidden();
});

test('siswa dapat memulai attempt kuis', function () {
    $student = User::factory()->create(['role' => Roles::User->value]);
    ['course' => $course, 'quiz' => $quiz] = setupCourseWithQuiz();
    enrollUser($student, $course);

    $this->actingAs($student)
        ->post(route('quiz.start', [$course, $quiz]))
        ->assertOk()
        ->assertJsonStructure(['attempt_id']);

    $this->assertDatabaseHas('quiz_attempts', [
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
    ]);
});

test('memulai kuis baru menghapus attempt lama yang belum disubmit', function () {
    $student = User::factory()->create(['role' => Roles::User->value]);
    ['course' => $course, 'quiz' => $quiz] = setupCourseWithQuiz();
    enrollUser($student, $course);

    // Attempt lama yang belum disubmit
    $oldAttempt = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
        'started_at' => now()->subHour(),
    ]);

    $this->actingAs($student)
        ->post(route('quiz.start', [$course, $quiz]))
        ->assertOk();

    $this->assertDatabaseMissing('quiz_attempts', ['id' => $oldAttempt->id]);
});

test('kuis pilihan ganda auto-graded setelah submit semua benar', function () {
    $student = User::factory()->create(['role' => Roles::User->value]);
    ['course' => $course, 'quiz' => $quiz, 'questions' => $questions] = setupCourseWithQuiz();
    enrollUser($student, $course);

    $attempt = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
        'started_at' => now(),
    ]);

    // Jawab semua soal dengan jawaban benar
    $answers = $questions->map(fn ($q) => [
        'question_id' => $q->id,
        'selected_option_id' => $q->options->firstWhere('is_correct', true)->id,
        'essay_answer' => null,
    ])->toArray();

    $this->actingAs($student)
        ->post(route('quiz.submit', [$course, $quiz]), [
            'attempt_id' => $attempt->id,
            'answers' => $answers,
        ])
        ->assertOk()
        ->assertJson(['success' => true, 'has_essay' => false, 'status' => 'graded']);

    $this->assertDatabaseHas('quiz_attempts', [
        'id' => $attempt->id,
        'status' => 'graded',
    ]);
});

test('attempt yang salah semua mendapat score 0', function () {
    $student = User::factory()->create(['role' => Roles::User->value]);
    ['course' => $course, 'quiz' => $quiz, 'questions' => $questions] = setupCourseWithQuiz();
    enrollUser($student, $course);

    $attempt = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
        'started_at' => now(),
    ]);

    // Jawab semua dengan jawaban salah (pilihan ke-2)
    $answers = $questions->map(fn ($q) => [
        'question_id' => $q->id,
        'selected_option_id' => $q->options->firstWhere('is_correct', false)->id,
        'essay_answer' => null,
    ])->toArray();

    $this->actingAs($student)
        ->post(route('quiz.submit', [$course, $quiz]), [
            'attempt_id' => $attempt->id,
            'answers' => $answers,
        ])
        ->assertOk()
        ->assertJson(['status' => 'graded', 'score' => 0]);
});

test('kuis dengan soal essay tidak auto-graded — status tetap pending', function () {
    $student = User::factory()->create(['role' => Roles::User->value]);
    $instructor = User::factory()->create(['role' => Roles::Instructor->value]);
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);
    enrollUser($student, $course);

    $quiz = Quiz::create([
        'course_id' => $course->id,
        'section_id' => null,
        'title' => 'Kuis Essay',
        'passing_score' => 70,
        'is_required' => true,
    ]);

    $essayQuestion = QuizQuestion::create([
        'quiz_id' => $quiz->id,
        'type' => 'essay',
        'question' => 'Jelaskan secara singkat!',
        'points' => 100,
        'sort_order' => 1,
    ]);

    $attempt = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'pending',
        'started_at' => now(),
    ]);

    $this->actingAs($student)
        ->post(route('quiz.submit', [$course, $quiz]), [
            'attempt_id' => $attempt->id,
            'answers' => [[
                'question_id' => $essayQuestion->id,
                'selected_option_id' => null,
                'essay_answer' => 'Jawaban esai saya.',
            ]],
        ])
        ->assertOk()
        ->assertJson(['has_essay' => true, 'status' => 'pending']);
});

test('halaman hasil kuis bisa diakses setelah submit', function () {
    $student = User::factory()->create(['role' => Roles::User->value]);
    ['course' => $course, 'quiz' => $quiz] = setupCourseWithQuiz();
    enrollUser($student, $course);

    QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'status' => 'graded',
        'score' => 85,
        'started_at' => now()->subMinutes(10),
        'submitted_at' => now(),
    ]);

    $this->actingAs($student)
        ->get(route('quiz.result', [$course, $quiz]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('user/QuizResult'));
});

test('guest tidak bisa mengakses kuis dan diarahkan ke login', function () {
    ['course' => $course, 'quiz' => $quiz] = setupCourseWithQuiz();

    $this->get(route('quiz.show', [$course, $quiz]))
        ->assertRedirect(route('login'));
});
