<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    use HasUuids;

    protected $fillable = [
        'nama',
    ];

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('banks');
        });
        
        static::updated(function () {
            Cache::forget('banks');
        });
        
        static::deleted(function () {
            Cache::forget('banks');
        });
    }

    // ─── Relationships ─────────────────────────────────────

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
