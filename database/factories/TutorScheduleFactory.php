<?php

namespace Database\Factories;

use App\Models\TutorSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TutorSchedule>
 */
class TutorScheduleFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->numberBetween(7, 16);

        return [
            'tutor_id' => User::factory()->companion(),
            'day_of_week' => fake()->numberBetween(0, 6),
            'start_time' => sprintf('%02d:00', $start),
            'end_time' => sprintf('%02d:00', $start + 2),
            'session_duration' => fake()->randomElement([60, 90, 120]),
            'break_time' => fake()->randomElement([0, 10, 15]),
            'price' => fake()->randomElement([100000, 150000, 200000, 250000]),
            'max_participants' => fake()->numberBetween(1, 5),
            'is_active' => true,
        ];
    }
}
