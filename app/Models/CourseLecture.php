<?php

namespace App\Models;

use App\Enums\LectureType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseLecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'section_id',
        'title',
        'type',
        'url',
        'content',
        'video_duration',
        'is_free_preview',
        'sort_order',
    ];

    protected $casts = [
        'type' => LectureType::class,
        'video_duration' => 'integer',
        'is_free_preview' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(CourseResource::class, 'lecture_id')
            ->orderBy('sort_order');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getFormattedDurationAttribute(): string
    {
        $seconds = $this->video_duration;
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        return $hours > 0 ? sprintf('%d:%02d:%02d', $hours, $minutes, $secs) : sprintf('%d:%02d', $minutes, $secs);
    }
}
