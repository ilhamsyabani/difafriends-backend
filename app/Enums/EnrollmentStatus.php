<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            EnrollmentStatus::Active => 'Aktif',
            EnrollmentStatus::Expired => 'Kedaluwarsa',
            EnrollmentStatus::Completed => 'Selesai',
        };
    }

    // Completed tetap bisa akses materi untuk review
    public function isAccessible(): bool
    {
        return in_array($this, [
            EnrollmentStatus::Active,
            EnrollmentStatus::Completed,
        ]);
    }
}
