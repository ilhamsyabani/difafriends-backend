<?php

use App\Models\Booking;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingMeetingLinkNotification;
use App\Notifications\CourseApprovedNotification;
use Illuminate\Support\Facades\Notification;

// ── CourseApprovedNotification ─────────────────────────────────────────────

test('admin approve kelas mengirim notifikasi ke instruktur', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
        'status' => 'review',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.courses.approve', $course))
        ->assertRedirect();

    Notification::assertSentTo($instructor, CourseApprovedNotification::class);
});

test('CourseApprovedNotification menggunakan channel mail dan database', function () {
    Notification::fake();

    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'instructor_id' => $instructor->id,
        'category_id' => $category->id,
    ]);

    $instructor->notify(new CourseApprovedNotification($course));

    Notification::assertSentTo(
        $instructor,
        CourseApprovedNotification::class,
        fn ($notification, $channels) => in_array('mail', $channels) && in_array('database', $channels)
    );
});

// ── BookingConfirmedNotification ───────────────────────────────────────────

test('BookingConfirmedNotification menggunakan channel mail dan database', function () {
    Notification::fake();

    $student = User::factory()->create();
    $booking = Booking::factory()->confirmed()->create(['student_id' => $student->id]);

    $student->notify(new BookingConfirmedNotification($booking));

    Notification::assertSentTo(
        $student,
        BookingConfirmedNotification::class,
        fn ($notification, $channels) => in_array('mail', $channels) && in_array('database', $channels)
    );
});

// ── BookingMeetingLinkNotification ─────────────────────────────────────────

test('generateMeet mengirim notifikasi ke student dan tutor', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $booking = Booking::factory()->confirmed()->create([
        'meeting_link' => null,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.bookings.meeting', $booking), [
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
        ])
        ->assertRedirect();

    Notification::assertSentTo($booking->student, BookingMeetingLinkNotification::class);
    Notification::assertSentTo($booking->tutor, BookingMeetingLinkNotification::class);
});

test('BookingMeetingLinkNotification hanya menggunakan channel database', function () {
    Notification::fake();

    $student = User::factory()->create();
    $booking = Booking::factory()->confirmed()->create(['student_id' => $student->id]);

    $student->notify(new BookingMeetingLinkNotification($booking));

    Notification::assertSentTo(
        $student,
        BookingMeetingLinkNotification::class,
        fn ($notification, $channels) => $channels === ['database']
    );
});
