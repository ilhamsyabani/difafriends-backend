<?php

namespace App\Models;

use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Activity extends Model
{
    /** @use HasFactory<ActivityFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'location',
        'description',
        'price',
        'registration_code',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'price' => 'integer',
        ];
    }

    // ── Registration Code ─────────────────────────────────────

    /**
     * Encode amount untuk LinkId: price * 100 + registration_code.
     * Admin set harga 50000 + code 03 di LinkId dashboard → amount = 5000003
     */
    public function encodedAmount(): int
    {
        if (! $this->price || ! $this->registration_code) {
            return $this->price ?? 0;
        }

        return (int) ($this->price * 100 + (int) $this->registration_code);
    }

    /**
     * Decode registration code dari amount LinkId (2 digit terakhir).
     * Amount = 5000003 → code = 3
     * Amount = 5000032 → code = 32
     */
    public static function decodeRegistrationCode(int $amount): ?int
    {
        $code = (int) substr((string) $amount, -2);

        return $code > 0 ? $code : null;
    }

    // ── Relationships ──────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendanceForms(): HasMany
    {
        return $this->hasMany(AttendanceForm::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class);
    }

    // ── Accessors ──────────────────────────────────────────

    /**
     * Rentang tanggal kegiatan sebagai list tanggal harian.
     *
     * @return array<int, Carbon>
     */
    public function getDateRangeAttribute(): array
    {
        $dates = [];
        $cursor = $this->start_date->startOfDay();
        $end = $this->end_date->startOfDay();

        while ($cursor->lte($end)) {
            $dates[] = $cursor;
            $cursor = $cursor->addDay();
        }

        return $dates;
    }
}
