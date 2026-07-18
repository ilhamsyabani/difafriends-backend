<?php

namespace Database\Factories;

use App\Models\AttendanceForm;
use App\Models\AttendanceSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attendance_form_id' => AttendanceForm::factory(),
            'session_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'token' => Str::random(32),
            'is_open' => true,
        ];
    }
}
