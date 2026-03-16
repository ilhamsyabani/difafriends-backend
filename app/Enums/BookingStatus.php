<?php

namespace App\Enums;

enum BookingStatus:string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Ongoing   = 'ongoing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow    = 'no_show';

    public function label(): string
    {
        return match($this) {
            BookingStatus::Pending   => 'Menunggu Pembayaran',
            BookingStatus::Confirmed => 'Terkonfirmasi',
            BookingStatus::Ongoing   => 'Sedang Berlangsung',
            BookingStatus::Completed => 'Selesai',
            BookingStatus::Cancelled => 'Dibatalkan',
            BookingStatus::NoShow    => 'Tidak Hadir',
        };
    }

    public function isCancellable(): bool
    {
        return in_array($this, [
            BookingStatus::Pending,
            BookingStatus::Confirmed,
        ]);
    }
}
