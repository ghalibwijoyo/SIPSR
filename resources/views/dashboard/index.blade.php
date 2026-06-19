@extends('layouts.app')

@section('title', 'Dashboard — SIPSR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->nama_lengkap }}!</p>
    </div>
    <span class="badge bg-sipsr-primary fs-6 px-3 py-2">
        <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
    </span>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stats-icon bg-sipsr-primary bg-opacity-10 text-sipsr-primary me-3">
                    <i class="bi bi-file-earmark-text-fill fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">Total Dokumen</p>
                    <h3 class="fw-bold mb-0">{{ \App\Models\Document::count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="bi bi-folder-fill fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">Kategori</p>
                    <h3 class="fw-bold mb-0">{{ \App\Models\Category::count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">Pengguna Aktif</p>
                    <h3 class="fw-bold mb-0">{{ \App\Models\User::where('is_active', true)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="stats-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="bi bi-trash3-fill fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">Recycle Bin</p>
                    <h3 class="fw-bold mb-0">{{ \App\Models\Document::onlyTrashed()->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Activity --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-clock-history me-2 text-sipsr-primary"></i>Aktivitas Terbaru
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Waktu</th>
                        <th>Pengguna</th>
                        <th>Aktivitas</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(\App\Models\ActivityLog::with('user')->latest('created_at')->take(10)->get() as $log)
                    <tr>
                        <td class="ps-3 text-muted small">
                            {{ $log->created_at?->diffForHumans() ?? '-' }}
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $log->user->nama_lengkap ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-sipsr-primary bg-opacity-10 text-sipsr-primary">
                                {{ $log->jenis_aktivitas }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ Str::limit($log->detail, 50) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada aktivitas tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
