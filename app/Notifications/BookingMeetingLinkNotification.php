<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BookingMeetingLinkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'booking_meeting_link',
            'title' => 'Link Meeting Tersedia!',
            'message' => "Link meeting untuk sesi pada {$this->booking->start_at->format('d M Y H:i')} sudah dikirim. Klik untuk lihat detail.",
            'url' => "/bookings/{$this->booking->id}",
            'icon' => 'video',
        ];
    }
}
