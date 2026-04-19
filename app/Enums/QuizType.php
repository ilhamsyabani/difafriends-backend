<?php

namespace App\Enums;

enum QuizType: string
{
    case Pretest = 'pretest';
    case Quiz = 'quiz';
    case Posttest = 'posttest';

    public function label(): string
    {
        return match ($this) {
            QuizType::Pretest => 'Pre-Test',
            QuizType::Quiz => 'Kuis',
            QuizType::Posttest => 'Post-Test',
        };
    }
}
