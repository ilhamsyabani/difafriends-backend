<?php

namespace App\Models;

use Database\Factories\AttendanceFormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceForm extends Model
{
    /** @use HasFactory<AttendanceFormFactory> */
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'title',
        'fields',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }
}
