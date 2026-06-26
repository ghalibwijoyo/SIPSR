<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use App\Models\Bank;
use App\Models\User;
use App\Models\ActivityLog;
use App\Exports\DokumenExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan dengan form filter dan preview data.
     */
    public function index(Request $request)
    {
        // ── Validasi Date Range ─────────────────────────────
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            if ($request->tanggal_dari > $request->tanggal_sampai) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tanggal awal tidak boleh lebih besar dari tanggal akhir');
            }
        }

        $query = Document::with(['category', 'uploader', 'bank']);

        // ── Smart Filters via Scopes ────────────────────────
        $query->search($request->search)
              ->byCategory($request->category_id)
              ->byBank($request->bank_id)
              ->byUploader($request->uploader_id)
              ->dateRange($request->tanggal_dari, $request->tanggal_sampai);

        $query->orderBy('tanggal_dokumen', 'desc');

        $totalDokumen = $query->count();
        // Preview table maximum 10 rows
        $dokumenPreview = $query->take(10)->get();

        // Data untuk dropdown filter
        $categories = Category::orderBy('nama')->get();
        $banks = Bank::orderBy('nama')->get();
        $users = User::where('is_active', true)->orderBy('nama_lengkap')->get();

        return view('laporan.index', compact(
            'totalDokumen', 'dokumenPreview',
            'categories', 'banks', 'users'
        ));
    }

    /**
     * Mengunduh file Excel (.xlsx).
     */
    public function exportExcel(Request $request)
    {
        $filterDesc = $this->getFilterDescription($request);
        $this->logActivity('EXPORT_EXCEL', "Export laporan dokumen ke Excel ({$filterDesc})");

        $fileName = 'Laporan_Dokumen_SIPSR_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new DokumenExport($request->all()), $fileName);
    }

    /**
     * Mengunduh laporan dalam format PDF.
     */
    public function exportPdf(Request $request)
    {
        $dokumen = $this->getDokumenByFilter($request);
        $rentangWaktu = $this->getRentangWaktuText($request);

        $filterDesc = $this->getFilterDescription($request);
        $this->logActivity('EXPORT_PDF', "Export laporan dokumen ke PDF ({$filterDesc})");

        $pdf = Pdf::loadView('laporan.pdf', compact('dokumen', 'rentangWaktu'))
                  ->setPaper('a4', 'landscape');
                  
        return $pdf->download('Laporan_Dokumen_SIPSR_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Menampilkan pratinjau untuk dicetak (Stream PDF).
     */
    public function printPdf(Request $request)
    {
        $dokumen = $this->getDokumenByFilter($request);
        $rentangWaktu = $this->getRentangWaktuText($request);

        $filterDesc = $this->getFilterDescription($request);
        $this->logActivity('CETAK_PDF', "Mencetak laporan dokumen ({$filterDesc})");

        $pdf = Pdf::loadView('laporan.pdf', compact('dokumen', 'rentangWaktu'))
                  ->setPaper('a4', 'landscape');
                  
        return $pdf->stream('Laporan_Dokumen_SIPSR_' . date('Y-m-d') . '.pdf');
    }

    // ─── Private Helpers ─────────────────────────────────────

    private function getDokumenByFilter(Request $request)
    {
        $query = Document::with(['category', 'uploader', 'bank']);
        
        $query->search($request->search)
              ->byCategory($request->category_id)
              ->byBank($request->bank_id)
              ->byUploader($request->uploader_id)
              ->dateRange($request->tanggal_dari, $request->tanggal_sampai);

        return $query->orderBy('tanggal_dokumen', 'desc')->get();
    }

    /**
     * Membuat teks deskriptif rentang waktu berdasarkan filter aktif.
     */
    private function getRentangWaktuText(Request $request): string
    {
        $parts = [];

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $parts[] = Carbon::parse($request->tanggal_dari)->format('d/m/Y') . ' — ' . Carbon::parse($request->tanggal_sampai)->format('d/m/Y');
        } elseif ($request->filled('tanggal_dari')) {
            $parts[] = 'Dari ' . Carbon::parse($request->tanggal_dari)->format('d/m/Y');
        } elseif ($request->filled('tanggal_sampai')) {
            $parts[] = 'Sampai ' . Carbon::parse($request->tanggal_sampai)->format('d/m/Y');
        } else {
            $parts[] = 'Semua Waktu';
        }

        if ($request->filled('search')) {
            $parts[] = 'Pencarian: "' . $request->search . '"';
        }

        return implode(' · ', $parts);
    }

    /**
     * Membuat deskripsi singkat filter untuk Activity Log.
     */
    private function getFilterDescription(Request $request): string
    {
        $filters = [];

        if ($request->filled('search'))       $filters[] = "search={$request->search}";
        if ($request->filled('category_id'))  $filters[] = "category_id={$request->category_id}";
        if ($request->filled('bank_id'))      $filters[] = "bank_id={$request->bank_id}";
        if ($request->filled('uploader_id'))  $filters[] = "uploader_id={$request->uploader_id}";
        if ($request->filled('tanggal_dari')) $filters[] = "dari={$request->tanggal_dari}";
        if ($request->filled('tanggal_sampai')) $filters[] = "sampai={$request->tanggal_sampai}";

        return empty($filters) ? 'Tanpa filter' : implode(', ', $filters);
    }

    private function logActivity(string $jenis, string $detail): void
    {
        ActivityLog::create([
            'user_id'         => auth()->id(),
            'role_saat_itu'   => auth()->user()->role,
            'jenis_aktivitas' => $jenis,
            'detail'          => $detail,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);
    }
}
