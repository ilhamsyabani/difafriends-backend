<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            CourseStatus::Draft => 'Draft',
            CourseStatus::Review => 'Menunggu Review',
            CourseStatus::Published => 'Dipublikasikan',
            CourseStatus::Archived => 'Diarsipkan',
        };
    }

    public function isVisible(): bool
    {
        return $this === CourseStatus::Published;
    }

    public function canBeEdited(): bool
    {
        return in_array($this, [
            CourseStatus::Draft,
            CourseStatus::Review,
        ]);
    }
}
