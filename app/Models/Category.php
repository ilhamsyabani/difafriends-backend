<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relasi ke semua anak-anaknya
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Scope: ambil hanya kategori utama (root)
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    // Cek apakah ini kategori utama
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }
}

$rootCategories = Category::root()->with('children')->get();
