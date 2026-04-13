<?php

namespace App\Enums;

enum LectureType: string
{
    case Video = 'video';
    case Text = 'text';
    case Quiz = 'quiz';
    case Document = 'document'; // PDF embed

    public function label(): string
    {
        return match ($this) {
            LectureType::Video => 'Video',
            LectureType::Text => 'Artikel / Teks',
            LectureType::Quiz => 'Kuis',
            LectureType::Document => 'Dokumen PDF',
        };
    }

    public function hasVideo(): bool
    {
        return $this === LectureType::Video;
    }

    public function hasContent(): bool
    {
        return in_array($this, [LectureType::Text, LectureType::Quiz]);
    }
}
