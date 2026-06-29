<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasUuids;

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('categories');
        });

        static::updated(function () {
            Cache::forget('categories');
        });

        static::deleted(function () {
            Cache::forget('categories');
        });
    }

    // ─── Relationships ─────────────────────────────────────

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
