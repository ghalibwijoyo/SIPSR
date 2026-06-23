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
            {{-- Per page selector --}}
            <div class="d-flex align-items-center gap-2">
                <label for="per-page" class="form-label mb-0 small text-muted">Tampilkan:</label>
                <select class="form-select form-select-sm" id="per-page" style="width: auto;"
                        onchange="updatePerPage(this.value)">
                    @foreach([100, 250, 500] as $pp)
                        <option value="{{ $pp }}" {{ request('per_page', 100) == $pp ? 'selected' : '' }}>{{ $pp }}</option>
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
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Dokumen</th>
                        <th>Kategori</th>
                        <th>Dihapus Oleh</th>
                        <th>Tanggal Dihapus</th>
                        <th class="text-end text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    <tr>
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
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Recycle Bin kosong
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>
    
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
<script>
function updatePerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}
</script>
@endpush
