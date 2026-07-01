<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_saat_itu',
        'jenis_aktivitas',
        'detail',
        'document_id',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Helper statis untuk mencatat log aktivitas dengan cepat.
     */
    public static function log(string $jenis, string $detail, ?string $documentId = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'role_saat_itu' => auth()->check() ? auth()->user()->role : null,
            'jenis_aktivitas' => $jenis,
            'detail' => $detail,
            'document_id' => $documentId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    // ─── Scopes ────────────────────────────────────────────

    public function scopeWithEagerLoading($query)
    {
        return $query->with('user');
    }

    // ─── Relationships ─────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
