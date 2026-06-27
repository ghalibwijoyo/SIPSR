<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class RecycleBinController extends Controller
{
    // LIST dokumen yang dihapus
    public function index(Request $request)
    {
        $query = Document::with(['category', 'deletedBy'])
            ->onlyTrashed();

        // ── Filter: search ──────────────────────────────────
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // ── Filter: category ────────────────────────────────
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        // ── Filter: date range ──────────────────────────────
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            if ($request->tanggal_dari > $request->tanggal_sampai) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tanggal awal tidak boleh lebih besar dari tanggal akhir');
            }
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('deleted_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('deleted_at', '<=', $request->tanggal_sampai);
        }

        // ── Filter: trash_age ───────────────────────────────
        if ($request->filled('trash_age')) {
            if ($request->trash_age === 'new') {
                $query->where('deleted_at', '>=', now()->subDays(7));
            } elseif ($request->trash_age === 'medium') {
                $query->whereBetween('deleted_at', [
                    now()->subDays(20),
                    now()->subDays(7)
                ]);
            } elseif ($request->trash_age === 'old') {
                $query->where('deleted_at', '<', now()->subDays(20));
            }
        }
        
        // ── Filter: deleted_by ──────────────────────────────
        if ($request->filled('deleted_by') && $request->deleted_by) {
            $query->where('deleted_by_id', $request->deleted_by);
        }

        // ── Filter: quick_filter ────────────────────────────
        if ($request->filled('quick_filter')) {
            if ($request->quick_filter === 'my_deleted') {
                $query->where('deleted_by_id', auth()->id());
            } elseif ($request->quick_filter === 'today') {
                $query->whereDate('deleted_at', Carbon::today());
            }
        }

        // ── Pagination ──────────────────────────────────────
        $perPage = in_array($request->input('per_page'), [50, 100, 250, 500]) ? (int) $request->per_page : 50;

        $documents = $query->orderBy('deleted_at', 'desc')->paginate($perPage)->withQueryString();
        
        $categories = Category::orderBy('nama')->get();
        $users = User::where('is_active', true)->orderBy('nama_lengkap')->get();

        return view('recycle-bin.index', compact('documents', 'categories', 'users'));
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

    // BULK RESTORE
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id'
        ]);

        $documents = Document::onlyTrashed()->whereIn('id', $request->document_ids)->get();
        $count = 0;

        foreach ($documents as $doc) {
            $doc->restore();
            $doc->update(['deleted_by_id' => null]);
            $count++;
        }

        if ($count > 0) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'role_saat_itu' => Auth::user()->role,
                'jenis_aktivitas' => 'RESTORE_DOKUMEN',
                'detail' => "Restore Massal: {$count} dokumen",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', "{$count} dokumen berhasil dipulihkan.");
    }

    // BULK HAPUS PERMANEN (Admin only)
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id'
        ]);

        $documents = Document::onlyTrashed()->whereIn('id', $request->document_ids)->get();
        $count = 0;

        foreach ($documents as $doc) {
            if (Storage::disk('local')->exists($doc->file_path)) {
                Storage::disk('local')->delete($doc->file_path);
            }
            $doc->forceDelete();
            $count++;
        }

        if ($count > 0) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'role_saat_itu' => Auth::user()->role,
                'jenis_aktivitas' => 'HAPUS_PERMANEN',
                'detail' => "Hapus Permanen Massal: {$count} dokumen",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', "{$count} dokumen berhasil dihapus permanen.");
    }
}
