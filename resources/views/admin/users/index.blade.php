@extends('layouts.app')

@section('title', 'Manajemen Pengguna — SIPSR')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Manajemen Pengguna</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
    </button>
</div>

{{-- Flash message for reset password --}}
@if(session('new_password_info'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-3 fs-3 text-success"></i>
            <div>
                <strong>Berhasil mereset password untuk {{ session('new_password_info')['username'] }}!</strong><br>
                Password baru: <code class="fs-6 px-2 py-1 bg-white border text-dark rounded user-select-all" id="newPasswordText">{{ session('new_password_info')['password'] }}</code>
                <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="copyPassword()">
                    <i class="bi bi-clipboard"></i> Copy
                </button>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Nama Lengkap</th>
                        <th class="px-4 py-3">Username</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-end text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4 py-3 fw-medium">{{ $user->nama_lengkap }}</td>
                            <td class="px-4 py-3">{{ $user->username }}</td>
                            <td class="px-4 py-3">
                                @if($user->role === 'ADMIN')
                                    <span class="badge bg-danger">ADMIN</span>
                                @else
                                    <span class="badge bg-primary">STAFF</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-light btn-icon text-primary me-1" 
                                    data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Edit Pengguna">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light btn-icon text-warning me-1" 
                                    data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $user->id }}" title="Reset Password">
                                    <i class="bi bi-key"></i>
                                </button>
                                @if(auth()->user()->id !== $user->id)
                                    <button type="button" class="btn btn-sm btn-light btn-icon {{ $user->is_active ? 'text-danger' : 'text-success' }}" 
                                        data-bs-toggle="modal" data-bs-target="#toggleStatusModal{{ $user->id }}" 
                                        title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi {{ $user->is_active ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>


                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-center align-items-center w-100">
                {{ $users->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@foreach($users as $user)
    {{-- Edit User Modal --}}
    <div class="modal fade text-start" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->username }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ $user->nama_lengkap }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="STAFF" {{ $user->role == 'STAFF' ? 'selected' : '' }}>STAFF</option>
                                <option value="ADMIN" {{ $user->role == 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select" required>
                                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reset Password Modal --}}
    <div class="modal fade text-start" id="resetPasswordModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.reset-password', $user->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <p class="text-muted small">Mereset password untuk <strong>{{ $user->username }}</strong>.</p>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="new_password" class="form-control toggle-password" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility(this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Minimal 8 karakter, kombinasi huruf dan angka.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Toggle Status Modal --}}
    <div class="modal fade text-start" id="toggleStatusModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <div class="mb-3">
                        <i class="bi {{ $user->is_active ? 'bi-exclamation-triangle text-danger' : 'bi-check-circle text-success' }} fs-1"></i>
                    </div>
                    <h5 class="mb-2">Konfirmasi</h5>
                    <p class="text-muted mb-4">Anda yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} pengguna <strong>{{ $user->username }}</strong>?</p>
                    <form action="{{ route('users.toggle-active', $user->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">
                            Ya, {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Add User Modal --}}
<div class="modal fade text-start" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control toggle-password" required minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility(this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Minimal 8 karakter, kombinasi huruf dan angka.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="STAFF">STAFF</option>
                            <option value="ADMIN">ADMIN</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyPassword() {
        const passwordText = document.getElementById('newPasswordText').innerText;
        navigator.clipboard.writeText(passwordText).then(() => {
            alert('Password berhasil disalin!');
        });
    }

    function togglePasswordVisibility(button) {
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
