@extends ('layouts.app')

@section ('title', 'Recycle Bin')

@section ('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Recycle Bin</h1>
    </div>

    <!-- Warning untuk dokumen akan auto-prune -->
    @if ($documents->where('deleted_at', '<', \Carbon\Carbon::now()->subDays(20))->count() > 0)
        <div
            class="alert alert-danger shadow-sm border-0 d-flex align-items-center"
        >
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <strong>Perhatian!</strong>
                Terdapat {{ $documents->where('deleted_at', '<', \Carbon\Carbon::now()->subDays(20))->count() }} dokumen
                yang akan dihapus secara permanen secara otomatis dalam waktu
                kurang dari 10 hari.
            </div>
        </div>
    @endif

    {{-- Smart Search Bar & Quick Filters --}}
    <div class="card mb-3 border-0 shadow-sm" id="filter-card">
        <div class="card-body">
            <form
                method="GET"
                action="{{ route('recycle-bin.index') }}"
                class="row g-3"
                id="mainFilterForm"
            >
                <!-- Smart Search Bar -->
                <div class="col-12">
                    <label for="search_input" class="form-label visually-hidden"
                        >Cari Dokumen Terhapus</label
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
                            placeholder="Cari nomor atau nama dokumen..."
                            value="{{ request('search') }}"
                            aria-label="Cari dokumen di Recycle Bin"
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
                        
                        {{-- Active Filter Tags --}}
                        <div class="d-flex flex-wrap gap-2 ms-2">
                            @if(request('search'))
                                <span class="badge rounded-pill bg-white text-dark border shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-normal">Pencarian: <strong>{{ request('search') }}</strong></span>
                                    <a href="{{ route('recycle-bin.index', request()->except(['search', 'page'])) }}" class="text-muted hover-danger text-decoration-none">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('tanggal_dari') || request('tanggal_sampai'))
                                <span class="badge rounded-pill bg-white text-dark border shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-normal">Tanggal: <strong>{{ request('tanggal_dari') }} s/d {{ request('tanggal_sampai') }}</strong></span>
                                    <a href="{{ route('recycle-bin.index', request()->except(['tanggal_dari', 'tanggal_sampai', 'page'])) }}" class="text-muted hover-danger text-decoration-none">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request('deleted_by'))
                                <span class="badge rounded-pill bg-white text-dark border shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-normal">Dihapus Oleh Difilter</span>
                                    <a href="{{ route('recycle-bin.index', request()->except(['deleted_by', 'page'])) }}" class="text-muted hover-danger text-decoration-none">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                </span>
                            @endif
                            
                            @if(request('milik_saya'))
                                <span class="badge rounded-pill bg-white text-dark border shadow-sm px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-normal"><i class="bi bi-person-fill text-sipsr-primary me-1"></i>Hanya Dihapus Oleh Saya</span>
                                    <a href="{{ route('recycle-bin.index', request()->except(['milik_saya', 'page'])) }}" class="text-muted hover-danger text-decoration-none">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                </span>
                            @endif
                            
                            @if(!empty(array_filter([request('search'), request('category_id'), request('deleted_by'), request('tanggal_dari'), request('tanggal_sampai'), request('milik_saya'), request('trash_age')])))
                                <a href="{{ route('recycle-bin.index') }}" class="btn btn-sm btn-link text-danger text-decoration-none">Reset Semua</a>
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
                action="{{ route('recycle-bin.index') }}"
                id="advancedFilterForm"
            >
                <!-- Preserve search if any -->
                <input
                    type="hidden"
                    name="search"
                    value="{{ request('search') }}"
                />

                <!-- Data Milik Saya -->
                <div class="mb-4">
                    <div class="form-check form-switch form-check-inline">
                        <input class="form-check-input" type="checkbox" role="switch" id="filter_milik_saya_rec" name="milik_saya" value="1" {{ request('milik_saya') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold small text-dark" for="filter_milik_saya_rec">
                            Hanya Tampilkan File Dihapus Oleh Saya
                        </label>
                    </div>
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label
                        for="filter-category"
                        class="form-label small fw-bold"
                        >Kategori</label
                    >
                    <select
                        class="form-select"
                        id="filter-category"
                        name="category_id"
                    >
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}
                            >
                                {{ $category->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Trash Age -->
                <div class="mb-4">
                    <label
                        for="filter-trash-age"
                        class="form-label small fw-bold"
                        >Usia Sampah</label
                    >
                    <select
                        class="form-select"
                        id="filter-trash-age"
                        name="trash_age"
                    >
                        <option value="">-- Semua Waktu --</option>
                        <option
                            value="new"
                            {{ request('trash_age') == 'new' ? 'selected' : '' }}
                        >
                            < 7 Hari
                        </option>
                        <option
                            value="medium"
                            {{ request('trash_age') == 'medium' ? 'selected' : '' }}
                        >
                            7 - 20 Hari
                        </option>
                        <option
                            value="old"
                            {{ request('trash_age') == 'old' ? 'selected' : '' }}
                        >
                            > 20 Hari (Kritis)
                        </option>
                    </select>
                </div>

                <!-- Deleted By -->
                <div class="mb-4">
                    <label
                        for="filter-deleted-by"
                        class="form-label small fw-bold"
                        >Dihapus Oleh</label
                    >
                    <select
                        class="form-select"
                        id="filter-deleted-by"
                        name="deleted_by"
                    >
                        <option value="">-- Semua User --</option>
                        @foreach ($users as $user)
                            <option
                                value="{{ $user->id }}"
                                {{ request('deleted_by') == $user->id ? 'selected' : '' }}
                            >
                                {{ $user->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div class="mb-4">
                    <label class="form-label small fw-bold mb-3"
                        >Tanggal Dihapus</label
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
                        >
                            1 Bulan
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top bg-light d-flex gap-2">
            <a
                href="{{ route('recycle-bin.index') }}"
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

    <div class="card shadow-sm border-0">
        <div
            class="card-header bg-white py-3 d-flex justify-content-between align-items-center"
        >
            <h5 class="card-title mb-0 fw-semibold">
                <i class="bi bi-trash3 me-2 text-sipsr-primary"></i>
                Dokumen Terhapus
                <span
                    class="badge bg-secondary ms-1"
                    >{{ $documents->total() }}</span
                >
            </h5>

            <div class="d-flex align-items-center gap-3">
                {{-- Bulk Actions (Hidden by default) --}}
                <div id="bulk-actions" class="d-none align-items-center gap-2">
                    <span class="small text-muted me-2"
                        ><span id="selected-count">0</span> terpilih</span
                    >
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-success"
                        onclick="submitBulkRestore()"
                        title="Restore Terpilih"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                    @if (auth()->user()->role === 'ADMIN')
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger"
                            onclick="submitBulkDelete()"
                            title="Hapus Permanen Terpilih"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
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

                @if (auth()->user()->role === 'ADMIN' && $documents->count() > 0)
                    <button
                        type="button"
                        class="btn btn-sm btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#emptyModal"
                    >
                        <i class="bi bi-trash3 me-1"></i> Kosongkan Recycle Bin
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body border-bottom">
            <div class="table-responsive">
                <table
                    class="table table-hover table-striped align-middle mb-0"
                    id="recycle-bin-table"
                >
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="selectAllCheckbox"
                                />
                            </th>
                            <th style="width: 50px">No</th>
                            <th>Nama Dokumen</th>
                            <th>Kategori</th>
                            <th>Dihapus Oleh</th>
                            <th>Tanggal Dihapus</th>
                            <th class="text-end text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $i => $doc)
                            <tr>
                                <td>
                                    <input
                                        class="form-check-input row-checkbox"
                                        type="checkbox"
                                        value="{{ $doc->id }}"
                                    />
                                </td>
                                <td class="text-muted">
                                    {{ $documents->firstItem() + $i }}
                                </td>
                                <td>
                                    <div class="fw-bold">
                                        {{ $doc->nama_dokumen }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $doc->nomor_dokumen }}
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-secondary"
                                        >{{ $doc->category->nama ?? '-' }}</span
                                    >
                                </td>
                                <td>
                                    {{ $doc->deletedBy->nama_lengkap ?? 'Sistem' }}
                                </td>
                                <td>
                                    {{ $doc->deleted_at->format('d M Y H:i') }}
                                </td>
                                <td class="text-end text-nowrap">
                                    <form
                                        action="{{ route('recycle-bin.restore', $doc->id) }}"
                                        method="POST"
                                        class="d-inline"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-success"
                                            title="Restore"
                                        >
                                            <i
                                                class="bi bi-arrow-counterclockwise"
                                            ></i>
                                            Restore
                                        </button>
                                    </form>

                                    @if (auth()->user()->role === 'ADMIN')
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger ms-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $doc->id }}"
                                            title="Hapus Permanen"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <!-- Delete Modal -->
                                        <div
                                            class="modal fade"
                                            id="deleteModal{{ $doc->id }}"
                                            tabindex="-1"
                                            aria-labelledby="deleteModalLabel{{ $doc->id }}"
                                            aria-hidden="true"
                                        >
                                            <div class="modal-dialog">
                                                <div
                                                    class="modal-content text-start"
                                                >
                                                    <div
                                                        class="modal-header border-0 pb-0"
                                                    >
                                                        <h5
                                                            class="modal-title"
                                                            id="deleteModalLabel{{ $doc->id }}"
                                                        >
                                                            Hapus Permanen
                                                            Dokumen
                                                        </h5>
                                                        <button
                                                            type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close"
                                                        ></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Apakah Anda yakin ingin
                                                        menghapus dokumen
                                                        <strong
                                                            >{{ $doc->nama_dokumen }}</strong
                                                        >
                                                        secara permanen?
                                                        <div
                                                            class="alert alert-danger mt-3 mb-0"
                                                        >
                                                            <i
                                                                class="bi bi-exclamation-triangle-fill me-2"
                                                            ></i>
                                                            File fisik akan
                                                            dihapus dan tidak
                                                            dapat dipulihkan.
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="modal-footer border-0 pt-0"
                                                    >
                                                        <button
                                                            type="button"
                                                            class="btn btn-light"
                                                            data-bs-dismiss="modal"
                                                        >
                                                            Batal
                                                        </button>
                                                        <form
                                                            action="{{ route('recycle-bin.destroy', $doc->id) }}"
                                                            method="POST"
                                                        >
                                                            @csrf
                                                            @method ('DELETE')
                                                            <button
                                                                type="submit"
                                                                class="btn btn-danger"
                                                            >
                                                                Hapus Permanen
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-state">
                                <td colspan="7" class="p-4">
                                    <div
                                        class="alert alert-warning text-start mb-0"
                                    >
                                        <h5 class="alert-heading">
                                            <i class="bi bi-search me-2"></i>
                                            Tidak ada hasil
                                        </h5>
                                        <p>Recycle Bin kosong atau dokumen dengan filter Anda tidak ditemukan.</p>
                                        <ul class="mb-0">
                                            <li>
                                                Coba periksa kata kunci
                                                pencarian
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('recycle-bin.index') }}"
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

    <!-- Empty Modal -->
    @if (auth()->user()->role === 'ADMIN')
        <div
            class="modal fade"
            id="emptyModal"
            tabindex="-1"
            aria-labelledby="emptyModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title" id="emptyModalLabel">
                            Kosongkan Recycle Bin
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin mengosongkan Recycle Bin?
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Semua file fisik dan data dokumen akan dihapus
                            secara permanen dan tidak dapat dipulihkan.
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>
                        <form
                            action="{{ route('recycle-bin.empty') }}"
                            method="POST"
                        >
                            @csrf
                            @method ('DELETE')
                            <button type="submit" class="btn btn-danger">
                                Kosongkan Semua
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push ('scripts')
    <script>
        // Bulk Actions Logic
        const selectAllCheckbox = document.getElementById("selectAllCheckbox");
        const rowCheckboxes = document.querySelectorAll(".row-checkbox");
        const bulkActions = document.getElementById("bulk-actions");
        const selectedCount = document.getElementById("selected-count");

        function updateBulkActions() {
            const checkedCount = document.querySelectorAll(
                ".row-checkbox:checked",
            ).length;
            selectedCount.textContent = checkedCount;
            if (checkedCount > 0) {
                bulkActions.classList.remove("d-none");
                bulkActions.classList.add("d-flex");
            } else {
                bulkActions.classList.remove("d-flex");
                bulkActions.classList.add("d-none");
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener("change", function () {
                rowCheckboxes.forEach((cb) => (cb.checked = this.checked));
                updateBulkActions();
            });
        }

        rowCheckboxes.forEach((cb) => {
            cb.addEventListener("change", function () {
                if (!this.checked && selectAllCheckbox.checked) {
                    selectAllCheckbox.checked = false;
                }
                const allChecked =
                    document.querySelectorAll(".row-checkbox:checked").length ===
                    rowCheckboxes.length;
                if (allChecked && rowCheckboxes.length > 0) {
                    selectAllCheckbox.checked = true;
                }
                updateBulkActions();
            });
        });

        function getSelectedIds() {
            return Array.from(document.querySelectorAll(".row-checkbox:checked")).map(
                (cb) => cb.value,
            );
        }

        function submitBulkRestore() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            if (
                confirm(
                    `Apakah Anda yakin ingin memulihkan ${ids.length} dokumen yang dipilih?`,
                )
            ) {
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "{{ route("recycle-bin.bulk-restore") }}";

                const csrfToken = document.createElement("input");
                csrfToken.type = "hidden";
                csrfToken.name = "_token";
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                ids.forEach((id) => {
                    const input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "document_ids[]";
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        }

        function submitBulkDelete() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            if (
                confirm(
                    `Peringatan!\n\nApakah Anda yakin ingin MENGHAPUS SECARA PERMANEN ${ids.length} dokumen yang dipilih?\nFile fisik juga akan dihapus dan tidak dapat dipulihkan!`,
                )
            ) {
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "{{ route("recycle-bin.bulk-delete") }}";

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
                    input.name = "document_ids[]";
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        }

        function setDateRange(daysBack, daysForward) {
            const today = new Date();
            const fromDate = new Date(today);
            fromDate.setDate(today.getDate() - daysBack);

            const toDate = new Date(today);
            toDate.setDate(today.getDate() + daysForward);

            document.getElementById("tanggal_dari").value = fromDate
                .toISOString()
                .split("T")[0];
            document.getElementById("tanggal_sampai").value = toDate
                .toISOString()
                .split("T")[0];
        }

        function updatePerPage(val) {
            const url = new URL(window.location.href);
            url.searchParams.set("per_page", val);
            url.searchParams.delete("page");
            window.location.href = url.toString();
        }

        document.addEventListener("DOMContentLoaded", function () {
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

                                const newTable =
                                    doc.querySelector("#recycle-bin-table");
                                if (newTable) {
                                    document.querySelector(
                                        "#recycle-bin-table",
                                    ).innerHTML = newTable.innerHTML;
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

            // Filter form loading state
            const filterForm = document.querySelector(
                "form[action='{{ route('recycle-bin.index') }}']",
            );
            if (filterForm) {
                filterForm.addEventListener("submit", function () {
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
