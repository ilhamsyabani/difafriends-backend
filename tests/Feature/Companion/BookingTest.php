<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\TutorSchedule;
use App\Models\User;

beforeEach(function () {
    $this->companion = User::factory()->companion()->create();
    $this->student = User::factory()->create();

    $this->schedule = TutorSchedule::create([
        'tutor_id' => $this->companion->id,
        'day_of_week' => 1,
        'start_time' => '09:00',
        'end_time' => '11:00',
        'session_duration' => 60,
        'break_time' => 0,
        'price' => 150000,
        'max_participants' => 1,
        'is_active' => true,
    ]);
});

function buatBooking(User $companion, User $student, TutorSchedule $schedule, array $attrs = []): Booking
{
    // Gunakan offset menit acak agar start_at selalu unik per tutor
    $startAt = now()->addDays(3)->addMinutes(random_int(1, 50000));

    return Booking::create(array_merge([
        'tutor_id' => $companion->id,
        'student_id' => $student->id,
        'tutor_schedule_id' => $schedule->id,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'type' => 'online',
        'status' => BookingStatus::Confirmed,
    ], $attrs));
}

// ── Akses halaman ──────────────────────────────────────────────────────────

test('companion dapat mengakses halaman daftar booking', function () {
    $this->actingAs($this->companion)
        ->get(route('companion.bookings.index'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('companion/bookings/Index')
            ->has('bookings')
            ->has('filters')
        );
});

test('user biasa tidak dapat mengakses halaman booking companion', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('companion.bookings.index'))
        ->assertForbidden();
});

test('guest diarahkan ke login', function () {
    $this->get(route('companion.bookings.index'))
        ->assertRedirect('/login');
});

// ── Data booking ───────────────────────────────────────────────────────────

test('companion hanya melihat bookings miliknya sendiri', function () {
    $companion2 = User::factory()->companion()->create();
    $schedule2 = TutorSchedule::create([
        'tutor_id' => $companion2->id,
        'day_of_week' => 2,
        'start_time' => '10:00',
        'end_time' => '12:00',
        'session_duration' => 60,
        'break_time' => 0,
        'price' => 100000,
        'max_participants' => 1,
        'is_active' => true,
    ]);

    buatBooking($this->companion, $this->student, $this->schedule);
    buatBooking($companion2, $this->student, $schedule2); // bukan milik companion ini

    $this->actingAs($this->companion)
        ->get(route('companion.bookings.index'))
        ->assertInertia(fn ($page) => $page
            ->has('bookings.data', 1)
        );
});

test('companion dapat filter booking berdasarkan status', function () {
    buatBooking($this->companion, $this->student, $this->schedule, ['status' => BookingStatus::Confirmed]);
    buatBooking($this->companion, $this->student, $this->schedule, ['status' => BookingStatus::Completed]);
    buatBooking($this->companion, $this->student, $this->schedule, ['status' => BookingStatus::Cancelled]);

    $this->actingAs($this->companion)
        ->get(route('companion.bookings.index', ['status' => 'confirmed']))
        ->assertInertia(fn ($page) => $page
            ->has('bookings.data', 1)
            ->where('filters.status', 'confirmed')
        );
});

test('tanpa filter menampilkan semua booking', function () {
    buatBooking($this->companion, $this->student, $this->schedule, ['status' => BookingStatus::Confirmed]);
    buatBooking($this->companion, $this->student, $this->schedule, ['status' => BookingStatus::Completed]);

    $this->actingAs($this->companion)
        ->get(route('companion.bookings.index'))
        ->assertInertia(fn ($page) => $page
            ->has('bookings.data', 2)
        );
});
