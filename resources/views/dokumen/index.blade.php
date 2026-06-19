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

        {{-- Per page selector --}}
        <div class="d-flex align-items-center gap-2">
            <label for="per-page" class="form-label mb-0 small text-muted">Tampilkan:</label>
            <select class="form-select form-select-sm" id="per-page" style="width: auto;"
                    onchange="updatePerPage(this.value)">
                @foreach([10, 20, 50] as $pp)
                    <option value="{{ $pp }}" {{ request('per_page', 20) == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" id="dokumen-table">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>
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
                        <th>Uploader</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $i => $doc)
                    <tr>
                        <td class="ps-3 text-muted">{{ $documents->firstItem() + $i }}</td>
                        <td>
                            <code class="text-sipsr-primary fw-semibold">{{ $doc->nomor_dokumen }}</code>
                        </td>
                        <td>
                            <a href="{{ route('dokumen.show', $doc) }}" class="text-dark text-decoration-none fw-medium">
                                {{ Str::limit($doc->nama_dokumen, 45) }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-sipsr-primary bg-opacity-10 text-sipsr-primary">
                                {{ $doc->category->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $doc->tanggal_dokumen?->format('d/m/Y') }}</td>
                        <td class="small">{{ $doc->uploader->nama_lengkap ?? '-' }}</td>
                        <td class="text-center">
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

    {{-- Pagination --}}
    @if($documents->hasPages())
    <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $documents->firstItem() }}–{{ $documents->lastItem() }}
            dari {{ $documents->total() }} dokumen
        </small>
        {{ $documents->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>
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
