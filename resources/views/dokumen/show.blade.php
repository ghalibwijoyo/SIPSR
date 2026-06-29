@extends ('layouts.app')

@section ('title', $dokumen->nama_dokumen . ' — ArsiPSR')

@section ('content')
    {{-- Page Header --}}
    <div
        class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2"
    >
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Detail Dokumen</h1>
            <p class="text-muted mb-0">
                <a
                    href="{{ route('dokumen.index') }}"
                    class="text-decoration-none"
                    >Dokumen</a
                >
                <i class="bi bi-chevron-right mx-1 small"></i>
                {{ Str::limit($dokumen->nama_dokumen, 40) }}
            </p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Right (Visually Left): File Preview --}}
        <div class="col-lg-7 order-1 order-lg-1">
            <div
                class="card border-0 shadow-sm sticky-top"
                style="top: 1.5rem; z-index: 10"
            >
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center w-100">
                        {{-- Toggle button, only visible on screens smaller than lg (vertical layout) --}}
                        <button
                            class="btn btn-sm btn-outline-secondary d-lg-none me-3"
                            id="previewToggleBtn"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#previewCollapse"
                            aria-expanded="true"
                            aria-controls="previewCollapse"
                            title="Tutup/Buka Preview"
                        >
                            <i
                                class="bi bi-chevron-right d-inline-block"
                                id="previewToggleIcon"
                                style="transition: transform 0.3s ease"
                            ></i>
                        </button>
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="bi bi-eye me-2 text-sipsr-primary"></i
                            >Preview Dokumen
                        </h5>
                        <style>
                            #previewToggleBtn[aria-expanded="true"]
                                #previewToggleIcon {
                                transform: rotate(90deg);
                            }
                        </style>
                    </div>
                </div>
                <div class="collapse show d-lg-block" id="previewCollapse">
                    <div class="card-body p-0">
                        @if (Str::endsWith($dokumen->file_name, '.pdf'))
                            <iframe
                                src="{{ route('dokumen.preview', $dokumen) }}"
                                class="w-100 border-0"
                                style="height: calc(100vh - 110px)"
                                title="Preview {{ $dokumen->nama_dokumen }}"
                                id="pdf-preview"
                            ></iframe>
                        @else
                            <div
                                class="text-center py-5 text-muted d-flex flex-column justify-content-center"
                                style="height: calc(100vh - 110px)"
                            >
                                <div>
                                    <i
                                        class="bi bi-file-earmark-word fs-1 text-primary d-block mb-3"
                                    ></i>
                                    <p class="mb-1 fw-semibold">Preview tidak tersedia</p>
                                    <p class="small mb-3">Format DOC/DOCX tidak dapat ditampilkan langsung di browser.</p>
                                    <a
                                        href="{{ route('dokumen.download', $dokumen) }}"
                                        class="btn btn-success btn-sm"
                                    >
                                        <i class="bi bi-download me-1"></i
                                        >Download untuk melihat
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Left (Visually Right): Metadata --}}
        <div class="col-lg-5 order-2 order-lg-2">
            {{-- Document Info Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div
                    class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                >
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-info-circle me-2 text-sipsr-primary"></i
                        >Informasi Dokumen
                    </h5>
                    <span
                        class="badge bg-sipsr-primary"
                        >{{ $dokumen->category->nama ?? '-' }}</span
                    >
                </div>
                <div class="card-body p-0">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td
                                    class="ps-3 text-muted fw-semibold"
                                    style="width: 140px"
                                >
                                    Nomor
                                </td>
                                <td>
                                    <code
                                        class="text-sipsr-primary"
                                        >{{ $dokumen->nomor_dokumen }}</code
                                    >
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">
                                    Nama
                                </td>
                                <td class="fw-medium">
                                    {{ $dokumen->nama_dokumen }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">
                                    Nama Bank
                                </td>
                                <td>{{ $dokumen->bank->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">
                                    Kategori
                                </td>
                                <td>
                                    <span
                                        class="badge bg-sipsr-primary bg-opacity-10 text-sipsr-light"
                                    >
                                        {{ $dokumen->category->nama ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">
                                    Tanggal
                                </td>
                                <td>
                                    {{ $dokumen->tanggal_dokumen?->format('d F Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">
                                    Deskripsi
                                </td>
                                <td>{{ $dokumen->deskripsi ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">
                                    File
                                </td>
                                <td>
                                    <i
                                        class="bi bi-file-earmark-{{ Str::endsWith($dokumen->file_name, '.pdf') ? 'pdf text-danger' : 'word text-primary' }} me-1"
                                    ></i>
                                    {{ $dokumen->file_name }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">
                                    Diupload oleh
                                </td>
                                <td>
                                    {{ $dokumen->uploader->nama_lengkap ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">
                                    Tanggal Upload
                                </td>
                                <td class="small">
                                    {{ $dokumen->created_at?->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            @if ($dokumen->updatedBy)
                                <tr>
                                    <td class="ps-3 text-muted fw-semibold">
                                        Terakhir Diedit
                                    </td>
                                    <td class="small">
                                        {{ $dokumen->updatedBy->nama_lengkap }} · {{ $dokumen->updated_at?->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex gap-2 flex-wrap">
                        <a
                            href="{{ route('dokumen.download', $dokumen) }}"
                            class="btn btn-success"
                            id="btn-download"
                        >
                            <i class="bi bi-download me-1"></i>Download
                        </a>
                        <button
                            type="button"
                            class="btn btn-info text-white"
                            data-bs-toggle="modal"
                            data-bs-target="#shareModal"
                            id="btn-share"
                        >
                            <i class="bi bi-share me-1"></i>Bagikan
                        </button>
                        <a
                            href="{{ route('dokumen.edit', $dokumen) }}"
                            class="btn btn-warning"
                            id="btn-edit"
                        >
                            <i class="bi bi-pencil-square me-1"></i>Edit
                        </a>
                        <form
                            method="POST"
                            action="{{ route('dokumen.destroy', $dokumen) }}"
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
                                class="btn btn-danger"
                                id="btn-delete"
                            >
                                <i class="bi bi-trash3 me-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Share Links --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-link-45deg me-2 text-sipsr-primary"></i
                        >Tautan Berbagi
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table
                            class="table table-sm table-hover mb-0 align-middle"
                        >
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">URL</th>
                                    <th>Kedaluwarsa</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3 text-nowrap">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="share-links-table-body">
                                @forelse ($dokumen->shareLinks()->orderBy('created_at', 'desc')->get() as $link)
                                    <tr>
                                        <td
                                            class="ps-3 small"
                                            style="width: 45%"
                                        >
                                            <div
                                                class="input-group input-group-sm"
                                            >
                                                <input
                                                    type="text"
                                                    class="form-control bg-white"
                                                    value="{{ route('share.show', $link->token) }}"
                                                    readonly
                                                    id="link-{{ $link->id }}"
                                                />
                                                <button
                                                    class="btn btn-outline-secondary btn-copy"
                                                    type="button"
                                                    data-clipboard-target="#link-{{ $link->id }}"
                                                    title="Salin"
                                                >
                                                    <i
                                                        class="bi bi-clipboard"
                                                    ></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="small">
                                            {{ $link->expired_at ? $link->expired_at->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td class="small">
                                            @if ($link->revoked_at)
                                                <span class="badge bg-danger"
                                                    >Dicabut</span
                                                >
                                            @elseif ($link->expired_at && $link->expired_at < now())
                                                <span
                                                    class="badge bg-warning text-dark"
                                                    >Kedaluwarsa</span
                                                >
                                            @else
                                                <span class="badge bg-success"
                                                    >Aktif</span
                                                >
                                            @endif
                                        </td>
                                        <td class="text-end pe-3 text-nowrap">
                                            @if (!$link->revoked_at && (!$link->expired_at || $link->expired_at >= now()))
                                                <form
                                                    method="POST"
                                                    action="{{ route('share.revoke', $link->id) }}"
                                                    class="d-inline"
                                                    onsubmit="
                                                        return confirm(
                                                            'Cabut tautan ini?',
                                                        );
                                                    "
                                                >
                                                    @csrf
                                                    @method ('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger py-0 px-1"
                                                        title="Cabut Tautan"
                                                    >
                                                        <i
                                                            class="bi bi-x-circle"
                                                        ></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-link-row">
                                        <td
                                            colspan="4"
                                            class="text-center py-3 text-muted small"
                                        >
                                            Belum ada tautan berbagi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Change History --}}
            @if ($dokumen->histories->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i
                                class="bi bi-clock-history me-2 text-sipsr-primary"
                            ></i
                            >Riwayat Perubahan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div
                            class="position-relative"
                            style="
                                border-left: 2px solid #e9ecef;
                                margin-left: 10px;
                                padding-left: 20px;
                            "
                        >
                            @foreach ($dokumen->histories->sortByDesc('changed_at') as $hist)
                                <div class="mb-4 position-relative">
                                    <div
                                        class="position-absolute"
                                        style="
                                            left: -29px;
                                            top: 0;
                                            background: #fff;
                                            border: 2px solid #3b6d11;
                                            border-radius: 50%;
                                            width: 16px;
                                            height: 16px;
                                        "
                                    ></div>
                                    <div class="small text-muted fw-bold mb-1">
                                        {{ $hist->changed_at?->format('d/m/Y H:i') }} ·
                                        Diubah oleh: {{ $hist->changedBy->nama_lengkap ?? '-' }}
                                    </div>
                                    <div class="bg-light p-2 rounded-3 small">
                                        <div class="d-flex mb-1">
                                            <div
                                                class="fw-semibold"
                                                style="width: 70px"
                                            >
                                                Field
                                            </div>
                                            <div>: {{ $hist->field_name }}</div>
                                        </div>
                                        <div class="d-flex mb-1">
                                            <div
                                                class="fw-semibold text-danger"
                                                style="width: 70px"
                                            >
                                                Sebelum
                                            </div>
                                            <div class="text-danger">
                                                : {{ $hist->old_value ?: '-' }}
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div
                                                class="fw-semibold text-success"
                                                style="width: 70px"
                                            >
                                                Sesudah
                                            </div>
                                            <div class="text-success">
                                                : {{ $hist->new_value ?: '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i
                                class="bi bi-clock-history me-2 text-sipsr-primary"
                            ></i
                            >Riwayat Perubahan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info border-0 mb-0 small">
                            <i class="bi bi-info-circle me-2"></i>Belum ada
                            riwayat perubahan.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>

    {{-- Share Modal --}}
    <div
        class="modal fade"
        id="shareModal"
        tabindex="-1"
        aria-labelledby="shareModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="shareModalLabel">
                        Bagikan Dokumen
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <p>Buat tautan baru untuk membagikan dokumen <strong>{{ $dokumen->nama_dokumen }}</strong>.</p>

                    <div class="mb-4">
                        <div
                            class="form-check form-switch mb-3 pb-3 border-bottom"
                        >
                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                id="isPermanentLink"
                                checked
                            />
                            <label
                                class="form-check-label fw-semibold small"
                                for="isPermanentLink"
                                >Tautan Permanen (Selamanya)</label
                            >
                            <div class="form-text" style="font-size: 0.75rem">
                                Tautan tidak otomatis kedaluwarsa, namun akses
                                tetap dapat dicabut manual kapan saja.
                            </div>
                        </div>

                        <div
                            class="duration-wrapper transition-all overflow-hidden"
                            id="durationWrapper"
                            style="
                                max-height: 150px;
                                opacity: 1;
                                transform-origin: top;
                            "
                        >
                            <div
                                class="d-flex justify-content-between align-items-center mb-2"
                            >
                                <label
                                    for="linkDuration"
                                    class="form-label fw-semibold mb-0 small text-muted"
                                    >Atur Masa Berlaku:</label
                                >
                                <span
                                    id="durationValue"
                                    class="badge bg-sipsr-primary px-3 py-1 shadow-sm transition-all"
                                    style="font-size: 0.8rem"
                                    >7 Hari</span
                                >
                            </div>
                            <div
                                class="duration-control-container px-1 pt-1 pb-2"
                            >
                                <input
                                    type="range"
                                    class="form-range custom-sipsr-range"
                                    id="linkDuration"
                                    min="1"
                                    max="168"
                                    step="1"
                                    value="168"
                                    list="magnetic-points"
                                />
                                <datalist id="magnetic-points">
                                    <option value="24"></option>
                                    <option value="48"></option>
                                    <option value="72"></option>
                                    <option value="96"></option>
                                    <option value="120"></option>
                                    <option value="144"></option>
                                    <option value="168"></option>
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <div id="share-result" class="d-none">
                        <div class="mb-3">
                            <label class="form-label fw-bold small"
                                >Tautan Berhasil Dibuat:</label
                            >
                            <div class="input-group">
                                <input
                                    type="text"
                                    id="new-share-link"
                                    class="form-control"
                                    readonly
                                />
                                <button
                                    class="btn btn-primary"
                                    type="button"
                                    id="btn-copy-new-link"
                                >
                                    <i class="bi bi-clipboard me-1"></i> Salin
                                </button>
                            </div>
                            <div
                                class="text-success small mt-1 d-none"
                                id="copy-success-msg"
                            >
                                <i class="bi bi-check-circle me-1"></i>Tersalin
                                ke clipboard!
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Tutup
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        id="btn-generate-link"
                    >
                        <span
                            class="spinner-border spinner-border-sm d-none me-1"
                            role="status"
                            aria-hidden="true"
                            id="generate-spinner"
                        ></span>
                        <i class="bi bi-link-45deg me-1" id="generate-icon"></i
                        >Buat Tautan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ArsiPSR Custom Range Slider */
        .custom-sipsr-range {
            height: 6px;
            border-radius: 5px;
            background: #e9ecef;
            outline: none;
            -webkit-appearance: none;
        }
        .custom-sipsr-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--bs-green, #198754);
            border: 3px solid white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        .custom-sipsr-range::-webkit-slider-thumb:active {
            transform: scale(1.3);
        }
        .custom-sipsr-range::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--bs-green, #198754);
            border: 3px solid white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        .custom-sipsr-range::-moz-range-thumb:active {
            transform: scale(1.3);
        }
        .transition-all {
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Copy existing links
            document.querySelectorAll(".btn-copy").forEach((btn) => {
                btn.addEventListener("click", function () {
                    const targetId = this.getAttribute("data-clipboard-target");
                    const input = document.querySelector(targetId);
                    input.select();
                    input.setSelectionRange(0, 99999); // For mobile devices
                    navigator.clipboard.writeText(input.value);

                    const icon = this.querySelector("i");
                    icon.classList.remove("bi-clipboard");
                    icon.classList.add("bi-check2");
                    setTimeout(() => {
                        icon.classList.remove("bi-check2");
                        icon.classList.add("bi-clipboard");
                    }, 2000);
                });
            });

            // Duration Configuration Logic
            const durationInput = document.getElementById("linkDuration");
            const durationValue = document.getElementById("durationValue");
            const isPermanentToggle = document.getElementById("isPermanentLink");
            const sliderContainer = document.querySelector(
                ".duration-control-container",
            );
            const durationLabel = document.querySelector('label[for="linkDuration"]');

            function formatDuration(hours) {
                if (hours < 24) {
                    return hours + " Jam";
                } else {
                    const days = Math.floor(hours / 24);
                    const remainder = hours % 24;
                    if (remainder === 0) {
                        return days + " Hari";
                    } else {
                        return days + " Hari " + remainder + " Jam";
                    }
                }
            }

            if (durationInput && durationValue && isPermanentToggle) {
                durationInput.addEventListener("input", function () {
                    durationValue.textContent = formatDuration(this.value);
                    // Bouncy text effect
                    durationValue.style.transform = "scale(1.15)";
                    setTimeout(() => {
                        durationValue.style.transform = "scale(1)";
                    }, 150);
                });

                isPermanentToggle.addEventListener("change", function () {
                    if (this.checked) {
                        durationInput.disabled = true;
                        durationValue.textContent = "Permanen";
                        durationValue.classList.replace(
                            "bg-sipsr-primary",
                            "bg-primary",
                        );
                        sliderContainer.style.opacity = "0.3";
                        sliderContainer.style.pointerEvents = "none";
                        sliderContainer.style.filter = "grayscale(100%)";
                        if (durationLabel) durationLabel.style.opacity = "0.3";
                    } else {
                        durationInput.disabled = false;
                        durationValue.textContent = formatDuration(durationInput.value);
                        durationValue.classList.replace(
                            "bg-primary",
                            "bg-sipsr-primary",
                        );
                        sliderContainer.style.opacity = "1";
                        sliderContainer.style.pointerEvents = "auto";
                        sliderContainer.style.filter = "grayscale(0%)";
                        if (durationLabel) durationLabel.style.opacity = "1";
                    }
                });

                // Trigger initial state
                isPermanentToggle.dispatchEvent(new Event("change"));
            }

            // Generate new link
            const btnGenerate = document.getElementById("btn-generate-link");
            const spinner = document.getElementById("generate-spinner");
            const icon = document.getElementById("generate-icon");
            const shareResult = document.getElementById("share-result");
            const inputNewLink = document.getElementById("new-share-link");
            const btnCopyNew = document.getElementById("btn-copy-new-link");
            const copySuccessMsg = document.getElementById("copy-success-msg");

            if (btnGenerate) {
                btnGenerate.addEventListener("click", async function () {
                    // Show loading state
                    btnGenerate.disabled = true;
                    spinner.classList.remove("d-none");
                    icon.classList.add("d-none");

                    try {
                        const isPermanent =
                            document.getElementById("isPermanentLink")?.checked ||
                            false;
                        const duration =
                            document.getElementById("linkDuration")?.value || 168;

                        const response = await fetch(
                            `{{ route('dokumen.share', $dokumen->id) }}`,
                            {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                },
                                body: JSON.stringify({
                                    is_permanent: isPermanent,
                                    duration_hours: parseInt(duration),
                                }),
                            },
                        );

                        const result = await response.json();

                        if (response.ok && result.status === "success") {
                            // Show result
                            inputNewLink.value = result.data.url;
                            shareResult.classList.remove("d-none");
                            btnGenerate.classList.add("d-none");

                            // Reload page after modal closed to show the new link in table
                            const shareModal = document.getElementById("shareModal");
                            shareModal.addEventListener("hidden.bs.modal", function () {
                                window.location.reload();
                            });
                        } else {
                            alert("Gagal membuat tautan.");
                            btnGenerate.disabled = false;
                        }
                    } catch (error) {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan sistem.");
                        btnGenerate.disabled = false;
                    } finally {
                        spinner.classList.add("d-none");
                        icon.classList.remove("d-none");
                    }
                });
            }

            // Copy new link
            if (btnCopyNew) {
                btnCopyNew.addEventListener("click", function () {
                    inputNewLink.select();
                    inputNewLink.setSelectionRange(0, 99999);
                    navigator.clipboard.writeText(inputNewLink.value);
                    copySuccessMsg.classList.remove("d-none");
                    setTimeout(() => {
                        copySuccessMsg.classList.add("d-none");
                    }, 3000);
                });
            }
        });
    </script>
@endsection
