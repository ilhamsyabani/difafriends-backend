<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

        protected $fillable = [
            'author_id', 'title', 'slug', 'thumbnail', 'content', 'status'
        ];

        public function author(): BelongsTo
        {
            return $this->belongsTo(User::class, 'author_id');
        }
}
