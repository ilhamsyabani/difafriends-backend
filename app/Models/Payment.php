<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'midtrans_transaction_id',
        'snap_token',
        'payment_type',
        'payment_bank',
        'amount',
        'status',
        'midtrans_response',
        'linkid_transaction_id',
        'linkid_response',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => PaymentStatus::class,
        'midtrans_response' => 'array',
        'linkid_response' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }

    public function isPaid(): bool
    {
        return $this->status->isSuccess();
    }

    public function isFailed(): bool
    {
        return $this->status->isFailed();
    }

    public function isExpired(): bool
    {
        return $this->status === PaymentStatus::Expired
            || ($this->expired_at && now()->greaterThan($this->expired_at));
    }

    // Ambil detail metode pembayaran yang readable
    public function getPaymentMethodAttribute(): string
    {
        return match ($this->payment_type) {
            'bank_transfer' => strtoupper($this->payment_bank ?? '').' Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'credit_card' => 'Kartu Kredit',
            default => ucfirst($this->payment_type ?? 'Unknown'),
        };
    }
}
