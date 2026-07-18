<?php

namespace App\Models;

use Database\Factories\AttendanceSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AttendanceSession extends Model
{
    /** @use HasFactory<AttendanceSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'attendance_form_id',
        'session_date',
        'token',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'is_open' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AttendanceSession $session) {
            if (empty($session->token)) {
                $session->token = Str::random(32);
            }
        });
    }

    // ── Route Binding ──────────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'token';
    }

    // ── Relationships ──────────────────────────────────────

    public function attendanceForm(): BelongsTo
    {
        return $this->belongsTo(AttendanceForm::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
