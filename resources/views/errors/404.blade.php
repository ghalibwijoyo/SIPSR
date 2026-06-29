@extends ('layouts.guest')

@section ('title', 'Halaman Tidak Ditemukan (404) — ArsiPSR')

@section ('content')
    <div
        class="container d-flex flex-column justify-content-center align-items-center min-vh-100 text-center py-5"
    >
        <div class="mb-4">
            <div
                class="d-inline-flex justify-content-center align-items-center bg-secondary bg-opacity-10 text-secondary rounded-circle p-4 mb-3"
            >
                <i class="bi bi-file-earmark-x" style="font-size: 4rem"></i>
            </div>
        </div>
        <h1 class="display-3 fw-bold text-dark mb-2">404</h1>
        <h4 class="fw-semibold text-secondary mb-3">Halaman Tidak Ditemukan</h4>
        <p class="text-muted mb-4 mx-auto" style="
                max-width: 500px;
            ">Maaf, halaman atau dokumen yang Anda cari mungkin telah dihapus, dipindahkan, atau alamat URL yang Anda masukkan salah.</p>
        <a
            href="{{ route('dashboard') }}"
            class="btn btn-primary px-4 py-2 fw-semibold shadow-sm"
        >
            <i class="bi bi-house-door me-2"></i>Kembali ke Beranda
        </a>
    </div>
@endsection
