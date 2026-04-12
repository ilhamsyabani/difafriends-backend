<?php

namespace Database\Factories;

use App\Enums\CourseStatus;
use App\Enums\Roles;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->numberBetween(10000, 500000);
        $title = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::whereNotNull('parent_id')
                ->inRandomOrder()
                ->value('id'),
            'instructor_id' => User::where('role', Roles::Instructor->value)
                ->inRandomOrder()
                ->value('id'),

            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 900),
            'description' => fake()->paragraph(3, true),
            'thumbnail' => 'https://picsum.photos/seed/course-'.fake()->unique()->numberBetween(1, 500).'/800/450',
            'preview_video' => fake()->url,
            'price' => $price,
            'discount_price' => fake()->boolean(40) ? $price - fake()->numberBetween(10000, 50000) : null,
            'duration_minutes' => fake()->numberBetween(60, 600),
            'has_certificate' => fake()->boolean(70),
            'prerequisites' => fake()->sentence(),
            'is_featured' => fake()->boolean(20),
            'status' => CourseStatus::Published->value,
        ];
    }

    public function draft(): static
    {
        return $this->state([
            'status' => CourseStatus::Draft->value,
        ]);
    }

    public function published(): static
    {
        return $this->state([
            'status' => CourseStatus::Published->value,
        ]);
    }

    public function featured(): static
    {
        return $this->state([
            'is_featured' => true,
            'status' => CourseStatus::Published->value,
        ]);
    }

    public function free(): static
    {
        return $this->state([
            'price' => 0,
            'discount_price' => null,
        ]);
    }
}
