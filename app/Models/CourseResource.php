<?php

namespace App\Models;

use App\Enums\ResourceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'lecture_id',
        'title',
        'type',
        'value',
        'file_size',
        'sort_order',
    ];

    protected $casts = [
        'type' => ResourceType::class,
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(CourseLecture::class, 'lecture_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getAccessUrlAttribute(): string
    {
        return match ($this->type) {
            ResourceType::File => asset('storage/'.$this->value),
            ResourceType::Link,
            ResourceType::Video => $this->value,
        };
    }

    public function isDownloadable(): bool
    {
        return $this->type->isDownloadable();
    }
}
