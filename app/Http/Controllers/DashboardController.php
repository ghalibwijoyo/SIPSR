<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard.
     */
    public function index()
    {
        // Statistik Card
        $totalDokumen = Document::count();
        $uploadBulanIni = Document::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $totalUserAktif = User::where('is_active', true)->count();

        // Menggunakan 1 kali query untuk data Kategori
        $kategoriData = Category::withCount('documents')->get();
        $kategoriTerbanyak = $kategoriData->sortByDesc('documents_count')->first()->nama ?? '-';

        // Chart 1: Dokumen per Kategori (Doughnut)
        $chartKategoriLabels = $kategoriData->pluck('nama')->toArray();
        $chartKategoriData = $kategoriData->pluck('documents_count')->toArray();

        // Chart 2: Tren Upload Bulanan (Line)
        $chartUploadLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartUploadData = [];
        $currentYear = Carbon::now()->year;

        // Optimasi: Gunakan selectRaw dan groupBy untuk mengambil data 12 bulan dalam 1 query saja
        $monthlyUploads = Document::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('count', 'month');

        for ($i = 1; $i <= 12; $i++) {
            $chartUploadData[] = $monthlyUploads[$i] ?? 0;
        }

        // Tabel Terbaru
        $latestDocuments = Document::with(['category', 'uploader'])->latest()->take(5)->get();
        $latestActivities = ActivityLog::with('user')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalDokumen', 'uploadBulanIni', 'totalUserAktif', 'kategoriTerbanyak',
            'chartKategoriLabels', 'chartKategoriData',
            'chartUploadLabels', 'chartUploadData',
            'latestDocuments', 'latestActivities'
        ));
    }
}
