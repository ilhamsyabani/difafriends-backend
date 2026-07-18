<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'attendance_session_id' => AttendanceSession::factory(),
            'name' => $name,
            'data' => [
                'nama' => $name,
                'no_hp' => $this->faker->phoneNumber(),
                'email' => $this->faker->safeEmail(),
            ],
            'signature_path' => null,
            'ip_address' => $this->faker->ipv4(),
            'submitted_at' => now(),
        ];
    }
}
