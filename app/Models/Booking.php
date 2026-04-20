<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Enums\SessionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Booking extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'zoom_link', 'start_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Booking {$eventName}");
    }

    protected $fillable = [
        'student_id',
        'tutor_id',
        'tutor_schedule_id',
        'start_at',
        'end_at',
        'type',
        'status',
        'meeting_link',
        'notes',
        'session_notes',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'status' => BookingStatus::class,
        'type' => SessionType::class,
    ];

    // ── Relationships ──────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(TutorSchedule::class, 'tutor_schedule_id');
    }

    // Polymorphic — booking bisa di-review
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Polymorphic — booking bisa jadi item di orders
    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    // Order aktif untuk booking ini
    public function activeOrder(): MorphOne
    {
        return $this->morphOne(Order::class, 'orderable')
            ->whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Paid->value,
            ]);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->where('start_at', '>', now())
            ->where('status', BookingStatus::Confirmed);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_at', today());
    }

    // ── Accessors ──────────────────────────────────────────

    // Durasi sesi dalam menit
    public function getDurationMinutesAttribute(): int
    {
        return $this->start_at->diffInMinutes($this->end_at);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === BookingStatus::Pending;
    }

    public function isConfirmed(): bool
    {
        return $this->status === BookingStatus::Confirmed;
    }

    public function isCompleted(): bool
    {
        return $this->status === BookingStatus::Completed;
    }

    public function isCancelled(): bool
    {
        return $this->status === BookingStatus::Cancelled;
    }

    // Pakai method dari Enum — tidak perlu tulis logic lagi
    public function isCancellable(): bool
    {
        return $this->status->isCancellable();
    }
}
