@extends('layouts.guest')

@section('title', 'Login — SIPSR')

@section('content')
{{-- Antigravity Blob --}}
<div id="blob"></div>
<div id="blur"></div>

<div class="login-container position-relative" style="z-index: 2;">
    <div class="card login-card shadow-lg border-0" style="background-color: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px);">
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
                    <label for="username">Username</label>
                </div>

                <div class="form-floating mb-4 position-relative">
                    <input
                        type="password"
                        class="form-control pe-5"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >
                    <label for="password">Password</label>
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

    // Antigravity Mouse Follower
    const blob = document.getElementById("blob");
    window.addEventListener('pointermove', event => { 
        const { clientX, clientY } = event;
        blob.animate({
            left: `${clientX}px`,
            top: `${clientY}px`
        }, { duration: 3000, fill: "forwards" });
    });
</script>

<style>
/* Antigravity Blob Styles */
body.bg-sipsr-gradient {
    /* Make the base background a bit darker so the glowing blob pops out */
    background: #0f1c05 !important; 
    margin: 0;
    overflow: hidden;
}

#blob {
    background: linear-gradient(to right, #4a8a15, #8bc34a); /* SIPSR Greens */
    height: 400px;
    aspect-ratio: 1;
    position: absolute;
    left: 50%;
    top: 50%;
    translate: -50% -50%;
    border-radius: 50%;
    animation: rotateBlob 20s infinite;
    opacity: 0.6;
    z-index: 0;
    filter: blur(80px);
}

@keyframes rotateBlob {
    from {
        rotate: 0deg;
    }
    50% {
        scale: 1 1.5;
    }
    to {
        rotate: 360deg;
    }
}
</style>
@endpush
