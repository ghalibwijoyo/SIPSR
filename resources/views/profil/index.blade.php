@extends('layouts.app')

@section('title', 'Profil Saya — ArsiPSR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Profil Akun</h4>
</div>

<div class="row">
    {{-- Card Profil --}}
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-5">
                <div class="d-inline-flex justify-content-center align-items-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ $user->nama_lengkap }}</h5>
                <p class="text-muted mb-3">{{ $user->username }}</p>
                
                <div class="d-flex justify-content-center gap-2 mb-0">
                    <span class="badge {{ $user->role === 'ADMIN' ? 'bg-danger' : 'bg-primary' }}">{{ $user->role }}</span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Edit Profil --}}
    <div class="col-md-8 mb-4">
        {{-- Alert Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Informasi Dasar</h6>
            </div>
            <div class="card-body pt-0">
                <form action="{{ route('profil.update-nama') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label text-muted">Username</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control bg-light" value="{{ $user->username }}" readonly>
                            <small class="form-text text-muted">Username digunakan untuk login dan tidak dapat diubah.</small>
                        </div>
                    </div>
                    
                    <div class="mb-4 row">
                        <label class="col-sm-3 col-form-label">Nama Lengkap</label>
                        <div class="col-sm-9">
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ $user->nama_lengkap }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary">Simpan Nama</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-lock-fill me-2 text-warning"></i>Ganti Password</h6>
            </div>
            <div class="card-body pt-0">
                <form action="{{ route('profil.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Password Lama</label>
                        <div class="col-sm-9">
                            <input type="password" name="password_lama" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Password Baru</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="password" name="password_baru" class="form-control" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Minimal 8 karakter, harus mengandung huruf dan angka.</small>
                        </div>
                    </div>

                    <div class="mb-4 row">
                        <label class="col-sm-3 col-form-label">Konfirmasi Password</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="password" name="password_baru_confirmation" class="form-control" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-warning">Ganti Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(button) {
        const input = button.previousElementSibling;
        const icon = button.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
@endpush
