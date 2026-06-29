@extends('layouts.app')

@section('title', 'Manajemen Kategori — ArsiPSR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Manajemen Kategori</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Nama Kategori</th>
                        <th class="px-4 py-3 text-center">Jumlah Dokumen</th>
                        <th class="px-4 py-3 text-end text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-4 py-3 fw-medium">{{ $category->nama }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-secondary rounded-pill">{{ $category->documents_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-light btn-icon text-primary me-1" 
                                    data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}" title="Edit Kategori">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light btn-icon text-danger" 
                                    data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}" title="Hapus Kategori">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>


                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Belum ada kategori yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-center align-items-center w-100">
                {{ $categories->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@foreach($categories as $category)
    {{-- Edit Category Modal --}}
    <div class="modal fade text-start" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="nama" class="form-control" value="{{ $category->nama }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Category Modal --}}
    <div class="modal fade text-start" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <div class="mb-3">
                        @if($category->documents_count > 0)
                            <i class="bi bi-exclamation-triangle text-warning fs-1"></i>
                        @else
                            <i class="bi bi-trash text-danger fs-1"></i>
                        @endif
                    </div>
                    <h5 class="mb-2">Hapus Kategori?</h5>
                    
                    @if($category->documents_count > 0)
                        <p class="text-muted mb-4">Kategori <strong>{{ $category->nama }}</strong> sedang digunakan oleh {{ $category->documents_count }} dokumen. Anda tidak dapat menghapus kategori ini.</p>
                        <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Mengerti</button>
                    @else
                        <p class="text-muted mb-4">Anda yakin ingin menghapus kategori <strong>{{ $category->nama }}</strong> secara permanen?</p>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Add Category Modal --}}
<div class="modal fade text-start" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Contoh: Surat Keputusan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
