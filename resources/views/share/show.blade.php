<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $dokumen->nama_dokumen }} ÔÇö Tautan Berbagi SIPSR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fc;
            min-height: 100vh;
        }
        .share-header {
            background: linear-gradient(135deg, #3B6D11 0%, #5a9a2a 100%);
            color: #fff;
            padding: 1.5rem 0;
        }
        .share-badge {
            background: rgba(255,255,255,0.2);
            border-radius: 50rem;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="share-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h5 mb-1 fw-bold"><i class="bi bi-link-45deg me-1"></i>Tautan Berbagi SIPSR</h1>
                    <span class="share-badge"><i class="bi bi-clock me-1"></i>Berlaku sampai {{ $link->expired_at->format('d M Y H:i') }}</span>
                </div>
                <span class="badge bg-white text-dark">{{ $dokumen->category->nama ?? '-' }}</span>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row g-4">
            {{-- Left: Metadata --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="bi bi-info-circle me-2" style="color: #3B6D11;"></i>Informasi Dokumen
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="ps-3 text-muted fw-semibold" style="width: 120px;">Nomor</td>
                                    <td><code style="color: #3B6D11;">{{ $dokumen->nomor_dokumen }}</code></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 text-muted fw-semibold">Nama</td>
                                    <td class="fw-medium">{{ $dokumen->nama_dokumen }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 text-muted fw-semibold">Kategori</td>
                                    <td>
                                        <span class="badge" style="background: #3B6D11;">
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
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-top py-3 text-center">
                        <a href="{{ route('share.download', $link->token) }}" class="btn btn-success w-100">
                            <i class="bi bi-download me-1"></i>Download Dokumen
                        </a>
                    </div>
                </div>

                <div class="text-center text-muted small">
                    <i class="bi bi-shield-check me-1"></i>Dibagikan melalui SIPSR
                </div>
            </div>

            {{-- Right: Preview --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="bi bi-eye me-2" style="color: #3B6D11;"></i>Preview Dokumen
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if(Str::endsWith($dokumen->file_name, '.pdf'))
                            <iframe src="{{ route('share.preview', $link->token) }}"
                                    class="w-100 border-0" style="height: 700px;"
                                    title="Preview {{ $dokumen->nama_dokumen }}"></iframe>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-word fs-1 text-primary d-block mb-3"></i>
                                <p class="mb-1 fw-semibold">Preview tidak tersedia</p>
                                <p class="small mb-3">Format DOC/DOCX tidak dapat ditampilkan langsung di browser.</p>
                                <a href="{{ route('share.download', $link->token) }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-download me-1"></i>Download untuk melihat
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
