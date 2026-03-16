<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'file_path',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    // Cari sertifikat berdasarkan nomor — untuk halaman verifikasi
    public function scopeByNumber($query, string $number)
    {
        return $query->where('certificate_number', $number);
    }

    // ── Accessors ──────────────────────────────────────────

    // URL download PDF sertifikat
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path
            ? asset('storage/' . $this->file_path)
            : null;
    }

    // URL halaman verifikasi publik
    public function getVerifyUrlAttribute(): string
    {
        return url('/verify/' . $this->certificate_number);
    }

    // ── Helpers ────────────────────────────────────────────

    public function hasFile(): bool
    {
        return !is_null($this->file_path);
    }

    // Generate format nomor sertifikat yang konsisten
    public static function generateNumber(): string
    {
        return 'CERT-' . date('Y') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
    }
}
