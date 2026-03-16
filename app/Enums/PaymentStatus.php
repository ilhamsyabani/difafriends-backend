<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending    = 'pending';
    case Settlement = 'settlement';
    case Expired    = 'expired';
    case Cancel     = 'cancel';
    case Fraud      = 'fraud';
    case Deny       = 'deny';

    public function label(): string
    {
        return match($this) {
            PaymentStatus::Pending    => 'Menunggu',
            PaymentStatus::Settlement => 'Berhasil',
            PaymentStatus::Expired    => 'Kedaluwarsa',
            PaymentStatus::Cancel     => 'Dibatalkan',
            PaymentStatus::Fraud      => 'Terindikasi Fraud',
            PaymentStatus::Deny       => 'Ditolak',
        };
    }

    public function isSuccess(): bool
    {
        return $this === PaymentStatus::Settlement;
    }

    public function isFailed(): bool
    {
        return in_array($this, [
            PaymentStatus::Expired,
            PaymentStatus::Cancel,
            PaymentStatus::Fraud,
            PaymentStatus::Deny,
        ]);
    }
}
