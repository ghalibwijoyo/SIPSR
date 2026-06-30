@extends('layouts.share')

@section('title', 'Tautan Tidak Valid - ArsiPSR')

@section('styles')
        .error-card {
            max-width: 450px;
            width: 100%;
            border: none;
            border-top: 5px solid #dc3545;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
        }
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-center" style="height: 100vh">
    <div class="card error-card text-center p-4">
        <div class="card-body">
            <i class="bi bi-exclamation-circle error-icon mb-3 d-block"></i>
            <h4 class="card-title fw-bold text-dark mb-3">
                Tautan Tidak Valid
            </h4>
            <p class="card-text text-muted mb-4">{{ $message }}</p>

            <a
                href="{{ route('dashboard') }}"
                class="btn btn-primary px-4 py-2"
            >
                <i class="bi bi-house-door me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
