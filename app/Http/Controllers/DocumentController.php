<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Daftar dokumen aktif + filter + pagination.
     */
    public function index(Request $request)
    {
        $query = Document::with(['category', 'uploader'])
            ->whereNull('deleted_at');

        // ── Filter: search ──────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_dokumen', 'LIKE', "%{$search}%")
                  ->orWhere('nama_dokumen', 'LIKE', "%{$search}%")
                  ->orWhereHas('uploader', function ($q2) use ($search) {
                      $q2->where('nama_lengkap', 'LIKE', "%{$search}%");
                  });
            });
        }

        // ── Filter: category ────────────────────────────────
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // ── Filter: date range ──────────────────────────────
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_dokumen', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_dokumen', '<=', $request->tanggal_sampai);
        }

        // ── Sorting ─────────────────────────────────────────
        $sortCol = $request->input('sort', 'created_at');
        $sortDir = $request->input('dir', 'desc');
        $allowedSorts = ['nomor_dokumen', 'nama_dokumen', 'tanggal_dokumen', 'created_at'];

        if (!in_array($sortCol, $allowedSorts)) {
            $sortCol = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        $query->orderBy($sortCol, $sortDir);

        // ── Pagination ──────────────────────────────────────
        $perPage = in_array($request->input('per_page'), [10, 20, 50]) ? (int) $request->per_page : 20;

        $documents  = $query->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('nama')->get();

        return view('dokumen.index', compact('documents', 'categories'));
    }

    /**
     * Form upload dokumen baru.
     */
    public function create()
    {
        $categories = Category::orderBy('nama')->get();

        return view('dokumen.create', compact('categories'));
    }

    /**
     * Proses upload + simpan dokumen.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_dokumen'   => 'required|string|max:255',
            'nama_dokumen'    => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'tanggal_dokumen' => 'required|date',
            'deskripsi'       => 'nullable|string',
            'file'            => 'required|mimes:pdf,doc,docx|max:20480',
        ]);

        // ── Cek nomor duplikat ──────────────────────────────
        if (!$request->boolean('konfirmasi_duplikat')) {
            $exists = Document::where('nomor_dokumen', $validated['nomor_dokumen'])->exists();
            if ($exists) {
                return back()
                    ->withInput()
                    ->with('warning_duplikat', true)
                    ->withErrors(['nomor_dokumen' => 'Nomor ini sudah digunakan dokumen lain. Lanjutkan menyimpan?']);
            }
        }

        // ── Proses file ─────────────────────────────────────
        $file      = $request->file('file');
        $now       = Carbon::now();
        $folder    = 'uploads/' . $now->format('Y') . '/' . $now->format('m');
        $origName  = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $baseName  = pathinfo($origName, PATHINFO_FILENAME);

        // Auto-rename jika file sudah ada
        $fileName = $origName;
        $counter  = 1;
        while (Storage::disk('local')->exists($folder . '/' . $fileName)) {
            $fileName = $baseName . '_' . $counter . '.' . $extension;
            $counter++;
        }

        Storage::disk('local')->putFileAs($folder, $file, $fileName);

        // ── Simpan ke database ──────────────────────────────
        $document = Document::create([
            'nomor_dokumen'   => $validated['nomor_dokumen'],
            'nama_dokumen'    => $validated['nama_dokumen'],
            'category_id'     => $validated['category_id'],
            'tanggal_dokumen' => $validated['tanggal_dokumen'],
            'deskripsi'       => $validated['deskripsi'] ?? null,
            'file_path'       => $folder . '/' . $fileName,
            'file_name'       => $fileName,
            'uploader_id'     => auth()->id(),
        ]);

        // ── Activity Log ────────────────────────────────────
        $this->logActivity('UPLOAD_DOKUMEN', 'Mengupload dokumen: ' . $document->nama_dokumen, $document->id);

        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil diupload.');
    }

    /**
     * Detail dokumen.
     */
    public function show(Document $dokumen)
    {
        $dokumen->load(['category', 'uploader', 'updatedBy', 'histories.changedBy']);

        return view('dokumen.show', compact('dokumen'));
    }

    /**
     * Form edit metadata (via modal — data dikirim dari show).
     */
    public function edit(Document $dokumen)
    {
        $categories = Category::orderBy('nama')->get();

        return view('dokumen.edit', compact('dokumen', 'categories'));
    }

    /**
     * Proses edit metadata + simpan history.
     */
    public function update(Request $request, Document $dokumen)
    {
        $validated = $request->validate([
            'nomor_dokumen'   => 'required|string|max:255',
            'nama_dokumen'    => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'tanggal_dokumen' => 'required|date',
            'deskripsi'       => 'nullable|string',
        ]);

        // ── Track changes per field ─────────────────────────
        $trackFields = [
            'nomor_dokumen'   => 'Nomor Dokumen',
            'nama_dokumen'    => 'Nama Dokumen',
            'category_id'     => 'Kategori',
            'tanggal_dokumen' => 'Tanggal Dokumen',
            'deskripsi'       => 'Deskripsi',
        ];

        foreach ($trackFields as $field => $label) {
            $oldValue = $dokumen->getOriginal($field);
            $newValue = $validated[$field] ?? null;

            // Normalize date for comparison
            if ($field === 'tanggal_dokumen') {
                $oldValue = $oldValue instanceof \DateTimeInterface
                    ? $oldValue->format('Y-m-d')
                    : (string) $oldValue;
                $newValue = (string) $newValue;
            }

            // Normalize category for display
            if ($field === 'category_id' && (string) $oldValue !== (string) $newValue) {
                $oldCat = Category::find($oldValue);
                $newCat = Category::find($newValue);
                DocumentHistory::create([
                    'document_id'   => $dokumen->id,
                    'field_name'    => $label,
                    'old_value'     => $oldCat?->nama ?? $oldValue,
                    'new_value'     => $newCat?->nama ?? $newValue,
                    'changed_by_id' => auth()->id(),
                    'changed_at'    => now(),
                ]);
                continue;
            }

            if ((string) $oldValue !== (string) $newValue) {
                DocumentHistory::create([
                    'document_id'   => $dokumen->id,
                    'field_name'    => $label,
                    'old_value'     => $oldValue,
                    'new_value'     => $newValue,
                    'changed_by_id' => auth()->id(),
                    'changed_at'    => now(),
                ]);
            }
        }

        // ── Update dokumen ──────────────────────────────────
        $validated['updated_by_id'] = auth()->id();
        $dokumen->update($validated);

        // ── Activity Log ────────────────────────────────────
        $this->logActivity('EDIT_METADATA', 'Mengedit metadata: ' . $dokumen->nama_dokumen, $dokumen->id);

        return redirect()->route('dokumen.show', $dokumen)
            ->with('success', 'Metadata dokumen berhasil diperbarui.');
    }

    /**
     * Soft delete → Recycle Bin.
     */
    public function destroy(Document $dokumen)
    {
        $dokumen->update([
            'deleted_by_id' => auth()->id(),
        ]);
        $dokumen->delete(); // SoftDeletes → sets deleted_at

        // ── Activity Log ────────────────────────────────────
        $this->logActivity('HAPUS_DOKUMEN', 'Menghapus dokumen: ' . $dokumen->nama_dokumen, $dokumen->id);

        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen dipindahkan ke Recycle Bin.');
    }

    /**
     * Download file (auth check via middleware).
     */
    public function download(Document $dokumen)
    {
        if (!Storage::disk('local')->exists($dokumen->file_path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        // ── Activity Log ────────────────────────────────────
        $this->logActivity('DOWNLOAD_DOKUMEN', 'Mengunduh dokumen: ' . $dokumen->nama_dokumen, $dokumen->id);

        return Storage::disk('local')->download($dokumen->file_path, $dokumen->file_name);
    }

    /**
     * Preview / stream file untuk iframe.
     */
    public function preview(Document $dokumen)
    {
        if (!Storage::disk('local')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType = Storage::disk('local')->mimeType($dokumen->file_path);

        return response()->file(
            Storage::disk('local')->path($dokumen->file_path),
            ['Content-Type' => $mimeType]
        );
    }

    // ─── Helper: Activity Log ──────────────────────────────

    private function logActivity(string $jenis, string $detail, ?string $documentId = null): void
    {
        ActivityLog::create([
            'user_id'         => auth()->id(),
            'role_saat_itu'   => auth()->user()->role,
            'jenis_aktivitas' => $jenis,
            'detail'          => $detail,
            'document_id'     => $documentId,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);
    }
}
