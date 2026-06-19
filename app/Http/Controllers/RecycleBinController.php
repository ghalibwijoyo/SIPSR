<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecycleBinController extends Controller
{
    // LIST dokumen yang dihapus
    public function index(Request $request)
    {
        $query = Document::with(['category', 'deletedBy'])
            ->onlyTrashed();

        // ── Filter: category ────────────────────────────────
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // ── Filter: date range ──────────────────────────────
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('deleted_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('deleted_at', '<=', $request->tanggal_sampai);
        }

        $documents = $query->orderBy('deleted_at', 'desc')->paginate(20)->withQueryString();
        $categories = Category::orderBy('nama')->get();

        return view('recycle-bin.index', compact('documents', 'categories'));
    }

    // RESTORE dokumen
    public function restore($id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);

        $document->restore();
        $document->update([
            'deleted_by_id' => null,
        ]);

        // Activity log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'role_saat_itu' => Auth::user()->role,
            'jenis_aktivitas' => 'RESTORE_DOKUMEN',
            'detail' => "Restore: {$document->nama_dokumen}",
            'document_id' => $document->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil dipulihkan.');
    }

    // HAPUS PERMANEN (Admin only)
    public function destroy($id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);
        $namaDokumen = $document->nama_dokumen;
        $documentId = $document->id;

        // Hapus file fisik
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        // Hapus record
        $document->forceDelete();

        // Activity log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'role_saat_itu' => Auth::user()->role,
            'jenis_aktivitas' => 'HAPUS_PERMANEN',
            'detail' => "Hapus Permanen: {$namaDokumen}",
            'document_id' => $documentId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus permanen.');
    }

    // KOSONGKAN RECYCLE BIN (Admin only)
    public function empty()
    {
        $documents = Document::onlyTrashed()->get();
        $count = $documents->count();

        foreach ($documents as $doc) {
            if (Storage::disk('local')->exists($doc->file_path)) {
                Storage::disk('local')->delete($doc->file_path);
            }
            $doc->forceDelete();
        }

        // Activity log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'role_saat_itu' => Auth::user()->role,
            'jenis_aktivitas' => 'KOSONGKAN_RECYCLE_BIN',
            'detail' => "Kosongkan Recycle Bin: {$count} dokumen",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Recycle Bin berhasil dikosongkan.');
    }
}
