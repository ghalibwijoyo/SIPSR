@extends('layouts.guest')

@section('title', 'Akses Ditolak (403) — ArsiPSR')

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 text-center py-5">
    <div class="mb-4">
        <div class="d-inline-flex justify-content-center align-items-center bg-danger bg-opacity-10 text-danger rounded-circle p-4 mb-3">
            <i class="bi bi-shield-lock-fill" style="font-size: 4rem;"></i>
        </div>
    </div>
    <h1 class="display-3 fw-bold text-dark mb-2">403</h1>
    <h4 class="fw-semibold text-danger mb-3">Akses Ditolak</h4>
    <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
        Maaf, akun Anda tidak memiliki hak akses (role: <strong>ADMIN</strong>) untuk melihat halaman ini. 
        Jika ini adalah kesalahan, silakan hubungi Administrator.
    </p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
    </a>
</div>
@endsection
