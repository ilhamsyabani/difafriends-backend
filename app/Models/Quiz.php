<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'section_id',
        'title',
        'description',
        'is_required',
        'passing_score',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'passing_score' => 'integer',
        ];
    }

    // ── Relasi ────────────────────────────────────────────

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany // ✅ relasi yang kurang
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ── Helpers ───────────────────────────────────────────

    public function totalPoints(): int // ✅ helper berguna
    {
        return $this->questions()->sum('points');
    }

    public function attemptByUser(int $userId): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->latest()
            ->first();
    }
}
