<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\AttendanceForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceForm>
 */
class AttendanceFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'activity_id' => Activity::factory(),
            'title' => 'Absensi '.$this->faker->word(),
            'fields' => [
                ['key' => 'nama', 'label' => 'Nama Lengkap (untuk sertifikat)', 'type' => 'text', 'required' => true],
                ['key' => 'no_hp', 'label' => 'No. HP', 'type' => 'phone', 'required' => true],
                ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['key' => 'ttd', 'label' => 'Tanda Tangan', 'type' => 'signature', 'required' => true],
            ],
        ];
    }
}
