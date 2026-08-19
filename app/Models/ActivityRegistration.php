<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Database\Factories\ActivityRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityRegistration extends Model
{
    /** @use HasFactory<ActivityRegistrationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'order_id',
        'linkid_transaction_id',
        'linkid_response',
        'registered_at',
    ];

    protected $casts = [
        'linkid_response' => 'array',
        'registered_at' => 'datetime',
        'status' => RegistrationStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
