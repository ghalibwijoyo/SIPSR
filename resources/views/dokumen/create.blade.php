@extends('layouts.app')

@section('title', 'Upload Dokumen — SIPSR')

@section('content')
{{-- Page Header --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Upload Dokumen</h1>
        <p class="text-muted mb-0">Tambahkan dokumen baru ke arsip</p>
    </div>
    <a href="{{ route('dokumen.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-cloud-arrow-up me-2 text-sipsr-primary"></i>Form Upload
                </h5>
            </div>
            <div class="card-body p-4">
                {{-- Duplicate Warning --}}
                @if(session('warning_duplikat'))
                <div class="alert alert-warning d-flex align-items-start" role="alert" id="alert-duplikat">
                    <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                    <div>
                        <strong>Nomor dokumen sudah ada!</strong><br>
                        Nomor ini sudah digunakan dokumen lain.
                        Klik <strong>Upload</strong> sekali lagi untuk tetap menyimpan.
                    </div>
                </div>
                @endif

                {{-- Validation Errors --}}
                @if($errors->any() && !session('warning_duplikat'))
                <div class="alert alert-danger" role="alert" id="alert-errors">
                    <i class="bi bi-exclamation-circle-fill me-1"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('dokumen.store') }}" enctype="multipart/form-data" id="upload-form">
                    @csrf

                    {{-- Hidden flag for duplicate confirmation --}}
                    @if(session('warning_duplikat'))
                        <input type="hidden" name="konfirmasi_duplikat" value="1">
                    @endif

                    <div class="row g-3">
                        {{-- Nomor Dokumen --}}
                        <div class="col-md-6">
                            <label for="nomor_dokumen" class="form-label fw-semibold">
                                Nomor Dokumen <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('nomor_dokumen') is-invalid @enderror"
                                   id="nomor_dokumen" name="nomor_dokumen"
                                   value="{{ old('nomor_dokumen') }}"
                                   placeholder="Contoh: SM/001/PSR/2026" required>
                            @error('nomor_dokumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Dokumen --}}
                        <div class="col-md-6">
                            <label for="nama_dokumen" class="form-label fw-semibold">
                                Nama Dokumen <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('nama_dokumen') is-invalid @enderror"
                                   id="nama_dokumen" name="nama_dokumen"
                                   value="{{ old('nama_dokumen') }}"
                                   placeholder="Nama / judul dokumen" required>
                            @error('nama_dokumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category_id') is-invalid @enderror"
                                    id="category_id" name="category_id" required>
                                <option value="">— Pilih Kategori —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Dokumen --}}
                        <div class="col-md-6">
                            <label for="tanggal_dokumen" class="form-label fw-semibold">
                                Tanggal Dokumen <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('tanggal_dokumen') is-invalid @enderror"
                                   id="tanggal_dokumen" name="tanggal_dokumen"
                                   value="{{ old('tanggal_dokumen') }}" required>
                            @error('tanggal_dokumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="col-12">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                      id="deskripsi" name="deskripsi" rows="3"
                                      placeholder="Deskripsi dokumen (opsional)">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- File Upload --}}
                        <div class="col-12">
                            <label for="file" class="form-label fw-semibold">
                                File Dokumen <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                   id="file" name="file"
                                   accept=".pdf,.doc,.docx" required>
                            <div class="form-text">Format: PDF, DOC, DOCX · Maks: 500 MB</div>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- File preview info --}}
                            <div class="mt-2 d-none" id="file-info">
                                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                    <i class="bi bi-file-earmark-pdf text-danger fs-4" id="file-icon"></i>
                                    <div>
                                        <p class="mb-0 small fw-semibold" id="file-name-display"></p>
                                        <small class="text-muted" id="file-size-display"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('dokumen.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-success px-4" id="btn-submit-upload">
                            <i class="bi bi-cloud-arrow-up me-1"></i>Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('file')?.addEventListener('change', function () {
    const info    = document.getElementById('file-info');
    const nameEl  = document.getElementById('file-name-display');
    const sizeEl  = document.getElementById('file-size-display');
    const iconEl  = document.getElementById('file-icon');

    if (this.files.length > 0) {
        const file = this.files[0];
        const ext  = file.name.split('.').pop().toLowerCase();

        nameEl.textContent = file.name;
        sizeEl.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';

        iconEl.className = ext === 'pdf'
            ? 'bi bi-file-earmark-pdf text-danger fs-4'
            : 'bi bi-file-earmark-word text-primary fs-4';

        info.classList.remove('d-none');
    } else {
        info.classList.add('d-none');
    }
});
</script>
@endpush
