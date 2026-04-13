<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'essay_answer',
        'points_earned',
        'instructor_note',
    ];

    protected function casts(): array
    {
        return [
            'points_earned' => 'integer',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuizOption::class, 'selected_option_id');
    }

    // ── Helpers ───────────────────────────────────────────

    public function isCorrect(): bool
    {
        if (! $this->question->isMultipleChoice()) {
            return false;
        }
        if (! $this->selected_option_id) {
            return false;
        }

        return $this->selectedOption?->is_correct ?? false;
    }
}
