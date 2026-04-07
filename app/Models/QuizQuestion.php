<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'type',
        'question',
        'points',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'points'     => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class, 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'question_id');
    }

    // ── Helpers ───────────────────────────────────────────

    public function isMultipleChoice(): bool
    {
        return $this->type === 'multiple_choice';
    }

    public function isEssay(): bool
    {
        return $this->type === 'essay';
    }

    public function correctOption(): ?QuizOption
    {
        return $this->options()->where('is_correct', true)->first();
    }
}
