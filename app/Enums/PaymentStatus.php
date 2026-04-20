<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Settlement = 'settlement';
    case Capture = 'capture';
    case Expired = 'expired';
    case Expire = 'expire';
    case Cancel = 'cancel';
    case Fraud = 'fraud';
    case Deny = 'deny';

    public function label(): string
    {
        return match ($this) {
            PaymentStatus::Pending => 'Menunggu',
            PaymentStatus::Settlement, PaymentStatus::Capture => 'Berhasil',
            PaymentStatus::Expired, PaymentStatus::Expire => 'Kedaluwarsa',
            PaymentStatus::Cancel => 'Dibatalkan',
            PaymentStatus::Fraud => 'Terindikasi Fraud',
            PaymentStatus::Deny => 'Ditolak',
        };
    }

    public function isSuccess(): bool
    {
        return in_array($this, [PaymentStatus::Settlement, PaymentStatus::Capture]);
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
