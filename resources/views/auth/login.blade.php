@extends('layouts.guest')

@section('title', 'Login — SIPSR')

@section('content')
<div class="login-container">
    <div class="card login-card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            {{-- Logo --}}
            <div class="text-center mb-4">
                <div class="login-logo mx-auto mb-3">
                    <span class="login-logo-text">SI</span>
                </div>
                <h1 class="h3 fw-bold text-dark mb-1">SIPSR</h1>
                <p class="text-muted small mb-0">
                    Sistem Informasi Pengarsipan<br>
                    PSR Tanaman — PTPN IV Regional IV
                </p>
            </div>

            {{-- Error Alert --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert" id="login-error-alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    {{ $errors->first() }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <div class="form-floating mb-3">
                    <input
                        type="text"
                        class="form-control @error('username') is-invalid @enderror"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username"
                        autofocus
                        required
                    >
                    <label for="username">
                        <i class="bi bi-person-fill me-1"></i>Username
                    </label>
                </div>

                <div class="form-floating mb-4 position-relative">
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >
                    <label for="password">
                        <i class="bi bi-lock-fill me-1"></i>Password
                    </label>
                    <button class="btn btn-link text-secondary position-absolute end-0 top-50 translate-middle-y text-decoration-none shadow-none" 
                        type="button" id="toggle-password" title="Tampilkan password" style="z-index: 5;">
                        <i class="bi bi-eye-fill" id="toggle-password-icon"></i>
                    </button>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold" id="btn-login">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                </button>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">&copy; {{ date('Y') }} PTPN IV Regional IV</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('toggle-password')?.addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon  = document.getElementById('toggle-password-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
        }
    });
</script>
@endpush
