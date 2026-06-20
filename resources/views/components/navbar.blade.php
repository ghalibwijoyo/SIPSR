{{-- Top Navbar --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-3 py-2" id="top-navbar">
    <div class="d-flex align-items-center w-100">
        {{-- Mobile Sidebar Toggle --}}
        <button class="btn btn-link text-dark d-lg-none me-2 p-0" type="button" id="sidebar-toggler" title="Toggle sidebar">
            <i class="bi bi-list fs-4"></i>
        </button>

        {{-- Brand (mobile) --}}
        <span class="navbar-brand fw-bold text-sipsr-primary d-lg-none mb-0">SIPSR</span>

        {{-- Spacer --}}
        <div class="flex-grow-1"></div>

        {{-- Right Side --}}
        <div class="d-flex align-items-center gap-2">
            {{-- Role Badge --}}
            <span class="badge rounded-pill {{ auth()->user()->role === 'ADMIN' ? 'bg-sipsr-primary' : 'bg-secondary' }} px-3 py-2">
                <i class="bi bi-shield-fill me-1"></i>{{ auth()->user()->role }}
            </span>

            {{-- User Name --}}
            <span class="fw-semibold text-dark d-none d-sm-inline">{{ auth()->user()->nama_lengkap }}</span>

            {{-- Logout Button --}}
            <form method="POST" action="{{ route('logout') }}" class="d-inline" id="logout-form">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm" id="btn-logout" title="Keluar">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    <span class="d-none d-sm-inline">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</nav>
