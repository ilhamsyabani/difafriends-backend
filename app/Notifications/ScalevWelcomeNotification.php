<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScalevWelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $plainPassword,
        public Course $course,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat datang di DifaFriends! Akun kamu sudah siap.')
            ->greeting("Halo, {$notifiable->first_name}!")
            ->line('Pembelian kamu di Scalev telah dikonfirmasi dan akun belajarmu sudah otomatis dibuat.')
            ->line("**Kelas:** {$this->course->title}")
            ->line("**Email:** {$notifiable->email}")
            ->line("**Password sementara:** `{$this->plainPassword}`")
            ->action('Mulai Belajar Sekarang', route('login'))
            ->line('Silakan ganti password setelah login pertama kali.')
            ->salutation('Salam, Tim DifaFriends');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
