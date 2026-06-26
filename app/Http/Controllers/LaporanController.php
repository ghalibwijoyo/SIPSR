<?php

namespace App\Http\Controllers;

use App\Models\Document;
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
        // Default filter ke 1 Bulan jika tidak ada request
        $periode = $request->input('periode', '1_bulan');
        
        $query = Document::with(['category', 'uploader', 'bank']);

        // Rentang waktu
        $sekarang = Carbon::now();
        $tanggalDari = null;
        
        switch ($periode) {
            case '1_hari':
                $tanggalDari = $sekarang->copy()->subDay();
                break;
            case '1_minggu':
                $tanggalDari = $sekarang->copy()->subWeek();
                break;
            case '1_bulan':
                $tanggalDari = $sekarang->copy()->subMonth();
                break;
            case '1_tahun':
                $tanggalDari = $sekarang->copy()->subYear();
                break;
            case '5_tahun':
                $tanggalDari = $sekarang->copy()->subYears(5);
                break;
            case 'semua':
                $tanggalDari = null;
                break;
            default:
                $tanggalDari = $sekarang->copy()->subMonth();
        }

        if ($tanggalDari) {
            $query->where('tanggal_dokumen', '>=', $tanggalDari);
        }

        $query->orderBy('tanggal_dokumen', 'desc');

        $totalDokumen = $query->count();
        // Preview table maximum 10 rows
        $dokumenPreview = $query->take(10)->get();

        return view('laporan.index', compact('periode', 'totalDokumen', 'dokumenPreview', 'tanggalDari'));
    }

    /**
     * Mengunduh file Excel (.xlsx).
     */
    public function exportExcel(Request $request)
    {
        $periode = $request->input('periode', '1_bulan');
        
        $this->logActivity('EXPORT_EXCEL', "Export laporan dokumen ke Excel (Periode: {$periode})");

        $fileName = 'Laporan_Dokumen_SIPSR_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new DokumenExport($periode), $fileName);
    }

    /**
     * Mengunduh laporan dalam format PDF.
     */
    public function exportPdf(Request $request)
    {
        $periode = $request->input('periode', '1_bulan');
        $dokumen = $this->getDokumenByPeriode($periode);
        $rentangWaktu = $this->getTeksPeriode($periode);

        $this->logActivity('EXPORT_PDF', "Export laporan dokumen ke PDF (Periode: {$periode})");

        $pdf = Pdf::loadView('laporan.pdf', compact('dokumen', 'rentangWaktu'))
                  ->setPaper('a4', 'landscape');
                  
        return $pdf->download('Laporan_Dokumen_SIPSR_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Menampilkan pratinjau untuk dicetak (Stream PDF).
     */
    public function printPdf(Request $request)
    {
        $periode = $request->input('periode', '1_bulan');
        $dokumen = $this->getDokumenByPeriode($periode);
        $rentangWaktu = $this->getTeksPeriode($periode);

        $this->logActivity('CETAK_PDF', "Mencetak laporan dokumen (Periode: {$periode})");

        $pdf = Pdf::loadView('laporan.pdf', compact('dokumen', 'rentangWaktu'))
                  ->setPaper('a4', 'landscape');
                  
        return $pdf->stream('Laporan_Dokumen_SIPSR_' . date('Y-m-d') . '.pdf');
    }

    // ─── Private Helpers ─────────────────────────────────────

    private function getDokumenByPeriode($periode)
    {
        $query = Document::with(['category', 'uploader', 'bank']);
        
        $sekarang = Carbon::now();
        $tanggalDari = null;
        
        switch ($periode) {
            case '1_hari':   $tanggalDari = $sekarang->copy()->subDay(); break;
            case '1_minggu': $tanggalDari = $sekarang->copy()->subWeek(); break;
            case '1_bulan':  $tanggalDari = $sekarang->copy()->subMonth(); break;
            case '1_tahun':  $tanggalDari = $sekarang->copy()->subYear(); break;
            case '5_tahun':  $tanggalDari = $sekarang->copy()->subYears(5); break;
        }

        if ($tanggalDari) {
            $query->where('tanggal_dokumen', '>=', $tanggalDari);
        }

        return $query->orderBy('tanggal_dokumen', 'desc')->get();
    }

    private function getTeksPeriode($periode)
    {
        switch ($periode) {
            case '1_hari': return '1 Hari Terakhir';
            case '1_minggu': return '1 Minggu Terakhir';
            case '1_bulan': return '1 Bulan Terakhir';
            case '1_tahun': return '1 Tahun Terakhir';
            case '5_tahun': return '5 Tahun Terakhir';
            case 'semua': return 'Semua Waktu';
            default: return '1 Bulan Terakhir';
        }
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
