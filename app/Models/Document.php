<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasUuids, SoftDeletes, Prunable;

    protected $fillable = [
        'nomor_dokumen',
        'nama_dokumen',
        'category_id',
        'tanggal_dokumen',
        'deskripsi',
        'file_path',
        'file_name',
        'hasil_ocr',
        'uploader_id',
        'updated_by_id',
        'deleted_by_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_dokumen' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the prunable model query.
     * Documents that have been in the recycle bin for more than 30 days will be permanently deleted.
     */
    public function prunable()
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(30));
    }

    // ─── Relationships ─────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DocumentHistory::class);
    }

    public function shareLinks(): HasMany
    {
        return $this->hasMany(DocumentShareLink::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
