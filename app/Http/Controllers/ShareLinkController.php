<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\DocumentShareLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShareLinkController extends Controller
{
    /**
     * Generate share link baru untuk dokumen
     */
    public function store(Request $request, Document $dokumen)
    {
        $request->validate([
            'is_permanent' => 'nullable|boolean',
            'duration_hours' => 'nullable|integer|min:1|max:168',
        ]);

        $expiredAt = null;
        if (! $request->boolean('is_permanent')) {
            $hours = $request->input('duration_hours', 168); // default 7 hari
            $expiredAt = now()->addHours($hours);
        }

        $link = DocumentShareLink::create([
            'document_id' => $dokumen->id,
            'token' => Str::uuid(),
            'created_by_id' => Auth::id(),
            'expired_at' => $expiredAt,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'role_saat_itu' => Auth::user()->role,
            'jenis_aktivitas' => 'GENERATE_SHARE_LINK',
            'detail' => "Membuat Share Link: {$dokumen->nama_dokumen}",
            'document_id' => $dokumen->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Link berhasil dibuat',
            'data' => [
                'token' => $link->token,
                'url' => route('share.show', $link->token),
                'expired_at' => $link->expired_at ? $link->expired_at->format('d M Y H:i') : 'Tanpa batas waktu',
                'id' => $link->id,
            ],
        ]);
    }

    /**
     * Revoke share link
     */
    public function destroy(DocumentShareLink $link)
    {
        $link->update([
            'revoked_at' => now(),
        ]);

        $dokumen = $link->document;

        ActivityLog::create([
            'user_id' => Auth::id(),
            'role_saat_itu' => Auth::user()->role,
            'jenis_aktivitas' => 'REVOKE_SHARE_LINK',
            'detail' => "Mencabut Share Link: {$dokumen->nama_dokumen}",
            'document_id' => $dokumen->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Link berhasil dicabut.');
    }
}
