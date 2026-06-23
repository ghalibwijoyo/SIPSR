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

{{-- Filter Card --}}
<div class="card border-0 shadow-sm mb-4" id="filter-card">
    <div class="card-body">
        <form method="GET" action="{{ route('dokumen.index') }}" id="filter-form">
            <div class="row g-3 align-items-end">
                {{-- Search --}}
                <div class="col-md-4">
                    <label for="filter-search" class="form-label small fw-semibold">
                        <i class="bi bi-search me-1"></i>Pencarian
                    </label>
                    <input type="text" class="form-control" id="filter-search" name="search"
                           value="{{ request('search') }}"
                           placeholder="Nomor, nama dokumen, atau uploader…">
                </div>

                {{-- Category --}}
                <div class="col-md-2">
                    <label for="filter-category" class="form-label small fw-semibold">
                        <i class="bi bi-folder me-1"></i>Kategori
                    </label>
                    <select class="form-select" id="filter-category" name="category_id">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Range --}}
                <div class="col-md-2">
                    <label for="filter-tanggal-dari" class="form-label small fw-semibold">
                        <i class="bi bi-calendar-event me-1"></i>Dari Tanggal
                    </label>
                    <input type="date" class="form-control" id="filter-tanggal-dari" name="tanggal_dari"
                           value="{{ request('tanggal_dari') }}">
                </div>
                <div class="col-md-2">
                    <label for="filter-tanggal-sampai" class="form-label small fw-semibold">
                        Sampai Tanggal
                    </label>
                    <input type="date" class="form-control" id="filter-tanggal-sampai" name="tanggal_sampai"
                           value="{{ request('tanggal_sampai') }}">
                </div>

                {{-- Buttons --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-grow-1" id="btn-filter">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('dokumen.index') }}" class="btn btn-secondary" id="btn-reset" title="Reset filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
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

    <div class="card-body p-0">
        <div class="table-responsive">
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
    <div class="card-footer bg-white border-top py-3 d-flex justify-content-center align-items-center w-100 position-relative" style="min-height: 60px;">
        @if($documents->hasPages())
            {{ $documents->links('vendor.pagination.bootstrap-5') }}
        @endif

        {{-- Scroll to Top Button --}}
        <button type="button" onclick="document.querySelector('main').scrollTo({top: 0, behavior: 'smooth'})" 
                class="btn bg-sipsr-primary text-white btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center position-absolute" 
                title="Kembali ke atas" 
                style="width: 38px; height: 38px; right: 1.5rem; transition: all 0.2s ease;" 
                onmouseover="this.style.transform='translateY(-3px)';" 
                onmouseout="this.style.transform='translateY(0)';">
            <i class="bi bi-arrow-up fs-5"></i>
        </button>
    </div>
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

function updatePerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}
</script>
@endpush
