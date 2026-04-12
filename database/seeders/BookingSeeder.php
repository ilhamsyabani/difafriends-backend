<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Enums\Roles;
use App\Enums\SessionType;
use App\Models\Booking;
use App\Models\Order;
use App\Models\TutorSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', Roles::User->value)->get();
        $schedules = TutorSchedule::with('tutor')->where('is_active', true)->get();

        if ($schedules->isEmpty()) {
            return;
        }

        $statusWeights = [
            BookingStatus::Completed->value => 50,
            BookingStatus::Confirmed->value => 25,
            BookingStatus::Pending->value => 15,
            BookingStatus::Cancelled->value => 10,
        ];

        $usedSlots = [];

        foreach (range(1, 20) as $i) {
            $schedule = $schedules->random();
            $student = $students->random();

            // Tentukan tanggal sesi berdasarkan hari dalam jadwal
            // Offset tiap iterasi agar slot tidak bentrok pada tutor yang sama
            $dayOfWeek = $schedule->day_of_week->value;
            $baseDate = now()->subWeeks(rand(1, 24));
            $diff = ($dayOfWeek - $baseDate->dayOfWeek + 7) % 7;
            $sessionDate = $baseDate->copy()->addDays($diff);

            [$startHour, $startMin] = explode(':', $schedule->start_time);
            $startAt = $sessionDate->copy()->setHour((int) $startHour)->setMinute((int) $startMin)->setSecond(0);
            $endAt = $startAt->copy()->addMinutes($schedule->session_duration);

            // Hindari duplikat slot tutor yang sama
            $slotKey = $schedule->tutor_id.'-'.$startAt->format('Y-m-d H:i');
            if (isset($usedSlots[$slotKey])) {
                continue;
            }
            $usedSlots[$slotKey] = true;

            $status = $this->weightedRandom($statusWeights);

            $booking = Booking::create([
                'student_id' => $student->id,
                'tutor_id' => $schedule->tutor_id,
                'tutor_schedule_id' => $schedule->id,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'type' => SessionType::Online->value,
                'status' => $status,
                'meeting_link' => $status !== BookingStatus::Cancelled->value
                    ? 'https://meet.google.com/'.fake()->regexify('[a-z]{3}-[a-z]{4}-[a-z]{3}')
                    : null,
                'notes' => fake()->boolean(60)
                    ? 'Mohon persiapkan materi tentang '.fake()->words(3, true)
                    : null,
                'confirmed_at' => in_array($status, [
                    BookingStatus::Confirmed->value,
                    BookingStatus::Completed->value,
                ]) ? $startAt->copy()->subDays(1) : null,
                'completed_at' => $status === BookingStatus::Completed->value ? $endAt : null,
                'cancelled_at' => $status === BookingStatus::Cancelled->value ? $startAt->copy()->subHours(2) : null,
                'created_at' => $startAt->copy()->subDays(rand(1, 7)),
                'updated_at' => $startAt,
            ]);

            // Buat order untuk booking yang sudah confirmed/completed
            if (in_array($status, [BookingStatus::Confirmed->value, BookingStatus::Completed->value])) {
                Order::create([
                    'user_id' => $student->id,
                    'orderable_type' => Booking::class,
                    'orderable_id' => $booking->id,
                    'item_name' => 'Sesi dengan '.$schedule->tutor->first_name.' '.$schedule->tutor->last_name,
                    'original_price' => $schedule->price,
                    'final_amount' => $schedule->price,
                    'status' => OrderStatus::Paid->value,
                    'invoice_number' => 'INV-'.strtoupper(fake()->bothify('????-####')),
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->created_at,
                ]);
            }
        }
    }

    /**
     * Pilih random berdasarkan bobot.
     *
     * @param  array<string, int>  $weights
     */
    private function weightedRandom(array $weights): string
    {
        $total = array_sum($weights);
        $rand = rand(1, $total);
        $cumulative = 0;
        foreach ($weights as $key => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $key;
            }
        }

        return array_key_first($weights);
    }
}
