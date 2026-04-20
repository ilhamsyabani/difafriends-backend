<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Course $course) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Kelas \"{$this->course->title}\" Sudah Dipublikasikan!")
            ->greeting("Halo, {$notifiable->first_name}!")
            ->line('Kabar baik! Kelas yang kamu ajukan sudah direview dan disetujui oleh tim admin.')
            ->line("**Kelas:** {$this->course->title}")
            ->action('Lihat Kelas', url("/courses/{$this->course->slug}"))
            ->line('Sekarang kelas kamu sudah bisa ditemukan dan dibeli oleh siswa.')
            ->salutation('Salam, Tim DifaFriends');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'course_approved',
            'title' => 'Kelas Disetujui!',
            'message' => "Kelas \"{$this->course->title}\" sudah dipublikasikan.",
            'url' => "/courses/{$this->course->slug}",
            'icon' => 'book-open',
        ];
    }
}
