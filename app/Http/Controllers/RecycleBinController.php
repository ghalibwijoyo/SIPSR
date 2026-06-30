<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
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
                    now()->subDays(7),
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
        ActivityLog::log('RESTORE_DOKUMEN', "Restore: {$document->nama_dokumen}", $document->id);

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
        ActivityLog::log('HAPUS_PERMANEN', "Hapus Permanen: {$namaDokumen}", $documentId);

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
        ActivityLog::log('KOSONGKAN_RECYCLE_BIN', "Kosongkan Recycle Bin: {$count} dokumen");

        return redirect()->back()->with('success', 'Recycle Bin berhasil dikosongkan.');
    }

    // BULK RESTORE
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id',
        ]);

        $documents = Document::onlyTrashed()->whereIn('id', $request->document_ids)->get(['id', 'nama_dokumen']);
        $count = $documents->count();

        if ($count > 0) {
            $docNames = $documents->pluck('nama_dokumen')->toArray();
            $docNamesString = implode(', ', $docNames);

            // Bulk Update (1 Query instead of N*2)
            Document::onlyTrashed()->whereIn('id', $request->document_ids)->update([
                'deleted_by_id' => null,
                'deleted_at' => null,
            ]);

            ActivityLog::log('RESTORE_DOKUMEN', "Restore Massal {$count} dokumen: {$docNamesString}");
        }

        return redirect()->back()->with('success', "{$count} dokumen berhasil dipulihkan.");
    }

    // BULK HAPUS PERMANEN (Admin only)
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:documents,id',
        ]);

        $documents = Document::onlyTrashed()->whereIn('id', $request->document_ids)->get();
        $count = 0;
        $docNames = [];

        foreach ($documents as $doc) {
            if (Storage::disk('local')->exists($doc->file_path)) {
                Storage::disk('local')->delete($doc->file_path);
            }
            $docNames[] = $doc->nama_dokumen;
            $doc->forceDelete();
            $count++;
        }

        if ($count > 0) {
            $docNamesString = implode(', ', $docNames);
            ActivityLog::log('HAPUS_PERMANEN', "Hapus Permanen Massal {$count} dokumen: {$docNamesString}");
        }

        return redirect()->back()->with('success', "{$count} dokumen berhasil dihapus permanen.");
    }
}
