<?php

namespace App\Enums;

enum AssessmentType :string
{
    case Kesiapan = 'kesiapan';
    case Pemilihan = 'pemilihan';

    public function label(): string
    {
        return match ($this) {
            AssessmentType::Kesiapan => 'Kesiapan',
            AssessmentType::Pemilihan => 'Pemilihan',
        };
    }
}


