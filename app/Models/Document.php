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
    use HasUuids, Prunable, SoftDeletes;

    protected $fillable = [
        'nomor_dokumen',
        'nama_dokumen',
        'bank_id',
        'category_id',
        'tanggal_dokumen',
        'deskripsi',
        'file_path',
        'file_name',
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

    // ─── Query Scopes ──────────────────────────────────────

    public function scopeSearch($query, $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nomor_dokumen', 'LIKE', "%{$search}%")
                ->orWhere('nama_dokumen', 'LIKE', "%{$search}%")
                ->orWhereHas('uploader', function ($q2) use ($search) {
                    $q2->where('nama_lengkap', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('category', function ($q2) use ($search) {
                    $q2->where('nama', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('bank', function ($q2) use ($search) {
                    $q2->where('nama', 'LIKE', "%{$search}%");
                });
        });
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $categoryId ? $query->where('category_id', $categoryId) : $query;
    }

    public function scopeByBank($query, $bankId)
    {
        return $bankId ? $query->where('bank_id', $bankId) : $query;
    }

    public function scopeByUploader($query, $uploaderId)
    {
        return $uploaderId ? $query->where('uploader_id', $uploaderId) : $query;
    }

    public function scopeDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('tanggal_dokumen', '>=', $from);
        }
        if ($to) {
            $query->whereDate('tanggal_dokumen', '<=', $to);
        }

        return $query;
    }

    public function scopeByFormat($query, $formats)
    {
        if (! $formats || empty($formats)) {
            return $query;
        }

        return $query->where(function ($q) use ($formats) {
            foreach ($formats as $format) {
                if ($format === 'pdf') {
                    $q->orWhere('file_name', 'LIKE', '%.pdf');
                } elseif ($format === 'doc') {
                    $q->orWhere('file_name', 'LIKE', '%.doc')
                        ->orWhere('file_name', 'LIKE', '%.docx');
                }
            }
        });
    }

    // ─── Relationships ─────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
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
