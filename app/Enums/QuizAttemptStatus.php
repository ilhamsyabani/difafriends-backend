<?php

namespace App\Enums;

enum QuizAttemptStatus: string
{
    case Pending = 'pending';
    case Graded  = 'graded';
}
