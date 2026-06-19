<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Menampilkan log aktivitas dengan filter dan pagination.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // ── Filter: jenis_aktivitas ────────────────────────
        if ($request->filled('jenis_aktivitas')) {
            $query->where('jenis_aktivitas', $request->jenis_aktivitas);
        }

        // ── Filter: user_id ────────────────────────────────
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // ── Filter: tanggal ────────────────────────────────
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        // ── Sorting & Pagination ───────────────────────────
        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // ── Data untuk dropdown filter ─────────────────────
        $users = User::orderBy('nama_lengkap')->get();
        // Ambil jenis_aktivitas unik dari tabel
        $jenisAktivitasList = ActivityLog::select('jenis_aktivitas')
            ->distinct()
            ->orderBy('jenis_aktivitas')
            ->pluck('jenis_aktivitas');

        return view('aktivitas.index', compact('logs', 'users', 'jenisAktivitasList'));
    }
}
