<?php

namespace App\Models;

use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TutorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'session_duration',
        'break_time',
        'price',
        'max_participants',
        'is_active',
    ];

    protected $casts = [
        'day_of_week'      => DayOfWeek::class,
        'price'            => 'decimal:2',
        'is_active'        => 'boolean',
        'session_duration' => 'integer',
        'break_time'       => 'integer',
        'max_participants' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'tutor_schedule_id');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay(Builder $query, DayOfWeek $day): Builder
    {
        return $query->where('day_of_week', $day->value);
    }

    // ── Helpers ────────────────────────────────────────────

    // Total durasi slot termasuk break
    public function getTotalSlotMinutesAttribute(): int
    {
        return $this->session_duration + $this->break_time;
    }

    // Label hari yang readable
    public function getDayLabelAttribute(): string
    {
        return $this->day_of_week->label();
    }

    // Apakah jadwal ini di akhir pekan?
    public function isWeekend(): bool
    {
        return $this->day_of_week->isWeekend();
    }

    // Cek apakah slot waktu tertentu masih tersedia
    public function isAvailableOn(string $date): bool
    {
        $bookingsCount = $this->bookings()
            ->whereDate('start_at', $date)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        return $bookingsCount < $this->max_participants;
    }
}
