<?php

namespace App\Enums;

enum SessionType: string
{
    case Online = 'online';
    case Offline = 'offline';

    public function label(): string
    {
        return match ($this) {
            SessionType::Online => 'Online',
            SessionType::Offline => 'Tatap Muka',
        };
    }

    public function needsMeetingLink(): bool
    {
        return $this === SessionType::Online;
    }
}
