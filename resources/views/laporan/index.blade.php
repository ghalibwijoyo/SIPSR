@extends('layouts.app')

@section('title', 'Laporan Dokumen — SIPSR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Dokumen</h1>
</div>

<div class="row g-4">
    <!-- Filter Card -->
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-funnel me-1"></i> Filter Periode Laporan</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('laporan.index') }}" method="GET" id="form-laporan" class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="btn-group" role="group" aria-label="Filter Periode">
                        <button type="submit" name="periode" value="1_hari" class="btn {{ $periode === '1_hari' ? 'btn-primary' : 'btn-outline-secondary' }}">1 Hari</button>
                        <button type="submit" name="periode" value="1_minggu" class="btn {{ $periode === '1_minggu' ? 'btn-primary' : 'btn-outline-secondary' }}">1 Minggu</button>
                        <button type="submit" name="periode" value="1_bulan" class="btn {{ $periode === '1_bulan' ? 'btn-primary' : 'btn-outline-secondary' }}">1 Bulan</button>
                        <button type="submit" name="periode" value="1_tahun" class="btn {{ $periode === '1_tahun' ? 'btn-primary' : 'btn-outline-secondary' }}">1 Tahun</button>
                        <button type="submit" name="periode" value="5_tahun" class="btn {{ $periode === '5_tahun' ? 'btn-primary' : 'btn-outline-secondary' }}">5 Tahun</button>
                        <button type="submit" name="periode" value="semua" class="btn {{ $periode === 'semua' ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Preview -->
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="m-0 font-weight-bold text-dark d-inline-block">Pratinjau Data</h6>
                    <span class="badge bg-success ms-2 px-2 py-1">Ditemukan {{ $totalDokumen }} dokumen</span>
                </div>
                
                @if($totalDokumen > 0)
                <div class="d-flex gap-2">
                    <form action="{{ route('laporan.export.excel') }}" method="GET" class="d-inline">
                        <input type="hidden" name="periode" value="{{ $periode }}">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                        </button>
                    </form>
                    
                    <form action="{{ route('laporan.export.pdf') }}" method="GET" class="d-inline">
                        <input type="hidden" name="periode" value="{{ $periode }}">
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                        </button>
                    </form>

                    <form action="{{ route('laporan.print.pdf') }}" method="GET" class="d-inline" target="_blank">
                        <input type="hidden" name="periode" value="{{ $periode }}">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-printer me-1"></i> Cetak PDF
                        </button>
                    </form>
                </div>
                @endif
            </div>
            
            <div class="card-body p-0">
                @if($totalDokumen > 0)
                    <div class="alert alert-info border-0 rounded-0 mb-0 py-2 small">
                        <i class="bi bi-info-circle me-1"></i> Menampilkan maksimal 10 baris pertama untuk pratinjau. Silakan *Export* atau *Cetak* untuk melihat seluruh data.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-center" style="width: 50px;">No</th>
                                    <th>Nomor Dokumen</th>
                                    <th>Nama Dokumen</th>
                                    <th>Kategori</th>
                                    <th>Tanggal</th>
                                    <th class="pe-3">Uploader</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dokumenPreview as $index => $doc)
                                <tr>
                                    <td class="ps-3 text-center text-muted small">{{ $index + 1 }}</td>
                                    <td class="small"><code class="text-dark fw-bold">{{ $doc->nomor_dokumen }}</code></td>
                                    <td class="small">{{ $doc->nama_dokumen }}</td>
                                    <td class="small"><span class="badge bg-secondary">{{ $doc->category->nama ?? '-' }}</span></td>
                                    <td class="small">{{ $doc->tanggal_dokumen?->format('d/m/Y') }}</td>
                                    <td class="pe-3 small">{{ $doc->uploader->nama_lengkap ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-search fs-1 text-secondary d-block mb-3"></i>
                        <h6 class="fw-semibold">Tidak Ada Data</h6>
                        <p class="small mb-0">Tidak ada dokumen yang ditemukan untuk periode yang dipilih.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
