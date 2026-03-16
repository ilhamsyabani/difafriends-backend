<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProgress extends Model
{
    use HasFactory;

    protected $table = 'course_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'lecture_id',
        'is_completed',
        'watch_seconds',
        'completed_at',
    ];

    protected $casts = [
        'is_completed'  => 'boolean',
        'watch_seconds' => 'integer',
        'completed_at'  => 'datetime',
    ];

    // ── Auto trigger setelah save ──────────────────────────

    protected static function booted(): void
    {
        static::saved(function (CourseProgress $progress) {
            // Hanya trigger kalau is_completed baru berubah jadi true
            if ($progress->is_completed && $progress->wasChanged('is_completed')) {
                $progress->checkCourseCompletion();
            }
        });
    }

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(CourseLecture::class, 'lecture_id');
    }

    // ── Helpers ────────────────────────────────────────────

    public function markCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
        // booted() akan otomatis trigger checkCourseCompletion()
    }

    public function addWatchTime(int $seconds): void
    {
        $this->increment('watch_seconds', $seconds);
    }

    // ── Private: Auto Certificate Logic ───────────────────

    private function checkCourseCompletion(): void
    {
        $totalLectures = CourseLecture::where('course_id', $this->course_id)
                                      ->count();

        if ($totalLectures === 0) return;

        $completedLectures = CourseProgress::where('user_id', $this->user_id)
                                           ->where('course_id', $this->course_id)
                                           ->where('is_completed', true)
                                           ->count();

        // Semua lecture sudah selesai!
        if ($completedLectures >= $totalLectures) {
            // Update enrollment jadi completed
            Enrollment::where('user_id', $this->user_id)
                      ->where('course_id', $this->course_id)
                      ->update([
                          'status'       => \App\Enums\EnrollmentStatus::Completed,
                          'completed_at' => now(),
                      ]);

            // Terbitkan sertifikat kalau course menyediakan
            $course = Course::find($this->course_id);
            if ($course?->has_certificate) {
                // Dispatch job — tidak blocking
                \App\Jobs\GenerateCertificateJob::dispatch(
                    $this->user_id,
                    $this->course_id
                );
            }
        }
    }
}
