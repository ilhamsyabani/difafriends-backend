<?php

namespace Database\Seeders;

use App\Enums\DayOfWeek;
use App\Enums\Roles;
use App\Models\TutorSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $companions = User::where('role', Roles::Companion->value)
            ->where('is_active', true)
            ->get();

        // Template jadwal yang realistis — variasi hari dan harga
        $scheduleTemplates = [
            // Weekday pagi
            ['day' => DayOfWeek::Monday, 'start' => '08:00', 'end' => '12:00', 'duration' => 60, 'break' => 15, 'price' => 150000],
            ['day' => DayOfWeek::Tuesday, 'start' => '08:00', 'end' => '12:00', 'duration' => 60, 'break' => 15, 'price' => 150000],
            ['day' => DayOfWeek::Wednesday, 'start' => '09:00', 'end' => '13:00', 'duration' => 90, 'break' => 15, 'price' => 200000],
            ['day' => DayOfWeek::Thursday, 'start' => '08:00', 'end' => '12:00', 'duration' => 60, 'break' => 15, 'price' => 150000],
            ['day' => DayOfWeek::Friday, 'start' => '08:00', 'end' => '11:00', 'duration' => 60, 'break' => 0, 'price' => 150000],
            // Weekend
            ['day' => DayOfWeek::Saturday, 'start' => '08:00', 'end' => '12:00', 'duration' => 60, 'break' => 15, 'price' => 175000],
            ['day' => DayOfWeek::Saturday, 'start' => '13:00', 'end' => '17:00', 'duration' => 90, 'break' => 15, 'price' => 200000],
            // Weekday siang/sore
            ['day' => DayOfWeek::Monday, 'start' => '14:00', 'end' => '18:00', 'duration' => 60, 'break' => 15, 'price' => 175000],
            ['day' => DayOfWeek::Wednesday, 'start' => '14:00', 'end' => '17:00', 'duration' => 60, 'break' => 0, 'price' => 175000],
            ['day' => DayOfWeek::Friday, 'start' => '13:00', 'end' => '17:00', 'duration' => 90, 'break' => 15, 'price' => 225000],
        ];

        foreach ($companions as $companion) {
            // Tiap companion mendapat 3-4 jadwal acak
            $picked = collect($scheduleTemplates)->shuffle()->take(rand(3, 4));

            foreach ($picked as $tpl) {
                TutorSchedule::firstOrCreate(
                    [
                        'tutor_id' => $companion->id,
                        'day_of_week' => $tpl['day']->value,
                        'start_time' => $tpl['start'],
                    ],
                    [
                        'end_time' => $tpl['end'],
                        'session_duration' => $tpl['duration'],
                        'break_time' => $tpl['break'],
                        'price' => $tpl['price'],
                        'max_participants' => 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
