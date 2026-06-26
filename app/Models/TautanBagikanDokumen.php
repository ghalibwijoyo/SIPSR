<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentShareLink extends Model
{
    use HasUuids;

    protected $fillable = [
        'document_id',
        'token',
        'created_by_id',
        'expired_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    // ─── Relationships ─────────────────────────────────────

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
