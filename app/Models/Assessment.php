<?php

namespace App\Models;

use App\Enums\AssessmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
     protected $fillable = [
        'user_id',
        'assessment_type',
        'child_name',
        'child_age',
        'scores_json',
    ];

    protected $casts = [
        'assessment_type' => AssessmentType::class,
        'scores_json'     => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
