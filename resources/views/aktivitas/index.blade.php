@extends('layouts.app')

@section('title', 'Log Aktivitas — SIPSR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Log Aktivitas Sistem</h1>
</div>

{{-- Smart Search Bar & Quick Filters --}}
<div class="card mb-3 border-0 shadow-sm" id="filter-card">
    <div class="card-body">
        <form method="GET" action="{{ route('aktivitas.index') }}" class="row g-3" id="mainFilterForm">
            <!-- Smart Search Bar -->
            <div class="col-12">
                <label for="search_input" class="form-label visually-hidden">Cari Aktivitas</label>
                <div class="input-group input-group-lg shadow-sm rounded overflow-hidden border">
                    <button type="button" class="btn btn-light border-0 px-4" data-bs-toggle="offcanvas" data-bs-target="#advancedFilter" aria-controls="advancedFilter" aria-label="Buka panel filter lanjutan" title="Filter Lanjutan">
                        <i class="bi bi-sliders text-sipsr-primary"></i>
                    </button>
                    <input type="text" 
                           id="search_input"
                           name="search" 
                           class="form-control form-control-lg border-0 bg-white shadow-none"
                           placeholder="Cari aktivitas, pengguna, IP address, atau browser..."
                           value="{{ request('search') }}"
                           aria-label="Cari aktivitas">
                    <button type="submit" class="input-group-text bg-white border-0 text-muted px-4 btn btn-link" aria-label="Cari">
                        <i class="bi bi-search text-dark"></i>
                    </button>
                </div>
            </div>
            
            <!-- Quick Filter Badges -->
            <div class="col-12 mt-2">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('aktivitas.index') }}" 
                       class="btn btn-sm {{ !request()->has('search') && !request()->has('jenis_aktivitas') && !request()->has('user_id') && !request()->has('ip_address') && !request()->has('user_agent') && !request()->has('tanggal_dari') ? 'btn-success' : 'btn-outline-success' }}"
                       aria-label="Lihat semua aktivitas">
                        Semua Aktivitas
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Filter Status & Result Counter --}}
@php
    $activeFilters = array_filter([
        request('search'),
        request('jenis_aktivitas'),
        request('user_id'),
        request('ip_address'),
        request('user_agent'),
        request('tanggal_dari'),
        request('tanggal_sampai')
    ]);
@endphp

@if(!empty($activeFilters))
<div class="alert alert-secondary py-2 px-3 mb-3 d-flex justify-content-between align-items-center shadow-sm border-0">
    <span>
        <i class="bi bi-funnel-fill text-sipsr-primary me-2"></i> 
        <strong>{{ count($activeFilters) }} filter aktif</strong> diterapkan.
    </span>
    <a href="{{ route('aktivitas.index') }}" class="btn btn-sm btn-danger rounded-pill px-3">
        <i class="bi bi-x-circle me-1"></i>Clear All
    </a>
</div>
@endif

{{-- Offcanvas Advanced Filter Panel --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="advancedFilter" aria-labelledby="advancedFilterLabel">
    <div class="offcanvas-header bg-light border-bottom">
        <h5 class="offcanvas-title fw-bold" id="advancedFilterLabel">
            <i class="bi bi-sliders me-2 text-sipsr-primary"></i>Filter Lanjutan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup filter lanjutan"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="{{ route('aktivitas.index') }}" id="advancedFilterForm">
            <!-- Preserve search if any -->
            <input type="hidden" name="search" value="{{ request('search') }}">
            
            <!-- Jenis Aktivitas -->
            <div class="mb-4">
                <label for="jenis_aktivitas" class="form-label small fw-bold">Jenis Aktivitas</label>
                <select name="jenis_aktivitas" id="jenis_aktivitas" class="form-select">
                    <option value="">-- Semua Aktivitas --</option>
                    @foreach($jenisAktivitasList as $jenis)
                        <option value="{{ $jenis }}" {{ request('jenis_aktivitas') == $jenis ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', $jenis) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pengguna -->
            <div class="mb-4">
                <label for="user_id" class="form-label small fw-bold">Pengguna</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">-- Semua Pengguna --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->nama_lengkap }} ({{ $user->role }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- IP Address -->
            <div class="mb-4">
                <label for="ip_address" class="form-label small fw-bold">IP Address</label>
                <input type="text" class="form-control" id="ip_address" name="ip_address" value="{{ request('ip_address') }}" placeholder="Contoh: 192.168.1.1">
            </div>
            
            <!-- User Agent -->
            <div class="mb-4">
                <label for="user_agent" class="form-label small fw-bold">Browser / Device</label>
                <input type="text" class="form-control" id="user_agent" name="user_agent" value="{{ request('user_agent') }}" placeholder="Contoh: Chrome atau Windows">
            </div>

            <!-- Date Range -->
            <div class="mb-4">
                <label class="form-label small fw-bold mb-3">Rentang Waktu</label>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="tanggal_dari" class="form-label small text-muted mb-1">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" id="tanggal_dari" class="form-control form-control-sm" value="{{ request('tanggal_dari') }}" aria-label="Filter aktivitas dari tanggal">
                    </div>
                    <div class="col-6">
                        <label for="tanggal_sampai" class="form-label small text-muted mb-1">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai" class="form-control form-control-sm" value="{{ request('tanggal_sampai') }}" aria-label="Filter aktivitas sampai tanggal">
                    </div>
                </div>
                
                <small class="text-muted d-block mb-2">Quick preset:</small>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setDateRange(0, 0); return false;" aria-label="Filter aktivitas hari ini">Hari Ini</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setDateRange(7, 0); return false;" aria-label="Filter aktivitas 1 minggu terakhir">1 Minggu</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setDateRange(30, 0); return false;" aria-label="Filter aktivitas 1 bulan terakhir">1 Bulan</button>
                </div>
            </div>
        </form>
    </div>
    <div class="offcanvas-footer p-3 border-top bg-light d-flex gap-2">
        <a href="{{ route('aktivitas.index') }}" class="btn btn-light flex-grow-1 border">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
        </a>
        <button type="submit" form="advancedFilterForm" class="btn btn-success flex-grow-1">
            <i class="bi bi-check2 me-1"></i>Terapkan
        </button>
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
            <table class="table table-hover table-striped align-middle mb-0" id="aktivitas-table">
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
                        <td colspan="7" class="p-4">
                            <div class="alert alert-warning text-start mb-0">
                                <h5 class="alert-heading">
                                    <i class="bi bi-search me-2"></i> Tidak ada hasil
                                </h5>
                                <p>Log aktivitas dengan filter Anda tidak ditemukan.</p>
                                <ul class="mb-0">
                                    <li>Coba filter jenis aktivitas atau rentang waktu yang lebih umum</li>
                                    <li><a href="{{ route('aktivitas.index') }}" class="alert-link">Reset semua filter</a></li>
                                </ul>
                            </div>
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
window.setDateRange = function(daysBack, daysForward) {
    const today = new Date();
    const fromDate = new Date(today);
    fromDate.setDate(today.getDate() - daysBack);
    
    const toDate = new Date(today);
    toDate.setDate(today.getDate() + daysForward);

    document.getElementById('tanggal_dari').value = fromDate.toISOString().split('T')[0];
    document.getElementById('tanggal_sampai').value = toDate.toISOString().split('T')[0];
};

window.updatePerPage = function(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
};

document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = document.getElementById('mainFilterForm');
    
    if (searchInput && searchForm) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const url = new URL(searchForm.action);
                const formData = new FormData(searchForm);
                const searchParams = new URLSearchParams();
                for (const pair of formData) {
                    if (pair[1]) searchParams.append(pair[0], pair[1]);
                }
                url.search = searchParams.toString();
                
                const tableContainer = document.querySelector('.table-responsive');
                if (tableContainer) tableContainer.style.opacity = '0.5';

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        const newTable = doc.querySelector('#aktivitas-table');
                        if (newTable) {
                            document.querySelector('#aktivitas-table').innerHTML = newTable.innerHTML;
                        }
                        
                        const newPagination = doc.querySelector('.card-footer');
                        const currentPagination = document.querySelector('.card-footer');
                        if (newPagination && currentPagination) {
                            currentPagination.innerHTML = newPagination.innerHTML;
                        }

                        window.history.pushState({}, '', url);
                        if (tableContainer) tableContainer.style.opacity = '1';
                    })
                    .catch(() => {
                        searchForm.submit(); // Fallback
                    });
            }, 400); // Delay 400ms is smooth
        });
        
        // Auto-focus search on initial load
        if(searchInput.value) {
            searchInput.focus();
            const length = searchInput.value.length;
            searchInput.setSelectionRange(length, length);
        }
    }

    // Filter form loading state
    const filterForm = document.querySelector("form[action='{{ route('aktivitas.index') }}']");
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sedang memproses...';
            }
        });
    }

    // Date range validation
    const dateFrom = document.getElementById('tanggal_dari');
    const dateTo = document.getElementById('tanggal_sampai');
    
    if (dateFrom && dateTo) {
        const validateDateRange = () => {
            if (dateFrom.value && dateTo.value && dateFrom.value > dateTo.value) {
                dateTo.setCustomValidity('Tanggal akhir tidak boleh sebelum tanggal awal');
                dateTo.classList.add('is-invalid');
            } else {
                dateTo.setCustomValidity('');
                dateTo.classList.remove('is-invalid');
            }
        };
        dateFrom.addEventListener('change', validateDateRange);
        dateTo.addEventListener('change', validateDateRange);
    }
});
</script>
@endpush
