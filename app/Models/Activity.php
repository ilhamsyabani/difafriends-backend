<?php

namespace App\Models;

use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Activity extends Model
{
    /** @use HasFactory<ActivityFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'location',
        'description',
        'linkid_product_uuid',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendanceForms(): HasMany
    {
        return $this->hasMany(AttendanceForm::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class);
    }

    // ── Accessors ──────────────────────────────────────────

    /**
     * Rentang tanggal kegiatan sebagai list tanggal harian.
     *
     * @return array<int, Carbon>
     */
    public function getDateRangeAttribute(): array
    {
        $dates = [];
        $cursor = $this->start_date->startOfDay();
        $end = $this->end_date->startOfDay();

        while ($cursor->lte($end)) {
            $dates[] = $cursor;
            $cursor = $cursor->addDay();
        }

        return $dates;
    }
}
