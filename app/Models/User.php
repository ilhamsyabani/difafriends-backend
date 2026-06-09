<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class User extends Authenticatable
{
    use HasFactory, LogsActivity, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'role', 'is_active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "User {$eventName}");
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'google_id',
        'photo',
        'bio',
        'phone',
        'birth_date',
        'gender',
        'address',
        'city',
        'country',
        // 'role' dan 'is_active' SENGAJA tidak ada di sini.
        // Keduanya hanya boleh diubah melalui metode khusus (assignRole, activate, deactivate)
        // untuk mencegah mass-assignment privilege escalation.
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_confirmed_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'role' => Roles::class,
    ];

    // ── Relationships: sebagai User/Student ───────────────

    // Semua order yang pernah dibuat user ini
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Semua kelas yang sudah dibeli/diikuti
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // Progress belajar di semua lecture
    public function progress(): HasMany
    {
        return $this->hasMany(CourseProgress::class);
    }

    // Semua sertifikat yang sudah didapat
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    // Semua review yang pernah ditulis
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Booking sebagai student
    public function bookingsAsStudent(): HasMany
    {
        return $this->hasMany(Booking::class, 'student_id');
    }

    // ── Relationships: sebagai Instructor ─────────────────

    // Kelas yang dibuat sebagai instruktur
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    // ── Relationships: sebagai Companion ──────────────────

    // Jadwal ketersediaan sebagai guru pendamping
    public function schedules(): HasMany
    {
        return $this->hasMany(TutorSchedule::class, 'tutor_id');
    }

    // Booking yang diterima sebagai tutor/companion
    public function bookingsAsTutor(): HasMany
    {
        return $this->hasMany(Booking::class, 'tutor_id');
    }

    // ── Accessors ──────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->photo ? asset("storage/{$this->photo}") : "https://ui-avatars.com/api/?name={$this->full_name}&background=7B2D8B&color=fff";
    }

    // ── Role Helpers ───────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === Roles::Admin;
    }

    public function isInstructor(): bool
    {
        return $this->role === Roles::Instructor;
    }

    public function isCompanion(): bool
    {
        return $this->role === Roles::Companion;
    }

    public function isUser(): bool
    {
        return $this->role === Roles::User;
    }

    public function isProvider(): bool
    {
        return $this->role->isProvider();
    }

    // ── Privileged Setters (hanya dipanggil dari Admin/konteks terpercaya) ──

    public function assignRole(Roles $role): void
    {
        $this->forceFill(['role' => $role])->save();
    }

    public function activate(): void
    {
        $this->forceFill(['is_active' => true])->save();
    }

    public function deactivate(): void
    {
        $this->forceFill(['is_active' => false])->save();
    }

    // ── Enrollment Helpers ─────────────────────────────────

    public function isEnrolledIn(Course $course): bool
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->exists();
    }

    // Ambil enrollment untuk course tertentu
    public function enrollmentFor(Course $course): ?Enrollment
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->first();
    }
}
