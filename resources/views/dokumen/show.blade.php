@extends('layouts.app')

@section('title', $dokumen->nama_dokumen . ' — SIPSR')

@section('content')
{{-- Page Header --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Detail Dokumen</h1>
        <p class="text-muted mb-0">
            <a href="{{ route('dokumen.index') }}" class="text-decoration-none">Dokumen</a>
            <i class="bi bi-chevron-right mx-1 small"></i>
            {{ Str::limit($dokumen->nama_dokumen, 40) }}
        </p>
    </div>
    <a href="{{ route('dokumen.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row g-4">
    {{-- Left: Metadata --}}
    <div class="col-lg-5">
        {{-- Document Info Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-info-circle me-2 text-sipsr-primary"></i>Informasi Dokumen
                </h5>
                <span class="badge bg-sipsr-primary">{{ $dokumen->category->nama ?? '-' }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold" style="width: 140px;">Nomor</td>
                            <td><code class="text-sipsr-primary">{{ $dokumen->nomor_dokumen }}</code></td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">Nama</td>
                            <td class="fw-medium">{{ $dokumen->nama_dokumen }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">Kategori</td>
                            <td>
                                <span class="badge bg-sipsr-primary bg-opacity-10 text-sipsr-primary">
                                    {{ $dokumen->category->nama ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">Tanggal</td>
                            <td>{{ $dokumen->tanggal_dokumen?->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">Deskripsi</td>
                            <td>{{ $dokumen->deskripsi ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">File</td>
                            <td>
                                <i class="bi bi-file-earmark-{{ Str::endsWith($dokumen->file_name, '.pdf') ? 'pdf text-danger' : 'word text-primary' }} me-1"></i>
                                {{ $dokumen->file_name }}
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">Diupload oleh</td>
                            <td>{{ $dokumen->uploader->nama_lengkap ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">Tanggal Upload</td>
                            <td class="small">{{ $dokumen->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($dokumen->updatedBy)
                        <tr>
                            <td class="ps-3 text-muted fw-semibold">Terakhir Diedit</td>
                            <td class="small">
                                {{ $dokumen->updatedBy->nama_lengkap }}
                                · {{ $dokumen->updated_at?->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('dokumen.download', $dokumen) }}" class="btn btn-success" id="btn-download">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                    <a href="{{ route('dokumen.edit', $dokumen) }}" class="btn btn-warning" id="btn-edit">
                        <i class="bi bi-pencil-square me-1"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('dokumen.destroy', $dokumen) }}"
                          class="d-inline" onsubmit="return confirm('Hapus dokumen ini ke Recycle Bin?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" id="btn-delete">
                            <i class="bi bi-trash3 me-1"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Change History --}}
        @if($dokumen->histories->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-2 text-sipsr-primary"></i>Riwayat Perubahan
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Field</th>
                                <th>Sebelum</th>
                                <th>Sesudah</th>
                                <th>Oleh</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dokumen->histories->sortByDesc('changed_at') as $hist)
                            <tr>
                                <td class="ps-3 fw-semibold small">{{ $hist->field_name }}</td>
                                <td class="small text-danger">{{ Str::limit($hist->old_value, 30) ?: '-' }}</td>
                                <td class="small text-success">{{ Str::limit($hist->new_value, 30) ?: '-' }}</td>
                                <td class="small">{{ $hist->changedBy->nama_lengkap ?? '-' }}</td>
                                <td class="small text-muted">{{ $hist->changed_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Right: File Preview --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-eye me-2 text-sipsr-primary"></i>Preview Dokumen
                </h5>
            </div>
            <div class="card-body p-0">
                @if(Str::endsWith($dokumen->file_name, '.pdf'))
                    <iframe src="{{ route('dokumen.preview', $dokumen) }}"
                            class="w-100 border-0" style="height: 700px;"
                            title="Preview {{ $dokumen->nama_dokumen }}" id="pdf-preview"></iframe>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-file-earmark-word fs-1 text-primary d-block mb-3"></i>
                        <p class="mb-1 fw-semibold">Preview tidak tersedia</p>
                        <p class="small mb-3">Format DOC/DOCX tidak dapat ditampilkan langsung di browser.</p>
                        <a href="{{ route('dokumen.download', $dokumen) }}" class="btn btn-success btn-sm">
                            <i class="bi bi-download me-1"></i>Download untuk melihat
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
