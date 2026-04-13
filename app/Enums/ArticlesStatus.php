<?php

namespace App\Enums;

enum ArticlesStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            ArticlesStatus::Draft => 'Draft',
            ArticlesStatus::Review => 'Menunggu Review',
            ArticlesStatus::Published => 'Dipublikasikan',
            ArticlesStatus::Archived => 'Diarsipkan',
        };
    }

    public function isVisible(): bool
    {
        return $this === ArticlesStatus::Published;
    }

    public function canBeEdited(): bool
    {
        return in_array($this, [
            ArticlesStatus::Draft,
            ArticlesStatus::Review,
        ]);
    }
}
