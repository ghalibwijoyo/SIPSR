@extends('layouts.app')

@section('title', 'Recycle Bin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Recycle Bin</h1>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4" id="filter-card">
    <div class="card-body">
        <form method="GET" action="{{ route('recycle-bin.index') }}" id="filter-form">
            <div class="row g-3 align-items-end">
                {{-- Search --}}
                <div class="col-md-4">
                    <label for="filter-search" class="form-label small fw-semibold">
                        <i class="bi bi-search me-1"></i>Pencarian
                    </label>
                    <input type="text" class="form-control" id="filter-search" name="search"
                           value="{{ request('search') }}"
                           placeholder="Nomor atau nama dokumen…">
                </div>

                {{-- Category --}}
                <div class="col-md-2">
                    <label for="filter-category" class="form-label small fw-semibold">
                        <i class="bi bi-folder me-1"></i>Kategori
                    </label>
                    <select class="form-select" id="filter-category" name="category_id">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->nama }}
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
                    <a href="{{ route('recycle-bin.index') }}" class="btn btn-secondary" id="btn-reset" title="Reset filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="bi bi-trash3 me-2 text-sipsr-primary"></i>
            Dokumen Terhapus
            <span class="badge bg-secondary ms-1">{{ $documents->total() }}</span>
        </h5>
        
        <div class="d-flex align-items-center gap-3">
            {{-- Bulk Actions (Hidden by default) --}}
            <div id="bulk-actions" class="d-none align-items-center gap-2">
                <span class="small text-muted me-2"><span id="selected-count">0</span> terpilih</span>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="submitBulkRestore()" title="Restore Terpilih">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
                @if(auth()->user()->role === 'ADMIN')
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="submitBulkDelete()" title="Hapus Permanen Terpilih">
                    <i class="bi bi-trash"></i>
                </button>
                @endif
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

            @if(auth()->user()->role === 'ADMIN' && $documents->count() > 0)
            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#emptyModal">
                <i class="bi bi-trash3 me-1"></i> Kosongkan Recycle Bin
            </button>
            @endif
        </div>
    </div>
    <div class="card-body border-bottom">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                        </th>
                        <th style="width: 50px;">No</th>
                        <th>Nama Dokumen</th>
                        <th>Kategori</th>
                        <th>Dihapus Oleh</th>
                        <th>Tanggal Dihapus</th>
                        <th class="text-end text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $i => $doc)
                    <tr>
                        <td>
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $doc->id }}">
                        </td>
                        <td class="text-muted">{{ $documents->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-bold">{{ $doc->nama_dokumen }}</div>
                            <div class="small text-muted">{{ $doc->nomor_dokumen }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $doc->category->nama ?? '-' }}</span>
                        </td>
                        <td>{{ $doc->deletedBy->nama_lengkap ?? 'Sistem' }}</td>
                        <td>{{ $doc->deleted_at->format('d M Y H:i') }}</td>
                        <td class="text-end text-nowrap">
                            <form action="{{ route('recycle-bin.restore', $doc->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                </button>
                            </form>
                            
                            @if(auth()->user()->role === 'ADMIN')
                            <button type="button" class="btn btn-sm btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $doc->id }}" title="Hapus Permanen">
                                <i class="bi bi-trash"></i>
                            </button>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $doc->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $doc->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content text-start">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $doc->id }}">Hapus Permanen Dokumen</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah Anda yakin ingin menghapus dokumen <strong>{{ $doc->nama_dokumen }}</strong> secara permanen?
                                            <div class="alert alert-danger mt-3 mb-0">
                                                <i class="bi bi-exclamation-triangle-fill me-2"></i> File fisik akan dihapus dan tidak dapat dipulihkan.
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('recycle-bin.destroy', $doc->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus Permanen</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr id="empty-state">
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-trash fs-1 text-muted mb-3 d-block"></i>
                            <h6 class="text-muted">Recycle Bin kosong</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>
    <x-scroll-to-top />

    <div class="card-footer bg-white border-top-0 pb-3 pt-2 d-flex justify-content-center align-items-center w-100 position-relative" style="min-height: 60px;">
        @if($documents->hasPages())
            {{ $documents->links('vendor.pagination.bootstrap-5') }}
        @endif
    </div>
</div>

<!-- Empty Modal -->
@if(auth()->user()->role === 'ADMIN')
<div class="modal fade" id="emptyModal" tabindex="-1" aria-labelledby="emptyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="emptyModalLabel">Kosongkan Recycle Bin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin mengosongkan Recycle Bin?
                <div class="alert alert-danger mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Semua file fisik dan data dokumen akan dihapus secara permanen dan tidak dapat dipulihkan.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('recycle-bin.empty') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Kosongkan Semua</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<style>
.scroll-top-wrapper {
    position: sticky; 
    bottom: 30px; /* Jarak melayang dari bawah viewport */
    z-index: 1050; 
    pointer-events: none;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    opacity: 0;
    visibility: hidden;
    transform: translateY(30px);
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55); /* Efek memantul khas SIPSR */
}
.scroll-top-wrapper.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
</style>
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

function submitBulkRestore() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    
    if (confirm(`Apakah Anda yakin ingin memulihkan ${ids.length} dokumen yang dipilih?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("recycle-bin.bulk-restore") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'document_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function submitBulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    
    if (confirm(`Peringatan!\n\nApakah Anda yakin ingin MENGHAPUS SECARA PERMANEN ${ids.length} dokumen yang dipilih?\nFile fisik juga akan dihapus dan tidak dapat dipulihkan!`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("recycle-bin.bulk-delete") }}';
        
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
            input.name = 'document_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function updatePerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// Scroll to top visibility
const filterCard = document.getElementById('filter-card');
const scrollTopBtn = document.getElementById('scrollTopBtn');

if (filterCard && scrollTopBtn) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.classList.remove('show');
            }
        });
    }, { root: document.querySelector('main'), threshold: 0 });
    
    observer.observe(filterCard);
}
</script>
@endpush
