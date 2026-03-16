<?php

namespace App\Enums;

enum OrderStatus :string
{
    case Pending   = 'pending';
    case Paid      = 'paid';
    case Expired   = 'expired';
    case Cancelled = 'cancelled';
    case Refunded  = 'refunded';

    public function label(): string
    {
        return match($this) {
            OrderStatus::Pending   => 'Menunggu Pembayaran',
            OrderStatus::Paid      => 'Lunas',
            OrderStatus::Expired   => 'Kedaluwarsa',
            OrderStatus::Cancelled => 'Dibatalkan',
            OrderStatus::Refunded  => 'Dikembalikan',
        };
    }

    public function isPaid(): bool
    {
        return $this === OrderStatus::Paid;
    }

    public function isActive(): bool
    {
        return in_array($this, [OrderStatus::Pending, OrderStatus::Paid]);
    }
}
