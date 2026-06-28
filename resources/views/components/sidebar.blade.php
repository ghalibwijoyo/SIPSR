{{-- Sidebar Overlay for Mobile --}}
<div class="sidebar-overlay d-lg-none" id="sidebar-overlay"></div>

{{-- Sidebar --}}
<aside class="sidebar d-flex flex-column" id="sidebar">
    {{-- Sidebar Header --}}
    <div class="sidebar-header px-3 py-3">
        <div class="d-flex align-items-center">
            <div class="sidebar-logo me-2">
                <span class="sidebar-logo-text">SI</span>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0 lh-1">SIPSR</h5>
                <small class="text-muted lh-1" style="font-size: 0.7rem;">PSR Tanaman</small>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav flex-grow-1 overflow-auto px-2 py-3">
        <ul class="nav flex-column gap-1">
            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}"
                   href="{{ url('/dashboard') }}" id="nav-dashboard">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>

            {{-- Dokumen --}}
            <li class="nav-item">
                <a class="nav-link sidebar-link {{ request()->is('dokumen*') ? 'active' : '' }}"
                   href="{{ route('dokumen.index') }}" id="nav-dokumen">
                    <i class="bi bi-file-earmark-text me-2"></i>Dokumen
                </a>
            </li>

            {{-- Laporan --}}
            <li class="nav-item">
                <a class="nav-link sidebar-link {{ request()->is('laporan*') ? 'active' : '' }}"
                   href="{{ route('laporan.index') }}" id="nav-laporan">
                    <i class="bi bi-bar-chart-line me-2"></i>Laporan
                </a>
            </li>

            {{-- Aktivitas --}}
            <li class="nav-item">
                <a class="nav-link sidebar-link {{ request()->is('aktivitas*') ? 'active' : '' }}"
                   href="{{ route('aktivitas.index') }}" id="nav-aktivitas">
                    <i class="bi bi-clock-history me-2"></i>Aktivitas
                </a>
            </li>

            {{-- Recycle Bin --}}
            <li class="nav-item">
                <a class="nav-link sidebar-link {{ request()->is('recycle-bin*') ? 'active' : '' }}"
                   href="{{ route('recycle-bin.index') }}" id="nav-recycle-bin">
                    <i class="bi bi-trash3 me-2"></i>Recycle Bin
                </a>
            </li>

            {{-- Admin Menu --}}
            @if(auth()->user()->role === 'ADMIN')
            <li class="nav-item mt-3">
                <small class="sidebar-label text-uppercase px-3">Admin</small>
            </li>
            <li class="nav-item">
                <a class="nav-link sidebar-link {{ request()->is('admin/users*') ? 'active' : '' }}"
                   href="{{ route('users.index') }}" id="nav-users">
                    <i class="bi bi-people me-2"></i>Manajemen User
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link sidebar-link {{ request()->is('admin/categories*') ? 'active' : '' }}"
                   href="{{ route('categories.index') }}" id="nav-kategori">
                    <i class="bi bi-tags me-2"></i>Kategori
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link sidebar-link {{ request()->is('admin/banks*') ? 'active' : '' }}"
                   href="{{ route('banks.index') }}" id="nav-bank">
                    <i class="bi bi-bank me-2"></i>Bank
                </a>
            </li>
            @endif

            {{-- Divider --}}
            <li class="nav-item mt-3">
                <small class="sidebar-label text-uppercase px-3">Akun</small>
            </li>
            <li class="nav-item">
                <a class="nav-link sidebar-link {{ request()->is('profil*') ? 'active' : '' }}"
                   href="{{ route('profil.show') }}" id="nav-profil">
                    <i class="bi bi-person-circle me-2"></i>Profil
                </a>
            </li>
        </ul>
    </nav>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer px-3 py-3">
        <div class="d-flex align-items-center">
            <div class="sidebar-avatar me-2">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <p class="text-dark fw-semibold mb-0 small text-truncate">{{ auth()->user()->nama_lengkap }}</p>
                <small class="text-muted">{{ auth()->user()->role }}</small>
            </div>
        </div>
    </div>
</aside>
