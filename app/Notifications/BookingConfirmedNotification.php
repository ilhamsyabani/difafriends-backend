<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Sesi Pendampingan Terkonfirmasi!')
            ->greeting("Halo, {$notifiable->first_name}!")
            ->line('Pembayaran kamu sudah diterima dan sesi pendampingan berhasil dikonfirmasi.')
            ->line("**Guru Pendamping:** {$this->booking->tutor->full_name}")
            ->line("**Tanggal & Waktu:** {$this->booking->start_at->format('d M Y, H:i')} WIB")
            ->line("**Durasi:** {$this->booking->schedule->session_duration} menit")
            ->action('Lihat Detail Booking', url("/bookings/{$this->booking->id}"))
            ->line('Link meeting akan dikirimkan mendekati waktu sesi.')
            ->salutation('Salam, Tim DifaFriends');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'booking_confirmed',
            'title' => 'Booking Terkonfirmasi!',
            'message' => "Sesi dengan {$this->booking->tutor->full_name} pada {$this->booking->start_at->format('d M Y H:i')} sudah dikonfirmasi.",
            'url' => "/bookings/{$this->booking->id}",
            'icon' => 'calendar',
        ];
    }
}
