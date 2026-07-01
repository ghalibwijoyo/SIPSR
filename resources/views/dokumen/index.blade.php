@extends ('layouts.app')

@section ('title', 'Daftar Dokumen — ArsiPSR')

@section ('content')
    {{-- Page Header --}}
    <div
        class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2"
    >
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Daftar Dokumen</h1>
            <p class="text-muted mb-0">Kelola semua dokumen arsip yang tersedia</p>
        </div>
        <a
            href="{{ route('dokumen.create') }}"
            class="btn btn-success"
            id="btn-upload-dokumen"
        >
            <i class="bi bi-cloud-arrow-up me-1"></i>Upload Dokumen
        </a>
    </div>

    {{-- Smart Search Bar & Quick Filters --}}
    <div class="card mb-3 border-0 shadow-sm" id="filter-card">
        <div class="card-body">
            <form
                method="GET"
                action="{{ route('dokumen.index') }}"
                class="row g-3"
                id="mainFilterForm"
            >
                <!-- Smart Search Bar -->
                <div class="col-12">
                    <label for="search_input" class="form-label visually-hidden"
                        >Cari Dokumen</label
                    >
                    <div
                        class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border-0 bg-white"
                    >
                        <button
                            type="submit"
                            class="input-group-text bg-white border-0 text-muted px-4 btn btn-link"
                            aria-label="Cari"
                        >
                            <i class="bi bi-search text-dark"></i>
                        </button>
                        <input
                            type="text"
                            id="search_input"
                            name="search"
                            class="form-control form-control-lg border-0 bg-white shadow-none"
                            placeholder="Cari nomor, nama dokumen, kategori, bank, atau uploader..."
                            value="{{ request('search') }}"
                            aria-label="Cari dokumen"
                            aria-describedby="search_hint"
                        />
                        <button
                            type="button"
                            class="btn btn-light border-0 px-4"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#advancedFilter"
                            aria-controls="advancedFilter"
                            aria-label="Buka panel filter lanjutan"
                            title="Filter Lanjutan"
                        >
                            <i class="bi bi-sliders text-sipsr-primary"></i>
                        </button>
                    </div>
                </div>

                <!-- Quick Filter Dropdown & Removable Tags -->
                <div class="col-12 mt-2">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        
                        {{-- Dropdown Quick Filter --}}
                        <div class="dropdown quick-filter-dropdown">
                            @php
                                $activeQuickFilterText = 'Pilih Filter Cepat...';
                                if(request('quick_filter') == 'today') $activeQuickFilterText = 'Hari Ini';
                                elseif(request('quick_filter') == 'my_upload') $activeQuickFilterText = 'Unggahan Saya';
                                elseif(request('quick_filter') == 'pdf') $activeQuickFilterText = 'File PDF';
                                elseif(request('category_id')) {
                                    $cat = $categories->firstWhere('id', request('category_id'));
                                    if($cat) $activeQuickFilterText = $cat->nama;
                                }
                            @endphp
                            
                            <button class="btn btn-sm btn-light rounded-pill px-3 shadow-sm dropdown-toggle border-0" type="button" id="quickFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> {{ $activeQuickFilterText }}
                            </button>
                            <ul class="dropdown-menu shadow border-0 rounded-3 mt-1" aria-labelledby="quickFilterDropdown">
                                <li>
                                    <a class="dropdown-item {{ !request()->has('quick_filter') && !request()->has('category_id') ? 'active bg-light text-dark' : '' }}" href="{{ route('dokumen.index', request()->except(['quick_filter', 'category_id', 'page'])) }}">
                                        Semua Dokumen
                                    </a>
                                </li>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header text-uppercase text-muted" style="font-size: 0.75rem;">Filter Sistem</h6></li>
                                
                                <li>
                                    <a class="dropdown-item {{ request('quick_filter') == 'today' ? 'active bg-sipsr-primary text-white' : '' }}" href="{{ route('dokumen.index', array_merge(request()->except(['page', 'category_id']), ['quick_filter' => 'today'])) }}">
                                        <i class="bi bi-calendar-event me-2"></i> Hari Ini
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request('quick_filter') == 'my_upload' ? 'active bg-sipsr-primary text-white' : '' }}" href="{{ route('dokumen.index', array_merge(request()->except(['page', 'category_id']), ['quick_filter' => 'my_upload'])) }}">
                                        <i class="bi bi-person-fill me-2"></i> Unggahan Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request('quick_filter') == 'pdf' ? 'active bg-sipsr-primary text-white' : '' }}" href="{{ route('dokumen.index', array_merge(request()->except(['page', 'category_id']), ['quick_filter' => 'pdf'])) }}">
                                        <i class="bi bi-file-earmark-pdf me-2"></i> File PDF
                                    </a>
                                </li>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header text-uppercase text-muted" style="font-size: 0.75rem;">Kategori</h6></li>
                                
                                @foreach ($categories as $cat)
                                    <li>
                                        <a class="dropdown-item {{ request('category_id') == $cat->id ? 'active bg-sipsr-primary text-white' : '' }}" href="{{ route('dokumen.index', array_merge(request()->except(['page', 'quick_filter']), ['category_id' => $cat->id])) }}">
                                            {{ $cat->nama }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        {{-- Active Filter Tags --}}
                        <div class="d-flex flex-wrap gap-2 ms-2">
                            @if(request('search'))
                                <span class="badge rounded-pill bg-white text-dark border shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-normal">Pencarian: <strong>{{ request('search') }}</strong></span>
                                    <a href="{{ route('dokumen.index', request()->except(['search', 'page'])) }}" class="text-muted hover-danger text-decoration-none">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('tanggal_dari') || request('tanggal_sampai'))
                                <span class="badge rounded-pill bg-white text-dark border shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-normal">Tanggal: <strong>{{ request('tanggal_dari') }} s/d {{ request('tanggal_sampai') }}</strong></span>
                                    <a href="{{ route('dokumen.index', request()->except(['tanggal_dari', 'tanggal_sampai', 'page'])) }}" class="text-muted hover-danger text-decoration-none">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('uploader_id'))
                                <span class="badge rounded-pill bg-white text-dark border shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-normal">Pengunggah Difilter</span>
                                    <a href="{{ route('dokumen.index', request()->except(['uploader_id', 'page'])) }}" class="text-muted hover-danger text-decoration-none">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('format'))
                                <span class="badge rounded-pill bg-white text-dark border shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-normal">Format: <strong>{{ strtoupper(request('format')) }}</strong></span>
                                    <a href="{{ route('dokumen.index', request()->except(['format', 'page'])) }}" class="text-muted hover-danger text-decoration-none">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('bank_id'))
                                <span class="badge rounded-pill bg-white text-dark border shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-normal">Bank Difilter</span>
                                    <a href="{{ route('dokumen.index', request()->except(['bank_id', 'page'])) }}" class="text-muted hover-danger text-decoration-none">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                </span>
                            @endif
                            
                            @if(!empty(array_filter([request('search'), request('category_id'), request('uploader_id'), request('tanggal_dari'), request('tanggal_sampai'), request('format'), request('quick_filter'), request('bank_id')])))
                                <a href="{{ route('dokumen.index') }}" class="btn btn-sm btn-link text-danger text-decoration-none">Reset Semua</a>
                            @endif
                        </div>
                    </div>
                </div>

    {{-- Offcanvas Advanced Filter Panel --}}
    <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="advancedFilter"
        aria-labelledby="advancedFilterLabel"
    >
        <div class="offcanvas-header bg-light border-bottom">
            <h5 class="offcanvas-title fw-bold" id="advancedFilterLabel">
                <i class="bi bi-sliders me-2 text-sipsr-primary"></i>Filter
                Lanjutan
            </h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Tutup filter lanjutan"
            ></button>
        </div>
        <div class="offcanvas-body">
            <form
                method="GET"
                action="{{ route('dokumen.index') }}"
                id="advancedFilterForm"
            >
                <!-- Preserve search if any -->
                <input
                    type="hidden"
                    name="search"
                    value="{{ request('search') }}"
                />

                <!-- Category -->
                <div class="mb-4">
                    <label
                        for="category_filter"
                        class="form-label small fw-bold"
                        >Kategori Dokumen</label
                    >
                    <select
                        name="category_id"
                        id="category_filter"
                        class="form-select"
                        aria-label="Filter berdasarkan kategori dokumen"
                    >
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($categories as $cat)
                            <option
                                value="{{ $cat->id }}"
                                {{ request('category_id') == $cat->id ? 'selected' : '' }}
                            >
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Bank -->
                <div class="mb-4">
                    <label for="bank_filter" class="form-label small fw-bold"
                        >Bank</label
                    >
                    <select
                        name="bank_id"
                        id="bank_filter"
                        class="form-select"
                        aria-label="Filter berdasarkan bank"
                    >
                        <option value="">-- Semua Bank --</option>
                        @foreach ($banks as $bank)
                            <option
                                value="{{ $bank->id }}"
                                {{ request('bank_id') == $bank->id ? 'selected' : '' }}
                            >
                                {{ $bank->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Uploader -->
                <div class="mb-4">
                    <label
                        for="uploader_filter"
                        class="form-label small fw-bold"
                        >Pengunggah (Uploader)</label
                    >
                    <select
                        name="uploader_id"
                        id="uploader_filter"
                        class="form-select"
                        aria-label="Filter berdasarkan pengunggah dokumen"
                    >
                        <option value="">-- Semua Pengguna --</option>
                        @foreach ($users as $user)
                            <option
                                value="{{ $user->id }}"
                                {{ request('uploader_id') == $user->id ? 'selected' : '' }}
                            >
                                {{ $user->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Format File -->
                <div class="mb-4">
                    <label class="form-label small fw-bold">Format File</label>
                    <div class="form-check mb-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="format[]"
                            value="pdf"
                            id="format_pdf"
                            {{ in_array('pdf', (array)request('format', [])) ? 'checked' : '' }}
                        />
                        <label class="form-check-label" for="format_pdf"
                            >PDF (Bisa di-preview)</label
                        >
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="format[]"
                            value="doc"
                            id="format_doc"
                            {{ in_array('doc', (array)request('format', [])) ? 'checked' : '' }}
                        />
                        <label class="form-check-label" for="format_doc"
                            >DOC / DOCX (Unduh)</label
                        >
                    </div>
                </div>

                <!-- Date Range -->
                <div class="mb-4">
                    <label class="form-label small fw-bold mb-3"
                        >Rentang Waktu</label
                    >
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label
                                for="tanggal_dari"
                                class="form-label small text-muted mb-1"
                                >Dari Tanggal</label
                            >
                            <input
                                type="date"
                                name="tanggal_dari"
                                id="tanggal_dari"
                                class="form-control form-control-sm"
                                value="{{ request('tanggal_dari') }}"
                                aria-label="Filter dokumen dari tanggal"
                            />
                        </div>
                        <div class="col-6">
                            <label
                                for="tanggal_sampai"
                                class="form-label small text-muted mb-1"
                                >Sampai Tanggal</label
                            >
                            <input
                                type="date"
                                name="tanggal_sampai"
                                id="tanggal_sampai"
                                class="form-control form-control-sm"
                                value="{{ request('tanggal_sampai') }}"
                                aria-label="Filter dokumen sampai tanggal"
                            />
                        </div>
                    </div>

                    <small class="text-muted d-block mb-2">Quick preset:</small>
                    <div class="d-flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary py-1 px-2"
                            onclick="
                                setDateRange(0, 0);
                                return false;
                            "
                            aria-label="Filter dokumen hari ini"
                        >
                            Hari Ini
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary py-1 px-2"
                            onclick="
                                setDateRange(7, 0);
                                return false;
                            "
                            aria-label="Filter dokumen 1 minggu terakhir"
                        >
                            1 Minggu
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary py-1 px-2"
                            onclick="
                                setDateRange(30, 0);
                                return false;
                            "
                            aria-label="Filter dokumen 1 bulan terakhir"
                        >
                            1 Bulan
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary py-1 px-2"
                            onclick="
                                setDateRange(365, 0);
                                return false;
                            "
                            aria-label="Filter dokumen 1 tahun terakhir"
                        >
                            1 Tahun
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top bg-light d-flex gap-2">
            <a
                href="{{ route('dokumen.index') }}"
                class="btn btn-light flex-grow-1 border"
            >
                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
            </a>
            <button
                type="submit"
                form="advancedFilterForm"
                class="btn btn-success flex-grow-1"
            >
                <i class="bi bi-check2 me-1"></i>Terapkan
            </button>
        </div>
    </div>

    {{-- Documents Table --}}
    <div class="card border-0 shadow-sm">
        <div
            class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
        >
            <h5 class="card-title mb-0 fw-semibold">
                <i class="bi bi-file-earmark-text me-2 text-sipsr-primary"></i>
                Dokumen
                <span
                    class="badge bg-secondary ms-1"
                    >{{ $documents->total() }}</span
                >
            </h5>

            {{-- Per page selector & Bulk Actions --}}
            <div class="d-flex align-items-center gap-3">
                {{-- Bulk Actions (Hidden by default) --}}
                <div id="bulk-actions" class="d-none align-items-center gap-2">
                    <span class="small text-muted me-2"
                        ><span id="selected-count">0</span> terpilih</span
                    >
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger"
                        onclick="submitBulkDelete()"
                        title="Hapus Terpilih"
                    >
                        <i class="bi bi-trash"></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-success"
                        onclick="submitBulkDownload()"
                        title="Download Terpilih"
                    >
                        <i class="bi bi-download"></i>
                    </button>
                </div>

                {{-- Per page selector --}}
                <div class="d-flex align-items-center gap-2">
                    <label
                        for="per-page"
                        class="form-label mb-0 small text-muted"
                        >Tampilkan:</label
                    >
                    <select
                        class="form-select form-select-sm"
                        id="per-page"
                        style="width: auto"
                        onchange="updatePerPage(this.value)"
                    >
                        @foreach ([50, 100, 250, 500] as $pp)
                            <option
                                value="{{ $pp }}"
                                {{ request('per_page', 50) == $pp ? 'selected' : '' }}
                            >
                                {{ $pp }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body p-0 border-bottom">
            <div class="table-responsive" style="min-height: 400px">
                <table
                    class="table table-hover table-striped align-middle mb-0"
                    id="dokumen-table"
                >
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 40px">
                                <div class="form-check m-0">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="selectAllCheckbox"
                                    />
                                </div>
                            </th>
                            <th class="d-mobile-none" style="width: 50px">
                                No
                            </th>
                            <th class="d-mobile-none">
                                <a
                                    href="{{ route('dokumen.index', array_merge(request()->all(), ['sort' => 'nomor_dokumen', 'dir' => request('sort') == 'nomor_dokumen' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="text-decoration-none text-muted"
                                >
                                    Nomor
                                    @if (request('sort') == 'nomor_dokumen')
                                        <i
                                            class="bi bi-caret-{{ request('dir') == 'asc' ? 'up' : 'down' }}-fill ms-1"
                                        ></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route('dokumen.index', array_merge(request()->all(), ['sort' => 'nama_dokumen', 'dir' => request('sort') == 'nama_dokumen' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="text-decoration-none text-muted"
                                >
                                    Nama Dokumen
                                    @if (request('sort') == 'nama_dokumen')
                                        <i
                                            class="bi bi-caret-{{ request('dir') == 'asc' ? 'up' : 'down' }}-fill ms-1"
                                        ></i>
                                    @endif
                                </a>
                            </th>
                            <th>Nama Bank</th>
                            <th>Kategori</th>
                            <th>
                                <a
                                    href="{{ route('dokumen.index', array_merge(request()->all(), ['sort' => 'tanggal_dokumen', 'dir' => request('sort') == 'tanggal_dokumen' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
                                    class="text-decoration-none text-muted"
                                >
                                    Tanggal
                                    @if (request('sort') == 'tanggal_dokumen')
                                        <i
                                            class="bi bi-caret-{{ request('dir') == 'asc' ? 'up' : 'down' }}-fill ms-1"
                                        ></i>
                                    @endif
                                </a>
                            </th>
                            <th class="d-mobile-none">Uploader</th>
                            <th
                                class="text-center text-nowrap"
                                style="width: 140px"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $i => $doc)
                            <tr>
                                <td class="ps-3">
                                    <div class="form-check m-0">
                                        <input
                                            class="form-check-input row-checkbox"
                                            type="checkbox"
                                            value="{{ $doc->id }}"
                                        />
                                    </div>
                                </td>
                                <td class="text-muted d-mobile-none">
                                    {{ $documents->firstItem() + $i }}
                                </td>
                                <td class="d-mobile-none">
                                    <code
                                        class="text-sipsr-primary fw-semibold"
                                        >{{ $doc->nomor_dokumen }}</code
                                    >
                                </td>
                                <td>
                                    <a
                                        href="{{ route('dokumen.show', $doc) }}"
                                        class="text-dark text-decoration-none fw-medium"
                                    >
                                        {{ Str::limit($doc->nama_dokumen, 45) }}
                                    </a>
                                </td>
                                <td class="small">
                                    {{ $doc->bank->nama ?? '-' }}
                                </td>
                                <td>
                                    <span
                                        class="badge bg-sipsr-primary bg-opacity-10 text-sipsr-light"
                                    >
                                        {{ $doc->category->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $doc->tanggal_dokumen?->format('d/m/Y') }}
                                </td>
                                <td class="small d-mobile-none">
                                    {{ $doc->uploader->nama_lengkap ?? '-' }}
                                </td>
                                <td class="text-center text-nowrap">
                                    <div
                                        class="btn-group btn-group-sm"
                                        role="group"
                                    >
                                        <a
                                            href="{{ route('dokumen.show', $doc) }}"
                                            class="btn btn-outline-secondary"
                                            title="Lihat Detail"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a
                                            href="{{ route('dokumen.download', $doc) }}"
                                            class="btn btn-outline-success"
                                            title="Download"
                                        >
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form
                                            method="POST"
                                            action="{{ route('dokumen.destroy', $doc) }}"
                                            class="d-inline"
                                            onsubmit="
                                                return confirm(
                                                    'Hapus dokumen ini ke Recycle Bin?',
                                                );
                                            "
                                        >
                                            @csrf
                                            @method ('DELETE')
                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm"
                                                title="Hapus"
                                            >
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-4">
                                    <div
                                        class="alert alert-warning text-start mb-0"
                                    >
                                        <h5 class="alert-heading">
                                            <i class="bi bi-search me-2"></i>
                                            Tidak ada hasil pencarian
                                        </h5>
                                        <p>Dokumen dengan filter atau pencarian Anda tidak ditemukan. Coba:</p>
                                        <ul class="mb-0">
                                            <li>
                                                Periksa kembali ejaan kata kunci
                                                pencarian
                                            </li>
                                            <li>
                                                Coba filter kategori atau
                                                rentang waktu yang lebih umum
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('dokumen.index') }}"
                                                    class="alert-link"
                                                    >Reset semua filter</a
                                                >
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer Actions / Pagination --}}
        <div
            class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center align-items-center w-100 position-relative"
            style="min-height: 80px"
        >
            @if ($documents->hasPages())
                {{ $documents->links('vendor.pagination.bootstrap-5') }}
            @endif
        </div>

        <x-scroll-to-top />
    </div>

@endsection

@push ('scripts')
    <script>
        // Bulk Actions Logic with Event Delegation
        const bulkActions = document.getElementById("bulk-actions");
        const selectedCount = document.getElementById("selected-count");

        function updateBulkActions() {
            const checkedCount = document.querySelectorAll(
                ".row-checkbox:checked",
            ).length;
            if (selectedCount) selectedCount.textContent = checkedCount;
            if (bulkActions) {
                if (checkedCount > 0) {
                    bulkActions.classList.remove("d-none");
                    bulkActions.classList.add("d-flex");
                } else {
                    bulkActions.classList.remove("d-flex");
                    bulkActions.classList.add("d-none");
                }
            }
        }

        document.addEventListener("change", function (e) {
            if (e.target.id === "selectAllCheckbox") {
                const checkboxes = document.querySelectorAll(".row-checkbox");
                checkboxes.forEach((cb) => (cb.checked = e.target.checked));
                updateBulkActions();
            } else if (e.target.classList.contains("row-checkbox")) {
                const selectAll = document.getElementById("selectAllCheckbox");
                if (!e.target.checked && selectAll) {
                    selectAll.checked = false;
                }
                const checkboxes = document.querySelectorAll(".row-checkbox");
                const allChecked =
                    document.querySelectorAll(".row-checkbox:checked").length ===
                    checkboxes.length;
                if (allChecked && checkboxes.length > 0 && selectAll) {
                    selectAll.checked = true;
                }
                updateBulkActions();
            }
        });

        function getSelectedIds() {
            return Array.from(document.querySelectorAll(".row-checkbox:checked")).map(
                (cb) => cb.value,
            );
        }

        function submitBulkDelete() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            if (
                confirm(
                    `Apakah Anda yakin ingin menghapus ${ids.length} dokumen yang dipilih ke Recycle Bin?`,
                )
            ) {
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "{{ route("dokumen.bulk-delete") }}";

                const csrfToken = document.createElement("input");
                csrfToken.type = "hidden";
                csrfToken.name = "_token";
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                const methodField = document.createElement("input");
                methodField.type = "hidden";
                methodField.name = "_method";
                methodField.value = "DELETE";
                form.appendChild(methodField);

                ids.forEach((id) => {
                    const input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "ids[]";
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        }

        function submitBulkDownload() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            const form = document.createElement("form");
            form.method = "POST";
            form.action = "{{ route("dokumen.bulk-download") }}";

            const csrfToken = document.createElement("input");
            csrfToken.type = "hidden";
            csrfToken.name = "_token";
            csrfToken.value = "{{ csrf_token() }}";
            form.appendChild(csrfToken);

            ids.forEach((id) => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "ids[]";
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        document.addEventListener("DOMContentLoaded", function () {
            window.updatePerPage = function (val) {
                const url = new URL(window.location.href);
                url.searchParams.set("per_page", val);
                url.searchParams.delete("page");
                window.location.href = url.toString();
            };

            // Real-Time Search with AJAX
            let searchTimeout;
            const searchInput = document.querySelector('input[name="search"]');
            const searchForm = document.getElementById("mainFilterForm");

            if (searchInput && searchForm) {
                searchInput.addEventListener("input", function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const url = new URL(searchForm.action);
                        const formData = new FormData(searchForm);
                        const searchParams = new URLSearchParams();
                        for (const pair of formData) {
                            if (pair[1]) searchParams.append(pair[0], pair[1]);
                        }
                        url.search = searchParams.toString();

                        const tableContainer =
                            document.querySelector(".table-responsive");
                        if (tableContainer) tableContainer.style.opacity = "0.5";

                        fetch(url, {
                            headers: { "X-Requested-With": "XMLHttpRequest" },
                        })
                            .then((response) => response.text())
                            .then((html) => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, "text/html");

                                const newTable = doc.querySelector("#dokumen-table");
                                if (newTable) {
                                    document.querySelector("#dokumen-table").innerHTML =
                                        newTable.innerHTML;
                                }

                                const newPagination = doc.querySelector(".card-footer");
                                const currentPagination =
                                    document.querySelector(".card-footer");
                                if (newPagination && currentPagination) {
                                    currentPagination.innerHTML =
                                        newPagination.innerHTML;
                                }

                                window.history.pushState({}, "", url);
                                if (tableContainer) tableContainer.style.opacity = "1";

                                // Reset checkboxes
                                updateBulkActions();
                                const selectAll =
                                    document.getElementById("selectAllCheckbox");
                                if (selectAll) selectAll.checked = false;
                            })
                            .catch(() => {
                                searchForm.submit(); // Fallback
                            });
                    }, 400); // Delay 400ms is smooth
                });

                // Auto-focus search on initial load
                if (searchInput.value) {
                    searchInput.focus();
                    const length = searchInput.value.length;
                    searchInput.setSelectionRange(length, length);
                }
            }

            // Date Preset Logic
            window.setDateRange = function (daysBack, daysForward) {
                const today = new Date();
                const fromDate = new Date(today);
                fromDate.setDate(fromDate.getDate() - daysBack);

                const toDate = new Date(today);
                toDate.setDate(toDate.getDate() + daysForward);

                document.getElementById("tanggal_dari").value = fromDate
                    .toISOString()
                    .split("T")[0];
                document.getElementById("tanggal_sampai").value = toDate
                    .toISOString()
                    .split("T")[0];
            };

            // Filter form loading state
            const advancedFilterForm = document.querySelector(
                "form[action='{{ route('dokumen.index') }}']",
            );
            if (advancedFilterForm) {
                advancedFilterForm.addEventListener("submit", function () {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2"></span>Sedang memproses...';
                    }
                });
            }

            // Date range validation
            const dateFrom = document.getElementById("tanggal_dari");
            const dateTo = document.getElementById("tanggal_sampai");

            if (dateFrom && dateTo) {
                const validateDateRange = () => {
                    if (
                        dateFrom.value &&
                        dateTo.value &&
                        dateFrom.value > dateTo.value
                    ) {
                        dateTo.setCustomValidity(
                            "Tanggal akhir tidak boleh sebelum tanggal awal",
                        );
                        dateTo.classList.add("is-invalid");
                    } else {
                        dateTo.setCustomValidity("");
                        dateTo.classList.remove("is-invalid");
                    }
                };
                dateFrom.addEventListener("change", validateDateRange);
                dateTo.addEventListener("change", validateDateRange);
            }
        });
    </script>
@endpush
