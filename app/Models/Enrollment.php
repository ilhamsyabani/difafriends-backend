<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'order_id',
        'status',
        'enrolled_at',
        'expired_at',
        'completed_at',
    ];

    protected $casts = [
        'status'       => EnrollmentStatus::class,
        'enrolled_at'  => 'datetime',
        'expired_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Progress semua lecture dalam enrollment ini
    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class, 'user_id', 'user_id')->where('course_id', $this->course_id);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', EnrollmentStatus::Active);
    }

    public function scopeAccessible(Builder $query): Builder
    {
        return $query->whereIn('status', [
            EnrollmentStatus::Active->value,
            EnrollmentStatus::Completed->value,
        ]);
    }

    // ── Helpers ────────────────────────────────────────────

    // Apakah masih boleh akses course?
    public function isAccessible(): bool
    {
        return $this->status->isAccessible() && ($this->expired_at === null || now()->lessThan($this->expired_at));
    }

    public function isCompleted(): bool
    {
        return $this->status === EnrollmentStatus::Completed;
    }

    public function isExpired(): bool
    {
        return $this->status === EnrollmentStatus::Expired
            || ($this->expired_at && now()->greaterThan($this->expired_at));
    }

    // Hitung persentase progress (0-100)
    public function getProgressPercentageAttribute(): int
    {
        $totalLectures = $this->course->lectures()->count();

        if ($totalLectures === 0) return 0;

        $completedLectures = CourseProgress::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->where('is_completed', true)
            ->count();

        return (int) round(($completedLectures / $totalLectures) * 100);
    }
}
