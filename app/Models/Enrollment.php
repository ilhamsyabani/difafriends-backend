<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'status' => EnrollmentStatus::class,
        'enrolled_at' => 'datetime',
        'expired_at' => 'datetime',
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

    //Scope untuk hitungan  progeres
    public function scopeWithProgress(Builder $query):Builder {
        return $query->with(['course' => function ($query) {
            $query->withCount('lectures');
        }])
        ->whithCount([
            'courseProgresses as completed_lectures' => function ($query) {
                $query->where('is_completed', true);
            },
        ]);
    }

    // Hitung persentase progress (0-100
    public function getProgressPercentageAttribute(): int
    {
        $total = $this->course ? $this->course->lectures_count : 0;
        $completed = $this->completed_lectures;

        if ($total === 0) {
            return 0;
        }

        return (int) round(($completed / $total) * 100);
    }
}
