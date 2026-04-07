<?php

namespace App\Enums;

enum QuizQuestionType: string
{
    case MultipleChoice = 'multiple_choice';
    case Essay          = 'essay';
}
