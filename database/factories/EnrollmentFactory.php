<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        $instructor = User::factory()->instructor()->create();
        $category = Category::factory()->create();

        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory()->create([
                'instructor_id' => $instructor->id,
                'category_id' => $category->id,
            ]),
            'order_id' => null,
            'status' => 'active',
            'enrolled_at' => now(),
        ];
    }

    public function completed(): static
    {
        return $this->state([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'status' => 'expired',
            'expired_at' => now()->subDay(),
        ]);
    }
}
