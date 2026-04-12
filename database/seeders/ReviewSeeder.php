<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCourseReviews();
        $this->seedBookingReviews();
    }

    private function seedCourseReviews(): void
    {
        $courseComments = [
            'Materi sangat mudah dipahami, instruktur menjelaskan dengan sabar dan jelas.',
            'Kelas yang luar biasa! Anak saya sangat menikmati setiap sesi pembelajaran.',
            'Metode pengajaran yang inovatif dan efektif untuk anak berkebutuhan khusus.',
            'Saya sangat puas dengan kualitas kelas ini. Sangat direkomendasikan!',
            'Instruktur sangat berpengalaman dan memahami kebutuhan anak kami.',
            'Materi terstruktur dengan baik dan mudah diikuti. Terima kasih DifaFriends!',
            'Kelas ini memberikan dampak positif yang besar bagi perkembangan anak saya.',
            'Sangat membantu! Anak saya kini lebih percaya diri dalam berkomunikasi.',
            'Pendekatan yang digunakan sangat tepat untuk kondisi anak saya.',
            'Kelas online yang tidak membosankan, interaktif dan menyenangkan.',
        ];

        $enrollments = Enrollment::with(['user', 'course'])
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit(20)
            ->get();

        foreach ($enrollments as $enrollment) {
            // Hindari duplikat review
            $exists = Review::where('user_id', $enrollment->user_id)
                ->where('reviewable_type', Course::class)
                ->where('reviewable_id', $enrollment->course_id)
                ->exists();

            if ($exists) {
                continue;
            }

            $rating = $this->weightedRating();

            Review::create([
                'user_id' => $enrollment->user_id,
                'reviewable_type' => Course::class,
                'reviewable_id' => $enrollment->course_id,
                'rating' => $rating,
                'comment' => $courseComments[array_rand($courseComments)],
                'is_published' => true,
                'published_at' => now()->subDays(rand(1, 90)),
                'created_at' => now()->subDays(rand(1, 90)),
            ]);
        }
    }

    private function seedBookingReviews(): void
    {
        $bookingComments = [
            'Guru pendamping sangat sabar dan profesional. Anak saya nyaman sekali.',
            'Sesi berlangsung dengan baik, metode yang digunakan sangat efektif.',
            'Pendamping sangat mengerti kondisi anak saya, terima kasih banyak!',
            'Progres anak saya terlihat jelas setelah beberapa sesi. Luar biasa!',
            'Komunikasi sebelum dan sesudah sesi sangat baik dan informatif.',
            'Guru pendampingnya ramah dan sabar sekali, anak saya tidak mau berhenti belajar.',
            'Metode belajar yang menyenangkan membuat anak saya semangat setiap sesi.',
            'Sangat puas dengan pelayanan, akan terus melanjutkan sesi.',
        ];

        $completedBookings = Booking::with(['student'])
            ->where('status', BookingStatus::Completed->value)
            ->inRandomOrder()
            ->limit(15)
            ->get();

        foreach ($completedBookings as $booking) {
            $exists = Review::where('user_id', $booking->student_id)
                ->where('reviewable_type', Booking::class)
                ->where('reviewable_id', $booking->id)
                ->exists();

            if ($exists) {
                continue;
            }

            $rating = $this->weightedRating();

            Review::create([
                'user_id' => $booking->student_id,
                'reviewable_type' => Booking::class,
                'reviewable_id' => $booking->id,
                'rating' => $rating,
                'comment' => $bookingComments[array_rand($bookingComments)],
                'is_published' => true,
                'published_at' => $booking->completed_at ?? now()->subDays(rand(1, 60)),
                'created_at' => $booking->completed_at ?? now()->subDays(rand(1, 60)),
            ]);
        }
    }

    /** Rating berbobot — kebanyakan 4-5 bintang, realistis untuk platform baru */
    private function weightedRating(): int
    {
        $weights = [1 => 2, 2 => 3, 3 => 10, 4 => 30, 5 => 55];
        $total = array_sum($weights);
        $rand = rand(1, $total);
        $cumulative = 0;
        foreach ($weights as $rating => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $rating;
            }
        }

        return 5;
    }
}
