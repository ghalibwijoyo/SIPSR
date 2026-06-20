<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
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
        $kategoriTerbanyak = Category::withCount('documents')->orderBy('documents_count', 'desc')->first()->nama ?? '-';

        // Chart 1: Dokumen per Kategori (Doughnut)
        $kategoriData = Category::withCount('documents')->get();
        $chartKategoriLabels = $kategoriData->pluck('nama')->toArray();
        $chartKategoriData = $kategoriData->pluck('documents_count')->toArray();

        // Chart 2: Tren Upload Bulanan (Line)
        $chartUploadLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartUploadData = [];
        $currentYear = Carbon::now()->year;
        
        for ($i = 1; $i <= 12; $i++) {
            $chartUploadData[] = Document::whereMonth('created_at', $i)->whereYear('created_at', $currentYear)->count();
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
