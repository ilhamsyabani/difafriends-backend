<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\Roles;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', Roles::User->value)->get();
        $courses = Course::where('status', 'published')->get();

        if ($students->isEmpty() || $courses->isEmpty()) {
            return;
        }

        foreach ($students as $student) {
            // Tiap siswa enroll 1-4 kelas
            $enrollCourses = $courses->random(min(rand(1, 4), $courses->count()));

            foreach ($enrollCourses as $course) {
                $exists = Enrollment::where('user_id', $student->id)
                    ->where('course_id', $course->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $enrolledAt = now()->subDays(rand(7, 180));

                // Buat order terlebih dahulu
                $order = Order::create([
                    'user_id' => $student->id,
                    'orderable_type' => Course::class,
                    'orderable_id' => $course->id,
                    'item_name' => $course->title,
                    'original_price' => $course->price,
                    'final_amount' => $course->discount_price ?? $course->price,
                    'status' => OrderStatus::Paid->value,
                    'invoice_number' => 'INV-'.strtoupper(fake()->bothify('????-####')),
                    'created_at' => $enrolledAt,
                    'updated_at' => $enrolledAt,
                ]);

                Enrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'order_id' => $order->id,
                    'status' => 'active',
                    'enrolled_at' => $enrolledAt,
                    'created_at' => $enrolledAt,
                    'updated_at' => $enrolledAt,
                ]);
            }
        }
    }
}
