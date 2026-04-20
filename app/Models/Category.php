<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, SoftDeletes;

    // protected $guarded = [];
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'image',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Cek apakah ini kategori utama
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}

