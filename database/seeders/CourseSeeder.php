<?php

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Enums\Roles;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseGoal;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /** Judul kelas relevan untuk platform ABK */
    private array $courseTitles = [
        ['title' => 'Dasar Terapi Wicara untuk Anak Autisme', 'featured' => true],
        ['title' => 'Teknik ABA: Penguatan Positif dalam Pembelajaran Anak', 'featured' => true],
        ['title' => 'Strategi Mengajar Anak Disleksia di Rumah', 'featured' => true],
        ['title' => 'Sensori Integrasi: Memahami dan Membantu Anak Hipersensitif', 'featured' => false],
        ['title' => 'Komunikasi Augmentatif (AAC) untuk Anak Non-Verbal', 'featured' => false],
        ['title' => 'Matematika Visual untuk Anak Diskalkulia', 'featured' => false],
        ['title' => 'Manajemen Perilaku: Strategi Praktis untuk Orang Tua', 'featured' => false],
        ['title' => 'Membaca Bermakna: Metode Phonics untuk Anak Kebutuhan Khusus', 'featured' => false],
        ['title' => 'Keterampilan Sosial: Membantu Anak Autisme Berteman', 'featured' => false],
        ['title' => 'Regulasi Emosi: Panduan Praktis untuk Anak dan Orang Tua', 'featured' => false],
        ['title' => 'Motorik Halus: Aktivitas Menyenangkan untuk Tumbuh Kembang Anak', 'featured' => false],
        ['title' => 'Panduan PECS untuk Orang Tua: Komunikasi Melalui Gambar', 'featured' => false],
        ['title' => 'Intervensi Dini: Langkah Pertama yang Tepat untuk Anak Berkebutuhan Khusus', 'featured' => true],
    ];

    public function run(): void
    {
        $instructors = User::where('role', Roles::Instructor->value)->get();

        if ($instructors->isEmpty()) {
            return;
        }

        foreach ($this->courseTitles as $index => $courseData) {
            $price = fake()->randomElement([99000, 149000, 199000, 249000, 299000, 349000, 399000]);
            $hasDiscount = fake()->boolean(40);
            $thumbnailSeed = 'difacourse-'.($index + 1);

            $course = Course::create([
                'category_id' => Category::whereNotNull('parent_id')->inRandomOrder()->value('id'),
                'instructor_id' => $instructors->random()->id,
                'title' => $courseData['title'],
                'slug' => Str::slug($courseData['title']).'-'.fake()->unique()->numberBetween(100, 900),
                'description' => $this->generateDescription($courseData['title']),
                'thumbnail' => "https://picsum.photos/seed/{$thumbnailSeed}/800/450",
                'preview_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'price' => $price,
                'discount_price' => $hasDiscount ? $price - fake()->randomElement([20000, 30000, 50000, 70000]) : null,
                'duration_minutes' => fake()->numberBetween(90, 480),
                'has_certificate' => fake()->boolean(70),
                'prerequisites' => 'Tidak ada prasyarat khusus. Kelas ini terbuka untuk semua orang tua dan tenaga pendidik.',
                'is_featured' => $courseData['featured'],
                'status' => CourseStatus::Published->value,
            ]);

            $this->seedCourseContent($course);
        }

        // 2 kelas draft
        Course::factory(2)->draft()->create()->each(fn ($c) => $this->seedCourseContent($c));
    }

    private function generateDescription(string $title): string
    {
        return "Kelas **{$title}** dirancang khusus untuk orang tua, pengasuh, dan tenaga pendidik yang mendampingi anak berkebutuhan khusus. "
            .'Melalui pendekatan yang berbasis bukti dan praktis, peserta akan mendapatkan pemahaman mendalam serta keterampilan langsung yang dapat diterapkan sehari-hari. '
            .'Materi disajikan dalam format video interaktif, dilengkapi dengan panduan praktis dan lembar kerja yang dapat diunduh. '
            .'Instruktur kami adalah praktisi berpengalaman di bidangnya yang telah mendampingi ratusan keluarga ABK di seluruh Indonesia.';
    }

    private function seedCourseContent(Course $course): void
    {
        // Goals
        $goals = [
            'Memahami kebutuhan unik anak berkebutuhan khusus',
            'Menerapkan teknik yang terbukti efektif secara ilmiah',
            'Mengembangkan strategi pembelajaran yang dipersonalisasi',
            'Membangun komunikasi yang efektif dengan anak',
            'Mengelola tantangan perilaku dengan pendekatan positif',
        ];

        $selectedGoals = array_slice($goals, 0, rand(3, 5));
        CourseGoal::insert(
            collect($selectedGoals)->map(fn ($goal) => [
                'course_id' => $course->id,
                'goal_name' => $goal,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );

        // Sections & Lectures
        $sectionNames = [
            'Pengantar dan Dasar-Dasar',
            'Teknik dan Strategi Utama',
            'Latihan Praktis',
            'Studi Kasus dan Penerapan',
            'Evaluasi dan Langkah Selanjutnya',
        ];

        $sectionsCount = rand(2, 4);
        foreach (range(1, $sectionsCount) as $sectionOrder) {
            $section = CourseSection::create([
                'course_id' => $course->id,
                'title' => $sectionNames[$sectionOrder - 1] ?? 'Bagian '.$sectionOrder,
                'sort_order' => $sectionOrder,
            ]);

            foreach (range(1, rand(3, 5)) as $lectureOrder) {
                CourseLecture::create([
                    'course_id' => $course->id,
                    'section_id' => $section->id,
                    'title' => $this->lectureTitles($sectionOrder, $lectureOrder),
                    'type' => 'video',
                    'url' => 'https://www.youtube.com/watch?v='.fake()->regexify('[A-Za-z0-9]{11}'),
                    'video_duration' => fake()->numberBetween(300, 1800),
                    'is_free_preview' => $sectionOrder === 1 && $lectureOrder === 1,
                    'sort_order' => $lectureOrder,
                ]);
            }
        }
    }

    private function lectureTitles(int $section, int $lecture): string
    {
        $map = [
            1 => ['Sambutan dan Gambaran Umum Kelas', 'Memahami Kondisi Anak', 'Pentingnya Intervensi Dini', 'Menyiapkan Lingkungan Belajar', 'Membangun Rutinitas Harian'],
            2 => ['Strategi Dasar yang Perlu Diketahui', 'Teknik Penguatan Positif', 'Mengelola Tantangan Umum', 'Komunikasi Efektif dengan Anak', 'Kolaborasi dengan Tim Terapis'],
            3 => ['Praktik Langsung: Sesi 1', 'Praktik Langsung: Sesi 2', 'Latihan di Rumah', 'Evaluasi Kemajuan Harian', 'Penyesuaian Strategi'],
            4 => ['Studi Kasus: Anak Autisme', 'Studi Kasus: ADHD', 'Studi Kasus: Down Syndrome', 'Diskusi dan Tanya Jawab', 'Penerapan di Sekolah'],
            5 => ['Merangkum Pembelajaran', 'Rencana Tindak Lanjut', 'Sumber Daya Tambahan', 'Sertifikasi dan Evaluasi Akhir', 'Komunitas dan Dukungan'],
        ];

        $titles = $map[$section] ?? $map[1];

        return $titles[$lecture - 1] ?? "Materi {$lecture}";
    }
}
