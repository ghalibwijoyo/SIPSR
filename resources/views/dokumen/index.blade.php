@extends('layouts.app')

@section('title', 'Daftar Dokumen — SIPSR')

@section('content')
{{-- Page Header --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Daftar Dokumen</h1>
        <p class="text-muted mb-0">Kelola semua dokumen arsip yang tersedia</p>
    </div>
    <a href="{{ route('dokumen.create') }}" class="btn btn-success" id="btn-upload-dokumen">
        <i class="bi bi-cloud-arrow-up me-1"></i>Upload Dokumen
    </a>
</div>

{{-- Smart Search Bar & Quick Filters --}}
<div class="card mb-3 border-0 shadow-sm" id="filter-card">
    <div class="card-body">
        <form method="GET" action="{{ route('dokumen.index') }}" class="row g-3" id="mainFilterForm">
            <!-- Smart Search Bar -->
            <div class="col-12">
                <div class="input-group input-group-lg shadow-sm rounded overflow-hidden border">
                    <span class="input-group-text bg-white border-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-lg border-0 bg-white shadow-none"
                           placeholder="Cari nomor, nama dokumen, atau uploader..."
                           value="{{ request('search') }}"
                           aria-label="Cari dokumen">
                    <button type="button" class="btn btn-light border-0 px-4" data-bs-toggle="offcanvas" data-bs-target="#advancedFilter" aria-controls="advancedFilter" aria-label="Buka panel filter lanjutan" title="Filter Lanjutan">
                        <i class="bi bi-sliders text-sipsr-primary"></i>
                    </button>
                </div>
                <small class="text-muted d-block mt-2 px-1">
                    Contoh: "PSR-2026-001" atau "laporan bulanan"
                </small>
            </div>
            
            <!-- Quick Filter Badges -->
            <div class="col-12 mt-2">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('dokumen.index') }}" 
                       class="btn btn-sm {{ !request()->has('search') && !request()->has('category_id') && !request()->has('quick_filter') && !request()->has('format') && !request()->has('uploader_id') && !request()->has('tanggal_dari') ? 'btn-success' : 'btn-outline-success' }}"
                       aria-label="Lihat semua dokumen">
                        Semua Dokumen
                    </a>
                    
                    <button type="submit" 
                            name="quick_filter" 
                            value="pdf" 
                            class="btn btn-sm {{ request('quick_filter') === 'pdf' ? 'btn-success' : 'btn-outline-success' }}"
                            aria-label="Filter hanya dokumen PDF">
                        📄 Hanya PDF
                    </button>
                    
                    <button type="submit" 
                            name="quick_filter" 
                            value="my_upload" 
                            class="btn btn-sm {{ request('quick_filter') === 'my_upload' ? 'btn-success' : 'btn-outline-success' }}"
                            aria-label="Filter dokumen yang saya upload">
                        👤 Unggahan Saya
                    </button>
                    
                    <button type="submit" 
                            name="quick_filter" 
                            value="today" 
                            class="btn btn-sm {{ request('quick_filter') === 'today' ? 'btn-success' : 'btn-outline-success' }}"
                            aria-label="Filter dokumen yang diupload hari ini">
                        📅 Hari Ini
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Filter Status & Result Counter --}}
@php
    $activeFilters = array_filter([
        request('search'),
        request('category_id'),
        request('uploader_id'),
        request('tanggal_dari'),
        request('tanggal_sampai'),
        request('format'),
        request('quick_filter')
    ]);
@endphp

@if(!empty($activeFilters))
<div class="alert alert-secondary py-2 px-3 mb-3 d-flex justify-content-between align-items-center shadow-sm border-0">
    <span>
        <i class="bi bi-funnel-fill text-sipsr-primary me-2"></i> 
        <strong>{{ count($activeFilters) }} filter aktif</strong> diterapkan.
    </span>
    <a href="{{ route('dokumen.index') }}" class="btn btn-sm btn-danger rounded-pill px-3">
        <i class="bi bi-x-circle me-1"></i>Clear All
    </a>
</div>
@endif

@if($documents->count() > 0)
<div class="alert alert-info py-2 px-3 mb-4 d-flex align-items-center gap-2 shadow-sm border-0">
    <i class="bi bi-info-circle-fill"></i>
    <span>
        Ditemukan <strong>{{ $documents->total() }}</strong> dokumen
        @if(!empty($activeFilters))
            ({{ $documents->count() }} ditampilkan di halaman ini)
        @endif
    </span>
</div>
@else
<div class="alert alert-warning mb-4 shadow-sm border-0">
    <div class="d-flex align-items-center mb-2">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
        <h6 class="mb-0 fw-bold">Tidak ada dokumen ditemukan</h6>
    </div>
    <p class="mb-0 small">Coba ubah filter di atas atau periksa kembali kata kunci pencarian Anda.</p>
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
        <form method="GET" action="{{ route('dokumen.index') }}" id="advancedFilterForm">
            <!-- Preserve search if any -->
            <input type="hidden" name="search" value="{{ request('search') }}">
            
            <!-- Category -->
            <div class="mb-4">
                <label for="category_filter" class="form-label small fw-bold">Kategori Dokumen</label>
                <select name="category_id" id="category_filter" class="form-select" aria-label="Filter berdasarkan kategori dokumen">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Uploader -->
            <div class="mb-4">
                <label for="uploader_filter" class="form-label small fw-bold">Pengunggah (Uploader)</label>
                <select name="uploader_id" id="uploader_filter" class="form-select" aria-label="Filter berdasarkan pengunggah dokumen">
                    <option value="">-- Semua Pengguna --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('uploader_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Format File -->
            <div class="mb-4">
                <label class="form-label small fw-bold">Format File</label>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="format[]" value="pdf" id="format_pdf" {{ in_array('pdf', (array)request('format', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="format_pdf">PDF (Bisa di-preview)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="format[]" value="doc" id="format_doc" {{ in_array('doc', (array)request('format', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="format_doc">DOC / DOCX (Unduh)</label>
                </div>
            </div>

            <!-- Date Range -->
            <div class="mb-4">
                <label class="form-label small fw-bold mb-3">Rentang Waktu</label>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="tanggal_dari" class="form-label small text-muted mb-1">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" id="tanggal_dari" class="form-control form-control-sm" value="{{ request('tanggal_dari') }}" aria-label="Filter dokumen dari tanggal">
                    </div>
                    <div class="col-6">
                        <label for="tanggal_sampai" class="form-label small text-muted mb-1">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai" class="form-control form-control-sm" value="{{ request('tanggal_sampai') }}" aria-label="Filter dokumen sampai tanggal">
                    </div>
                </div>
                
                <small class="text-muted d-block mb-2">Quick preset:</small>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setDateRange(0, 0); return false;" aria-label="Filter dokumen hari ini">Hari Ini</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setDateRange(7, 0); return false;" aria-label="Filter dokumen 1 minggu terakhir">1 Minggu</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setDateRange(30, 0); return false;" aria-label="Filter dokumen 1 bulan terakhir">1 Bulan</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="setDateRange(365, 0); return false;" aria-label="Filter dokumen 1 tahun terakhir">1 Tahun</button>
                </div>
            </div>
        </form>
    </div>
    <div class="offcanvas-footer p-3 border-top bg-light d-flex gap-2">
        <a href="{{ route('dokumen.index') }}" class="btn btn-light flex-grow-1 border">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
        </a>
        <button type="submit" form="advancedFilterForm" class="btn btn-success flex-grow-1">
            <i class="bi bi-check2 me-1"></i>Terapkan
        </button>
    </div>
</div>


{{-- Documents Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-file-earmark-text me-2 text-sipsr-primary"></i>
            Dokumen
            <span class="badge bg-secondary ms-1">{{ $documents->total() }}</span>
        </h5>

        {{-- Per page selector & Bulk Actions --}}
        <div class="d-flex align-items-center gap-3">
            {{-- Bulk Actions (Hidden by default) --}}
            <div id="bulk-actions" class="d-none align-items-center gap-2">
                <span class="small text-muted me-2"><span id="selected-count">0</span> terpilih</span>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="submitBulkDelete()" title="Hapus Terpilih">
                    <i class="bi bi-trash"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="submitBulkDownload()" title="Download Terpilih">
                    <i class="bi bi-download"></i>
                </button>
            </div>

            {{-- Per page selector --}}
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

    <div class="card-body p-0 border-bottom">
        <div class="table-responsive" style="min-height: 400px;">
            <table class="table table-striped table-hover align-middle mb-0" id="dokumen-table">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 40px;">
                            <div class="form-check m-0">
                                <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                            </div>
                        </th>
                        <th class="d-mobile-none" style="width: 50px;">No</th>
                        <th class="d-mobile-none">
                            <a href="{{ route('dokumen.index', array_merge(request()->all(), ['sort' => 'nomor_dokumen', 'dir' => request('sort') == 'nomor_dokumen' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
                               class="text-decoration-none text-muted">
                                Nomor
                                @if(request('sort') == 'nomor_dokumen')
                                    <i class="bi bi-caret-{{ request('dir') == 'asc' ? 'up' : 'down' }}-fill ms-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('dokumen.index', array_merge(request()->all(), ['sort' => 'nama_dokumen', 'dir' => request('sort') == 'nama_dokumen' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
                               class="text-decoration-none text-muted">
                                Nama Dokumen
                                @if(request('sort') == 'nama_dokumen')
                                    <i class="bi bi-caret-{{ request('dir') == 'asc' ? 'up' : 'down' }}-fill ms-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>Kategori</th>
                        <th>
                            <a href="{{ route('dokumen.index', array_merge(request()->all(), ['sort' => 'tanggal_dokumen', 'dir' => request('sort') == 'tanggal_dokumen' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
                               class="text-decoration-none text-muted">
                                Tanggal
                                @if(request('sort') == 'tanggal_dokumen')
                                    <i class="bi bi-caret-{{ request('dir') == 'asc' ? 'up' : 'down' }}-fill ms-1"></i>
                                @endif
                            </a>
                        </th>
                        <th class="d-mobile-none">Uploader</th>
                        <th class="text-center text-nowrap" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $i => $doc)
                    <tr>
                        <td class="ps-3">
                            <div class="form-check m-0">
                                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $doc->id }}">
                            </div>
                        </td>
                        <td class="text-muted d-mobile-none">{{ $documents->firstItem() + $i }}</td>
                        <td class="d-mobile-none">
                            <code class="text-sipsr-primary fw-semibold">{{ $doc->nomor_dokumen }}</code>
                        </td>
                        <td>
                            <a href="{{ route('dokumen.show', $doc) }}" class="text-dark text-decoration-none fw-medium">
                                {{ Str::limit($doc->nama_dokumen, 45) }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-sipsr-primary bg-opacity-10 text-sipsr-light">
                                {{ $doc->category->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $doc->tanggal_dokumen?->format('d/m/Y') }}</td>
                        <td class="small d-mobile-none">{{ $doc->uploader->nama_lengkap ?? '-' }}</td>
                        <td class="text-center text-nowrap">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('dokumen.show', $doc) }}"
                                   class="btn btn-outline-secondary" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('dokumen.download', $doc) }}"
                                   class="btn btn-outline-success" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                                <form method="POST" action="{{ route('dokumen.destroy', $doc) }}"
                                      class="d-inline" onsubmit="return confirm('Hapus dokumen ini ke Recycle Bin?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada dokumen.
                            <a href="{{ route('dokumen.create') }}">Upload sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer Actions / Pagination --}}
    <div class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center align-items-center w-100 position-relative" style="min-height: 80px;">
        @if($documents->hasPages())
            {{ $documents->links('vendor.pagination.bootstrap-5') }}
        @endif
    </div>

    <x-scroll-to-top />
</div>

@endsection

@push('scripts')

<script>
// Bulk Actions Logic
const selectAllCheckbox = document.getElementById('selectAllCheckbox');
const rowCheckboxes = document.querySelectorAll('.row-checkbox');
const bulkActions = document.getElementById('bulk-actions');
const selectedCount = document.getElementById('selected-count');

function updateBulkActions() {
    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
    selectedCount.textContent = checkedCount;
    if (checkedCount > 0) {
        bulkActions.classList.remove('d-none');
        bulkActions.classList.add('d-flex');
    } else {
        bulkActions.classList.remove('d-flex');
        bulkActions.classList.add('d-none');
    }
}

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });
}

rowCheckboxes.forEach(cb => {
    cb.addEventListener('change', function() {
        if (!this.checked && selectAllCheckbox.checked) {
            selectAllCheckbox.checked = false;
        }
        const allChecked = document.querySelectorAll('.row-checkbox:checked').length === rowCheckboxes.length;
        if (allChecked && rowCheckboxes.length > 0) {
            selectAllCheckbox.checked = true;
        }
        updateBulkActions();
    });
});

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
}

function submitBulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    
    if (confirm(`Apakah Anda yakin ingin menghapus ${ids.length} dokumen yang dipilih ke Recycle Bin?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("dokumen.bulk-delete") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function submitBulkDownload() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("dokumen.bulk-download") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    window.updatePerPage = function(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', val);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    };
    
    // Real-Time Search Debounce (300ms)
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = document.getElementById('mainFilterForm');
    
    if (searchInput && searchForm) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            // Submit jika input ada isinya atau kosong (untuk mereset)
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 300);
        });
        
        // Auto-focus search (optional, we check if it has value to place cursor at end)
        if(searchInput.value) {
            const length = searchInput.value.length;
            searchInput.setSelectionRange(length, length);
        }
    }

    // Date Preset Logic
    window.setDateRange = function(daysBack, daysForward) {
        const today = new Date();
        const fromDate = new Date(today);
        fromDate.setDate(fromDate.getDate() - daysBack);
        
        const toDate = new Date(today);
        toDate.setDate(toDate.getDate() + daysForward);
        
        document.getElementById('tanggal_dari').value = fromDate.toISOString().split('T')[0];
        document.getElementById('tanggal_sampai').value = toDate.toISOString().split('T')[0];
    };
});


</script>
@endpush
