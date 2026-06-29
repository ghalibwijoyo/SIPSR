@extends ('layouts.app')

@section ('title', 'Laporan Dokumen — ArsiPSR')

@section ('content')
    <div
        class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2"
    >
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Laporan Dokumen</h1>
            <p class="text-muted mb-0">Filter dan export laporan arsip dokumen</p>
        </div>
    </div>

    {{-- Smart Search Bar & Quick Filters --}}
    <div class="card mb-3 border-0 shadow-sm" id="filter-card">
        <div class="card-body">
            <form
                method="GET"
                action="{{ route('laporan.index') }}"
                class="row g-3"
                id="mainFilterForm"
            >
                {{-- Smart Search Bar --}}
                <div class="col-12">
                    <label
                        for="laporan_search_input"
                        class="form-label visually-hidden"
                        >Cari Dokumen</label
                    >
                    <div
                        class="input-group input-group-lg shadow-sm rounded overflow-hidden border"
                    >
                        <button
                            type="button"
                            class="btn btn-light border-0 px-4"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#laporanAdvancedFilter"
                            aria-controls="laporanAdvancedFilter"
                            aria-label="Buka panel filter lanjutan"
                            title="Filter Lanjutan"
                        >
                            <i class="bi bi-sliders text-sipsr-primary"></i>
                        </button>
                        <input
                            type="text"
                            id="laporan_search_input"
                            name="search"
                            class="form-control form-control-lg border-0 bg-white shadow-none"
                            placeholder="Cari nomor, nama dokumen, kategori, bank, atau uploader..."
                            value="{{ request('search') }}"
                            aria-label="Cari dokumen untuk laporan"
                        />
                        <button
                            type="submit"
                            class="input-group-text bg-white border-0 text-muted px-4 btn btn-link"
                            aria-label="Cari"
                        >
                            <i class="bi bi-search text-dark"></i>
                        </button>
                    </div>
                </div>

                {{-- Quick Filter Badges --}}
                <div class="col-12 mt-2">
                    <div class="d-flex flex-wrap gap-2">
                        <a
                            href="{{ route('laporan.index') }}"
                            class="btn btn-sm rounded-pill px-3 shadow-sm {{ !request()->has('search') && !request()->has('category_id') && !request()->has('bank_id') && !request()->has('uploader_id') && !request()->has('tanggal_dari') && !request()->has('quick_filter') ? 'btn-success' : 'btn-light text-muted border-0' }}"
                            aria-label="Lihat semua dokumen"
                        >
                            Semua Dokumen
                        </a>

                        {{-- System Quick Filters --}}
                        <a
                            href="{{ route('laporan.index', array_merge(request()->except(['page']), ['quick_filter' => 'today'])) }}"
                            class="btn btn-sm rounded-pill px-3 shadow-sm {{ request('quick_filter') == 'today' ? 'btn-success' : 'btn-light text-muted border-0' }}"
                        >
                            <i class="bi bi-calendar-event me-1"></i> Hari Ini
                        </a>

                        <a
                            href="{{ route('laporan.index', array_merge(request()->except(['page']), ['quick_filter' => 'my_upload'])) }}"
                            class="btn btn-sm rounded-pill px-3 shadow-sm {{ request('quick_filter') == 'my_upload' ? 'btn-success' : 'btn-light text-muted border-0' }}"
                        >
                            <i class="bi bi-person-fill me-1"></i> Unggahan Saya
                        </a>

                        <a
                            href="{{ route('laporan.index', array_merge(request()->except(['page']), ['quick_filter' => 'pdf'])) }}"
                            class="btn btn-sm rounded-pill px-3 shadow-sm {{ request('quick_filter') == 'pdf' ? 'btn-success' : 'btn-light text-muted border-0' }}"
                        >
                            <i class="bi bi-file-earmark-pdf me-1"></i> File PDF
                        </a>

                        {{-- Separator --}}
                        <div
                            class="vr mx-1 d-none d-md-block"
                            style="opacity: 0.1"
                        ></div>

                        {{-- Category Quick Filters --}}
                        @foreach ($categories as $cat)
                            <a
                                href="{{ route('laporan.index', array_merge(request()->except(['page']), ['category_id' => $cat->id])) }}"
                                class="btn btn-sm rounded-pill px-3 shadow-sm {{ request('category_id') == $cat->id ? 'btn-success' : 'btn-light text-muted border-0' }}"
                            >
                                {{ $cat->nama }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Filter Status & Result Counter --}}
    @php
    $activeFilters = array_filter([
        request('search'),
        request('category_id'),
        request('bank_id'),
        request('uploader_id'),
        request('tanggal_dari'),
        request('tanggal_sampai'),
    ]);
@endphp

    @if (!empty($activeFilters))
        <div
            class="alert alert-secondary py-2 px-3 mb-3 d-flex justify-content-between align-items-center shadow-sm border-0"
        >
            <span>
                <i class="bi bi-funnel-fill text-sipsr-primary me-2"></i>
                <strong>{{ count($activeFilters) }} filter aktif</strong>
                diterapkan.
            </span>
            <a
                href="{{ route('laporan.index') }}"
                class="btn btn-sm btn-danger rounded-pill px-3"
            >
                <i class="bi bi-x-circle me-1"></i>Clear All
            </a>
        </div>
    @endif

    {{-- Offcanvas Advanced Filter Panel --}}
    <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="laporanAdvancedFilter"
        aria-labelledby="laporanAdvancedFilterLabel"
    >
        <div class="offcanvas-header bg-light border-bottom">
            <h5 class="offcanvas-title fw-bold" id="laporanAdvancedFilterLabel">
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
                action="{{ route('laporan.index') }}"
                id="laporanAdvancedFilterForm"
            >
                {{-- Preserve search if any --}}
                <input
                    type="hidden"
                    name="search"
                    value="{{ request('search') }}"
                />

                {{-- Category --}}
                <div class="mb-4">
                    <label
                        for="laporan_category_filter"
                        class="form-label small fw-bold"
                        >Kategori Dokumen</label
                    >
                    <select
                        name="category_id"
                        id="laporan_category_filter"
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

                {{-- Bank --}}
                <div class="mb-4">
                    <label
                        for="laporan_bank_filter"
                        class="form-label small fw-bold"
                        >Bank</label
                    >
                    <select
                        name="bank_id"
                        id="laporan_bank_filter"
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

                {{-- Uploader --}}
                <div class="mb-4">
                    <label
                        for="laporan_uploader_filter"
                        class="form-label small fw-bold"
                        >Pengunggah (Uploader)</label
                    >
                    <select
                        name="uploader_id"
                        id="laporan_uploader_filter"
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

                {{-- Date Range --}}
                <div class="mb-4">
                    <label class="form-label small fw-bold mb-3"
                        >Rentang Waktu</label
                    >
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label
                                for="laporan_tanggal_dari"
                                class="form-label small text-muted mb-1"
                                >Dari Tanggal</label
                            >
                            <input
                                type="date"
                                name="tanggal_dari"
                                id="laporan_tanggal_dari"
                                class="form-control form-control-sm"
                                value="{{ request('tanggal_dari') }}"
                                aria-label="Filter dokumen dari tanggal"
                            />
                        </div>
                        <div class="col-6">
                            <label
                                for="laporan_tanggal_sampai"
                                class="form-label small text-muted mb-1"
                                >Sampai Tanggal</label
                            >
                            <input
                                type="date"
                                name="tanggal_sampai"
                                id="laporan_tanggal_sampai"
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
                                setLaporanDateRange(0, 0);
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
                                setLaporanDateRange(7, 0);
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
                                setLaporanDateRange(30, 0);
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
                                setLaporanDateRange(365, 0);
                                return false;
                            "
                            aria-label="Filter dokumen 1 tahun terakhir"
                        >
                            1 Tahun
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary py-1 px-2"
                            onclick="
                                setLaporanDateRange(1825, 0);
                                return false;
                            "
                            aria-label="Filter dokumen 5 tahun terakhir"
                        >
                            5 Tahun
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top bg-light d-flex gap-2">
            <a
                href="{{ route('laporan.index') }}"
                class="btn btn-light flex-grow-1 border"
            >
                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
            </a>
            <button
                type="submit"
                form="laporanAdvancedFilterForm"
                class="btn btn-success flex-grow-1"
            >
                <i class="bi bi-check2 me-1"></i>Terapkan
            </button>
        </div>
    </div>

    {{-- Data Preview --}}
    <div class="card border-0 shadow-sm">
        <div
            class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2"
        >
            <h5 class="card-title mb-0 fw-semibold">
                <i class="bi bi-table me-2 text-sipsr-primary"></i>
                Pratinjau Data
                <span
                    class="badge bg-secondary ms-1"
                    >{{ $totalDokumen }}</span
                >
            </h5>

            @if ($totalDokumen > 0)
                <div class="d-flex align-items-center gap-3 flex-wrap">
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

                    <div class="d-flex gap-2">
                        <form
                            action="{{ route('laporan.export.excel') }}"
                            method="GET"
                            class="d-inline no-global-loading"
                        >
                            <input
                                type="hidden"
                                name="search"
                                value="{{ request('search') }}"
                            />
                            <input
                                type="hidden"
                                name="category_id"
                                value="{{ request('category_id') }}"
                            />
                            <input
                                type="hidden"
                                name="bank_id"
                                value="{{ request('bank_id') }}"
                            />
                            <input
                                type="hidden"
                                name="uploader_id"
                                value="{{ request('uploader_id') }}"
                            />
                            <input
                                type="hidden"
                                name="tanggal_dari"
                                value="{{ request('tanggal_dari') }}"
                            />
                            <input
                                type="hidden"
                                name="tanggal_sampai"
                                value="{{ request('tanggal_sampai') }}"
                            />
                            <input
                                type="hidden"
                                name="sort"
                                value="{{ request('sort') }}"
                            />
                            <input
                                type="hidden"
                                name="dir"
                                value="{{ request('dir') }}"
                            />
                            <button
                                type="submit"
                                class="btn btn-sm btn-success"
                            >
                                <i class="bi bi-file-earmark-excel me-1"></i>
                                Export Excel
                            </button>
                        </form>

                        <form
                            action="{{ route('laporan.export.pdf') }}"
                            method="GET"
                            class="d-inline no-global-loading"
                        >
                            <input
                                type="hidden"
                                name="search"
                                value="{{ request('search') }}"
                            />
                            <input
                                type="hidden"
                                name="category_id"
                                value="{{ request('category_id') }}"
                            />
                            <input
                                type="hidden"
                                name="bank_id"
                                value="{{ request('bank_id') }}"
                            />
                            <input
                                type="hidden"
                                name="uploader_id"
                                value="{{ request('uploader_id') }}"
                            />
                            <input
                                type="hidden"
                                name="tanggal_dari"
                                value="{{ request('tanggal_dari') }}"
                            />
                            <input
                                type="hidden"
                                name="tanggal_sampai"
                                value="{{ request('tanggal_sampai') }}"
                            />
                            <input
                                type="hidden"
                                name="sort"
                                value="{{ request('sort') }}"
                            />
                            <input
                                type="hidden"
                                name="dir"
                                value="{{ request('dir') }}"
                            />
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                Download PDF
                            </button>
                        </form>

                        <form
                            action="{{ route('laporan.print.pdf') }}"
                            method="GET"
                            class="d-inline"
                            target="_blank"
                        >
                            <input
                                type="hidden"
                                name="search"
                                value="{{ request('search') }}"
                            />
                            <input
                                type="hidden"
                                name="category_id"
                                value="{{ request('category_id') }}"
                            />
                            <input
                                type="hidden"
                                name="bank_id"
                                value="{{ request('bank_id') }}"
                            />
                            <input
                                type="hidden"
                                name="uploader_id"
                                value="{{ request('uploader_id') }}"
                            />
                            <input
                                type="hidden"
                                name="tanggal_dari"
                                value="{{ request('tanggal_dari') }}"
                            />
                            <input
                                type="hidden"
                                name="tanggal_sampai"
                                value="{{ request('tanggal_sampai') }}"
                            />
                            <input
                                type="hidden"
                                name="sort"
                                value="{{ request('sort') }}"
                            />
                            <input
                                type="hidden"
                                name="dir"
                                value="{{ request('dir') }}"
                            />
                            <button
                                type="submit"
                                class="btn btn-sm btn-primary"
                            >
                                <i class="bi bi-printer me-1"></i> Cetak PDF
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="card-body p-0">
            @if ($totalDokumen > 0)
                <div class="table-responsive" style="min-height: 400px">
                    <table
                        class="table table-hover table-striped align-middle mb-0"
                        id="laporan-table"
                    >
                        <thead class="table-light">
                            <tr>
                                <th
                                    class="d-mobile-none ps-3"
                                    style="width: 50px"
                                >
                                    No
                                </th>
                                <th class="d-mobile-none">
                                    <a
                                        href="{{ route('laporan.index', array_merge(request()->all(), ['sort' => 'nomor_dokumen', 'dir' => request('sort') == 'nomor_dokumen' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
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
                                        href="{{ route('laporan.index', array_merge(request()->all(), ['sort' => 'nama_dokumen', 'dir' => request('sort') == 'nama_dokumen' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
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
                                        href="{{ route('laporan.index', array_merge(request()->all(), ['sort' => 'tanggal_dokumen', 'dir' => request('sort') == 'tanggal_dokumen' && request('dir') == 'asc' ? 'desc' : 'asc'])) }}"
                                        class="text-decoration-none text-muted"
                                    >
                                        Tanggal
                                        @if (request('sort') == 'tanggal_dokumen' || !request()->has('sort'))
                                            <i
                                                class="bi bi-caret-{{ request('dir') == 'asc' ? 'up' : 'down' }}-fill ms-1"
                                            ></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="d-mobile-none pe-3">Uploader</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dokumenPreview as $i => $doc)
                                <tr>
                                    <td class="text-muted d-mobile-none ps-3">
                                        {{ $dokumenPreview->firstItem() + $i }}
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
                                            target="_blank"
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
                                    <td class="small d-mobile-none pe-3">
                                        {{ $doc->uploader->nama_lengkap ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer Actions / Pagination --}}
                <div
                    class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center align-items-center w-100 position-relative"
                    style="min-height: 80px"
                >
                    @if ($dokumenPreview->hasPages())
                        {{ $dokumenPreview->appends(request()->all())->links('vendor.pagination.bootstrap-5') }}
                    @endif
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i
                        class="bi bi-search fs-1 text-secondary d-block mb-3"
                    ></i>
                    <h6 class="fw-semibold">Tidak Ada Data</h6>
                    <p class="small mb-0">Tidak ada dokumen yang ditemukan untuk filter yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push ('scripts')
    <script>
        window.updatePerPage = function (val) {
            const url = new URL(window.location.href);
            url.searchParams.set("per_page", val);
            url.searchParams.delete("page");
            window.location.href = url.toString();
        };

        function setLaporanDateRange(daysBack, daysForward) {
            const today = new Date();
            const fromDate = new Date(today);
            fromDate.setDate(today.getDate() - daysBack);

            const toDate = new Date(today);
            toDate.setDate(today.getDate() + daysForward);

            document.getElementById("laporan_tanggal_dari").value = fromDate
                .toISOString()
                .split("T")[0];
            document.getElementById("laporan_tanggal_sampai").value = toDate
                .toISOString()
                .split("T")[0];
        }

        document.addEventListener("DOMContentLoaded", function () {
            let searchTimeout;
            const searchInput = document.getElementById("laporan_search_input");
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
                            document.querySelector(".card-body.p-0");
                        if (tableContainer)
                            tableContainer.style.opacity = "0.5";

                        fetch(url, {
                            headers: { "X-Requested-With": "XMLHttpRequest" },
                        })
                            .then((response) => response.text())
                            .then((html) => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(
                                    html,
                                    "text/html",
                                );

                                const newTable =
                                    doc.querySelector(".card-body.p-0");
                                if (newTable && tableContainer) {
                                    tableContainer.innerHTML =
                                        newTable.innerHTML;
                                }

                                window.history.pushState({}, "", url);
                                if (tableContainer)
                                    tableContainer.style.opacity = "1";
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
        });
    </script>
@endpush
