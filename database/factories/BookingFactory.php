<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Enums\SessionType;
use App\Models\Booking;
use App\Models\TutorSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $schedule = TutorSchedule::factory()->create();
        $startAt = fake()->dateTimeBetween('+1 days', '+30 days');

        return [
            'student_id' => User::factory(),
            'tutor_id' => $schedule->tutor_id,
            'tutor_schedule_id' => $schedule->id,
            'start_at' => $startAt,
            'end_at' => (clone $startAt)->modify("+{$schedule->session_duration} minutes"),
            'type' => fake()->randomElement(SessionType::cases())->value,
            'status' => BookingStatus::Confirmed,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => BookingStatus::Pending]);
    }

    public function confirmed(): static
    {
        return $this->state(['status' => BookingStatus::Confirmed]);
    }

    public function completed(): static
    {
        return $this->state([
            'status' => BookingStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'status' => BookingStatus::Cancelled,
            'cancelled_at' => now(),
        ]);
    }
}
