<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case Registered = 'registered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            RegistrationStatus::Registered => 'Terdaftar',
            RegistrationStatus::Cancelled => 'Dibatalkan',
        };
    }
}
