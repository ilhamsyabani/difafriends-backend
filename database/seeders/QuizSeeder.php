<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Template kuis per topik — soal pilihan ganda yang relevan untuk platform ABK.
     *
     * @var array<int, array{title: string, description: string, passing_score: int, questions: list<array{question: string, type: string, points: int, options?: list<array{text: string, correct: bool}>}>}>
     */
    private array $quizTemplates = [
        [
            'title' => 'Kuis: Dasar-Dasar Pemahaman',
            'description' => 'Uji pemahaman kamu tentang materi yang telah dipelajari di bagian ini.',
            'passing_score' => 70,
            'questions' => [
                [
                    'question' => 'Apa yang dimaksud dengan intervensi dini pada anak berkebutuhan khusus?',
                    'type' => 'multiple_choice',
                    'points' => 20,
                    'options' => [
                        ['text' => 'Pemberian obat-obatan pada usia dini', 'correct' => false],
                        ['text' => 'Program penanganan terstruktur yang dimulai sedini mungkin untuk mengoptimalkan perkembangan', 'correct' => true],
                        ['text' => 'Menempatkan anak di sekolah reguler tanpa dukungan', 'correct' => false],
                        ['text' => 'Menunggu anak mencapai usia sekolah sebelum memberikan bantuan', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'Manakah di bawah ini yang termasuk ciri-ciri umum anak dengan Autisme Spectrum Disorder (ASD)?',
                    'type' => 'multiple_choice',
                    'points' => 20,
                    'options' => [
                        ['text' => 'Kemampuan berbicara yang sangat cepat berkembang', 'correct' => false],
                        ['text' => 'Kesulitan dalam interaksi sosial dan komunikasi', 'correct' => true],
                        ['text' => 'Selalu menghindari rutinitas', 'correct' => false],
                        ['text' => 'Sangat suka berinteraksi dengan orang baru', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'Apa kepanjangan dari ABA dalam konteks terapi anak berkebutuhan khusus?',
                    'type' => 'multiple_choice',
                    'points' => 20,
                    'options' => [
                        ['text' => 'Applied Behavioral Analysis', 'correct' => true],
                        ['text' => 'Autism Behavior Assessment', 'correct' => false],
                        ['text' => 'Active Brain Activity', 'correct' => false],
                        ['text' => 'Academic Behavioral Approach', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'Jelaskan dengan singkat bagaimana kamu akan menerapkan strategi yang telah dipelajari dalam mendampingi anak di rumah!',
                    'type' => 'essay',
                    'points' => 40,
                ],
            ],
        ],
        [
            'title' => 'Kuis: Penerapan Teknik',
            'description' => 'Evaluasi pemahaman kamu tentang teknik-teknik yang dipelajari.',
            'passing_score' => 75,
            'questions' => [
                [
                    'question' => 'Apa yang dimaksud dengan "penguatan positif" dalam terapi ABA?',
                    'type' => 'multiple_choice',
                    'points' => 25,
                    'options' => [
                        ['text' => 'Menghukum perilaku negatif', 'correct' => false],
                        ['text' => 'Memberikan hadiah/reward setelah perilaku yang diinginkan muncul', 'correct' => true],
                        ['text' => 'Mengabaikan semua perilaku anak', 'correct' => false],
                        ['text' => 'Membatasi aktivitas anak', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'Berapa lama rata-rata durasi sesi terapi yang direkomendasikan untuk anak usia 3-5 tahun?',
                    'type' => 'multiple_choice',
                    'points' => 25,
                    'options' => [
                        ['text' => '5-10 menit', 'correct' => false],
                        ['text' => '15-30 menit', 'correct' => true],
                        ['text' => '1-2 jam', 'correct' => false],
                        ['text' => '3-4 jam', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'Apa yang sebaiknya dilakukan ketika anak menunjukkan perilaku tantrum selama sesi belajar?',
                    'type' => 'multiple_choice',
                    'points' => 25,
                    'options' => [
                        ['text' => 'Langsung menghentikan sesi dan memberikan apa yang anak inginkan', 'correct' => false],
                        ['text' => 'Tetap tenang, identifikasi penyebab, dan alihkan dengan aktivitas menenangkan', 'correct' => true],
                        ['text' => 'Menghukum anak agar tidak mengulangi perilaku tersebut', 'correct' => false],
                        ['text' => 'Membiarkan anak hingga berhenti sendiri tanpa intervensi', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'Sebutkan 2 strategi konkret yang dapat diterapkan di rumah berdasarkan materi yang telah dipelajari!',
                    'type' => 'essay',
                    'points' => 25,
                ],
            ],
        ],
    ];

    public function run(): void
    {
        // Tambahkan kuis ke section pertama dari beberapa kelas published
        $courses = Course::where('status', 'published')
            ->with(['sections' => fn ($q) => $q->orderBy('sort_order')])
            ->get()
            ->take(8); // 8 dari 13+ kelas published mendapat kuis

        foreach ($courses as $index => $course) {
            $section = $course->sections->first();
            if (! $section) {
                continue;
            }

            // Hindari duplikat
            if ($section->quiz()->exists()) {
                continue;
            }

            $template = $this->quizTemplates[$index % count($this->quizTemplates)];

            $quiz = Quiz::create([
                'course_id' => $course->id,
                'section_id' => $section->id,
                'title' => $template['title'],
                'description' => $template['description'],
                'is_required' => true,
                'passing_score' => $template['passing_score'],
            ]);

            foreach ($template['questions'] as $sortOrder => $qData) {
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'type' => $qData['type'],
                    'question' => $qData['question'],
                    'points' => $qData['points'],
                    'sort_order' => $sortOrder + 1,
                ]);

                if ($qData['type'] === 'multiple_choice') {
                    foreach ($qData['options'] as $optData) {
                        QuizOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optData['text'],
                            'is_correct' => $optData['correct'],
                        ]);
                    }
                }
            }
        }
    }
}
