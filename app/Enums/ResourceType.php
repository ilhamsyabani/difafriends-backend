<?php

namespace App\Enums;

enum ResourceType:string
{
    case File  = 'file';
    case Link  = 'link';
    case Video = 'video';

    public function label(): string
    {
        return match($this) {
            ResourceType::File  => 'File Download',
            ResourceType::Link  => 'Link Eksternal',
            ResourceType::Video => 'Video',
        };
    }

    public function isDownloadable(): bool
    {
        return $this === ResourceType::File;
    }
}
