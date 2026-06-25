<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasUuids;

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::updated(function () {
            Cache::forget('users_active');
        });
        
        static::created(function () {
            Cache::forget('users_active');
        });
    }

    // ─── Relationships ─────────────────────────────────────

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploader_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function documentHistories(): HasMany
    {
        return $this->hasMany(DocumentHistory::class, 'changed_by_id');
    }

    public function documentShareLinks(): HasMany
    {
        return $this->hasMany(DocumentShareLink::class, 'created_by_id');
    }
}
