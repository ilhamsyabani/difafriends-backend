<?php

namespace App\Enums;

enum DayOfWeek: int
{
    case Sunday = 0;
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;

    public function label(): string
    {
        return match ($this) {
            DayOfWeek::Sunday => 'Minggu',
            DayOfWeek::Monday => 'Senin',
            DayOfWeek::Tuesday => 'Selasa',
            DayOfWeek::Wednesday => 'Rabu',
            DayOfWeek::Thursday => 'Kamis',
            DayOfWeek::Friday => 'Jumat',
            DayOfWeek::Saturday => 'Sabtu',
        };
    }

    public function isWeekend(): bool
    {
        return in_array($this, [DayOfWeek::Saturday, DayOfWeek::Sunday]);
    }

    public function isWeekday(): bool
    {
        return ! $this->isWeekend();
    }
}
