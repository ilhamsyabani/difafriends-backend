<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $plainPassword,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Akun DifaFriends Anda telah dibuat')
            ->greeting("Halo, {$notifiable->first_name}!")
            ->line('Sebuah akun telah dibuatkan untuk Anda di platform DifaFriends. Berikut kredensial untuk masuk:')
            ->line("**Email:** {$notifiable->email}")
            ->line('**Password sementara:**')
            ->line("`{$this->plainPassword}`")
            ->line('')
            ->line('Silakan salin password di atas dan tempel saat login.')
            ->action('Masuk Sekarang', route('login'))
            ->line('Demi keamanan, segera ganti password Anda setelah login pertama kali.')
            ->salutation('Salam, Tim DifaFriends');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
