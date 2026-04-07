<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Quiz;
use App\Models\CourseLecture;
use App\Models\Course;

class CourseSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lectures(): HasMany
    {
        return $this->hasMany(CourseLecture::class, 'section_id')
            ->orderBy('sort_order');
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class, 'section_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Total durasi semua lecture dalam section ini
    public function getTotalDurationAttribute(): int
    {
        return $this->lectures->sum('video_duration');
    }
}
