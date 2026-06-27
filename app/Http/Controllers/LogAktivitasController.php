<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ActivityLogController extends Controller
{
    /**
     * Menampilkan log aktivitas dengan filter dan pagination.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::withEagerLoading();

        // ── Smart Search ───────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jenis_aktivitas', 'LIKE', "%{$search}%")
                  ->orWhere('detail', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%")
                  ->orWhere('user_agent', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('nama_lengkap', 'LIKE', "%{$search}%")
                         ->orWhere('username', 'LIKE', "%{$search}%");
                  });
            });
        }

        // ── Filter: jenis_aktivitas ────────────────────────
        if ($request->filled('jenis_aktivitas')) {
            $query->where('jenis_aktivitas', $request->jenis_aktivitas);
        }

        // ── Filter: user_id ────────────────────────────────
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // ── Filter: tanggal ────────────────────────────────
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            if ($request->tanggal_dari > $request->tanggal_sampai) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tanggal awal tidak boleh lebih besar dari tanggal akhir');
            }
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        // ── Filter: ip_address ─────────────────────────────
        if ($request->filled('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        // ── Filter: user_agent ─────────────────────────────
        if ($request->filled('user_agent')) {
            $query->where('user_agent', 'LIKE', "%{$request->user_agent}%");
        }

        // ── Sorting & Pagination ───────────────────────────
        $perPage = in_array($request->input('per_page'), [50, 100, 250, 500]) ? (int) $request->per_page : 50;
        $logs = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        // ── Data untuk dropdown filter ─────────────────────
        $users = User::where('is_active', true)->orderBy('nama_lengkap')->get();
        
        // Ambil jenis_aktivitas unik dari tabel
        $jenisAktivitasList = ActivityLog::select('jenis_aktivitas')
            ->distinct()
            ->orderBy('jenis_aktivitas')
            ->pluck('jenis_aktivitas');

        return view('aktivitas.index', compact('logs', 'users', 'jenisAktivitasList'));
    }
}
