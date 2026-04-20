<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\TutorSchedule;
use App\Models\User;
use App\Notifications\BookingMeetingLinkNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->companion = User::factory()->companion()->create();
    $this->student = User::factory()->create();

    $schedule = TutorSchedule::create([
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

    $this->booking = Booking::create([
        'tutor_id' => $this->companion->id,
        'student_id' => $this->student->id,
        'tutor_schedule_id' => $schedule->id,
        'start_at' => now()->addDays(3),
        'end_at' => now()->addDays(3)->addHour(),
        'type' => 'online',
        'status' => BookingStatus::Confirmed,
    ]);
});

test('admin dapat menyimpan meeting link', function () {
    Notification::fake();

    $this->actingAs($this->admin)
        ->post("/admin/bookings/{$this->booking->id}/meeting", [
            'meeting_link' => 'https://zoom.us/j/123456789',
        ])
        ->assertRedirect();

    expect($this->booking->fresh()->meeting_link)->toBe('https://zoom.us/j/123456789');
});

test('menyimpan meeting link mengirim notifikasi ke student dan tutor', function () {
    Notification::fake();

    $this->actingAs($this->admin)
        ->post("/admin/bookings/{$this->booking->id}/meeting", [
            'meeting_link' => 'https://zoom.us/j/123456789',
        ]);

    Notification::assertSentTo($this->student, BookingMeetingLinkNotification::class);
    Notification::assertSentTo($this->companion, BookingMeetingLinkNotification::class);
});

test('meeting link wajib berupa URL yang valid', function () {
    Notification::fake();

    $this->actingAs($this->admin)
        ->post("/admin/bookings/{$this->booking->id}/meeting", [
            'meeting_link' => 'bukan-url',
        ])
        ->assertSessionHasErrors('meeting_link');

    Notification::assertNothingSent();
});

test('meeting link tidak boleh kosong', function () {
    Notification::fake();

    $this->actingAs($this->admin)
        ->post("/admin/bookings/{$this->booking->id}/meeting", [
            'meeting_link' => '',
        ])
        ->assertSessionHasErrors('meeting_link');

    Notification::assertNothingSent();
});
