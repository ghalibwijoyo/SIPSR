@extends('layouts.app')

@section('title', 'Log Aktivitas — SIPSR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Log Aktivitas Sistem</h1>
</div>

<!-- Filter Section -->
<div class="card mb-4 shadow-sm border-0" id="filter-card">
    <div class="card-body">
        <form action="{{ route('aktivitas.index') }}" method="GET">
            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-3">
                    <label for="jenis_aktivitas" class="form-label text-muted small fw-bold">Jenis Aktivitas</label>
                    <select name="jenis_aktivitas" id="jenis_aktivitas" class="form-select">
                        <option value="">Semua Aktivitas</option>
                        @foreach($jenisAktivitasList as $jenis)
                            <option value="{{ $jenis }}" {{ request('jenis_aktivitas') == $jenis ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', $jenis) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="user_id" class="form-label text-muted small fw-bold">Pengguna</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->nama_lengkap }} ({{ $user->role }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="tanggal_dari" class="form-label text-muted small fw-bold">Dari Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_dari" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                </div>
                <div class="col-md-2">
                    <label for="tanggal_sampai" class="form-label text-muted small fw-bold">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_sampai" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('aktivitas.index') }}" class="btn btn-secondary" title="Reset filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
            
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="ip_address" class="form-label text-muted small fw-bold">IP Address</label>
                    <input type="text" class="form-control" id="ip_address" name="ip_address" value="{{ request('ip_address') }}" placeholder="Contoh: 192.168.1.1">
                </div>
                <div class="col-md-3">
                    <label for="user_agent" class="form-label text-muted small fw-bold">Browser / Device</label>
                    <input type="text" class="form-control" id="user_agent" name="user_agent" value="{{ request('user_agent') }}" placeholder="Contoh: Chrome atau Windows">
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Table Section -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-activity me-2 text-sipsr-primary"></i>
            Log Aktivitas
            <span class="badge bg-secondary ms-1">{{ $logs->total() }}</span>
        </h5>

        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <label for="per-page" class="form-label mb-0 small text-muted">Tampilkan:</label>
                <select class="form-select form-select-sm" id="per-page" style="width: auto;"
                        onchange="updatePerPage(this.value)">
                    @foreach([50, 100, 250, 500] as $pp)
                        <option value="{{ $pp }}" {{ request('per_page', 50) == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Waktu</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Aktivitas</th>
                        <th>Detail</th>
                        <th>IP Address</th>
                        <th class="pe-4">Browser/Device</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="ps-4 small text-muted text-nowrap">
                            {{ $log->created_at?->format('d M Y') ?? '-' }}<br>
                            <span class="fw-semibold text-dark">{{ $log->created_at?->format('H:i:s') ?? '' }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $log->user->nama_lengkap ?? '-' }}</div>
                            <div class="small text-muted">{{ $log->user->username ?? '-' }}</div>
                        </td>
                        <td>
                            @if($log->role_saat_itu === 'ADMIN')
                                <span class="badge bg-danger">ADMIN</span>
                            @else
                                <span class="badge bg-primary">STAFF</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">
                                {{ $log->jenis_aktivitas }}
                            </span>
                        </td>
                        <td class="small">{{ $log->detail }}</td>
                        <td class="small font-monospace text-muted">{{ $log->ip_address ?: '-' }}</td>
                        <td class="pe-4 small text-muted" title="{{ $log->user_agent }}">
                            {{ Str::limit($log->user_agent, 30) ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Belum ada log aktivitas yang sesuai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center align-items-center w-100 position-relative" style="min-height: 80px;">
        @if($logs->hasPages())
            {{ $logs->links('vendor.pagination.bootstrap-5') }}
        @endif
    </div>

    <x-scroll-to-top />
</div>
@endsection

@push('scripts')
<script>
window.updatePerPage = function(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
};
</script>
@endpush
