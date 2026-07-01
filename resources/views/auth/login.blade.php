@extends ('layouts.guest')

@section ('title', 'Login — ArsiPSR')

@section ('content')
    <div class="login-container position-relative" style="z-index: 2">
        <div
            class="card login-card shadow-lg border-0"
            style="
                background-color: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
            "
        >
            <div class="card-body p-4 p-md-5">
                {{-- Logo --}}
                <div class="text-center mb-4">
                    <div
                        class="login-logo mx-auto mb-3"
                        style="background: transparent; box-shadow: none"
                    >
                        <img
                            src="{{ asset('logo.png') }}"
                            alt="ArsiPSR Logo"
                            style="
                                height: 64px;
                                width: auto;
                                object-fit: contain;
                            "
                        />
                    </div>
                    <h1 class="h3 fw-bold text-dark mb-1">ArsiPSR</h1>
                    <p class="text-muted small mb-0">Sistem Informasi Pengarsipan<br />
                    PSR Tanaman — PTPN IV Regional IV</p>
                </div>

                {{-- Error Alert Container --}}
                <div id="login-error-container">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            {{ $errors->first() }}
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>

                {{-- Login Form --}}
                <form
                    method="POST"
                    action="{{ route('login') }}"
                    id="login-form"
                >
                    @csrf

                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            class="form-control @error('nik') is-invalid @enderror"
                            id="nik"
                            name="nik"
                            value="{{ old('nik') }}"
                            placeholder="Masukkan NIK"
                            autofocus
                            required
                        />
                        <label for="nik">NIK</label>
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input
                            type="password"
                            class="form-control pe-5"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                        />
                        <label for="password">Password</label>
                        <button
                            class="btn btn-link text-secondary position-absolute end-0 top-50 translate-middle-y text-decoration-none shadow-none"
                            type="button"
                            id="toggle-password"
                            title="Tampilkan password"
                            style="z-index: 5"
                        >
                            <i
                                class="bi bi-eye-fill"
                                id="toggle-password-icon"
                            ></i>
                        </button>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary btn-lg w-100 fw-semibold"
                        id="btn-login"
                    >
                        <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                    </button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted"
                        >&copy; {{ date('Y') }} PTPN IV Regional IV</small
                    >
                </div>
            </div>
        </div>
    </div>
@endsection

@push ('scripts')
    <script>
        document
            .getElementById("toggle-password")
            ?.addEventListener("click", function () {
                const input = document.getElementById("password");
                const icon = document.getElementById("toggle-password-icon");
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.replace("bi-eye-fill", "bi-eye-slash-fill");
                } else {
                    input.type = "password";
                    icon.classList.replace("bi-eye-slash-fill", "bi-eye-fill");
                }
            });

        // AJAX Login Form Handler
        const loginForm = document.getElementById('login-form');
        const btnLogin = document.getElementById('btn-login');
        const errorContainer = document.getElementById('login-error-container');

        if (loginForm) {
            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(loginForm);
                
                // Set loading state
                btnLogin.disabled = true;
                btnLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...';
                errorContainer.innerHTML = ''; // Clear previous error
                
                try {
                    const response = await fetch(loginForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok && data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // Tampilkan error
                        const errorMsg = data.message || 'Terjadi kesalahan saat login.';
                        errorContainer.innerHTML = `
                            <div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                ${errorMsg}
                                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        
                        // Picu animasi denyut merah di canvas
                        window.dispatchEvent(new Event('loginFailed'));
                    }
                } catch (error) {
                    errorContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Terjadi kesalahan koneksi.
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    window.dispatchEvent(new Event('loginFailed'));
                } finally {
                    // Reset button state
                    btnLogin.disabled = false;
                    btnLogin.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i>Masuk';
                }
            });
        }
    </script>
@endpush
