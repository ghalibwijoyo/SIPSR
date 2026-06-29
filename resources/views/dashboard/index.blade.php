@extends ('layouts.app')

@section ('title', 'Dashboard — ArsiPSR')

@section ('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">
                Selamat datang, {{ auth()->user()->nama_lengkap }}! 👋
            </h4>
            <p class="text-muted mb-0">Ringkasan sistem pengarsipan Anda saat ini.</p>
        </div>
    </div>

    {{-- 4 Cards Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a
                href="{{ route('dokumen.index') }}"
                class="text-decoration-none text-dark d-block h-100"
            >
                <div
                    class="card border-0 shadow-sm h-100 bg-white"
                    style="transition: transform 0.2s ease"
                    onmouseover="this.style.transform = 'translateY(-3px)'"
                    onmouseout="this.style.transform = 'translateY(0)'"
                >
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div
                                class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px"
                            >
                                <i class="bi bi-file-earmark-text fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Total Dokumen</p>
                            <h4 class="mb-0 fw-bold">
                                {{ number_format($totalDokumen, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a
                href="{{ route('dokumen.create') }}"
                class="text-decoration-none text-dark d-block h-100"
            >
                <div
                    class="card border-0 shadow-sm h-100 bg-white"
                    style="transition: transform 0.2s ease"
                    onmouseover="this.style.transform = 'translateY(-3px)'"
                    onmouseout="this.style.transform = 'translateY(0)'"
                >
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div
                                class="bg-success bg-opacity-10 text-success rounded d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px"
                            >
                                <i class="bi bi-cloud-arrow-up fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Upload Bulan Ini</p>
                            <h4 class="mb-0 fw-bold">
                                {{ number_format($uploadBulanIni, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            @if (auth()->user()->role === 'ADMIN')
                <a
                    href="{{ route('users.index') }}"
                    class="text-decoration-none text-dark d-block h-100"
                >

            @endif
            <div
                class="card border-0 shadow-sm h-100 bg-white"
                @if (auth()->user()->role === 'ADMIN') style="
                    transition: transform 0.2s ease;
                " onmouseover="
                    this.style.transform = 'translateY(-3px)'
                " onmouseout="this.style.transform = 'translateY(0)'" @endif
            >
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div
                            class="bg-warning bg-opacity-10 text-warning rounded d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px"
                        >
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small text-uppercase">Total User</p>
                        <h4 class="mb-0 fw-bold">
                            {{ number_format($totalUserAktif, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
            @if (auth()->user()->role === 'ADMIN')
                </a>
            @endif
        </div>
        <div class="col-md-3">
            <a
                href="{{ route('dokumen.index') }}"
                class="text-decoration-none text-dark d-block h-100"
            >
                <div
                    class="card border-0 shadow-sm h-100 bg-white"
                    style="transition: transform 0.2s ease"
                    onmouseover="this.style.transform = 'translateY(-3px)'"
                    onmouseout="this.style.transform = 'translateY(0)'"
                >
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div
                                class="bg-info bg-opacity-10 text-info rounded d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px"
                            >
                                <i class="bi bi-tags fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-muted mb-1 small text-uppercase">Kategori Top</p>
                            <h5
                                class="mb-0 fw-bold text-truncate"
                                style="max-width: 120px"
                                title="{{ $kategoriTerbanyak }}"
                            >
                                {{ $kategoriTerbanyak }}
                            </h5>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold">Dokumen per Kategori</h6>
                </div>
                <div
                    class="card-body d-flex justify-content-center align-items-center"
                    style="min-height: 350px"
                >
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold">Tren Upload ({{ date('Y') }})</h6>
                </div>
                <div class="card-body" style="min-height: 350px">
                    <canvas id="uploadChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Ringkasan --}}
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div
                    class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center"
                >
                    <h6 class="mb-0 fw-bold">Dokumen Terbaru</h6>
                    <a
                        href="{{ route('dokumen.index') }}"
                        class="btn btn-sm btn-light"
                        >Lihat Semua</a
                    >
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table
                            class="table table-hover table-striped align-middle mb-0"
                        >
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama Dokumen</th>
                                    <th>Kategori</th>
                                    <th class="pe-4 text-end">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestDocuments as $doc)
                                    <tr>
                                        <td class="ps-4">
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <i
                                                    class="bi bi-file-earmark-text text-primary me-2"
                                                ></i>
                                                <a
                                                    href="{{ route('dokumen.show', $doc->id) }}"
                                                    class="text-decoration-none text-dark fw-medium"
                                                    >{{ Str::limit($doc->nama_dokumen, 30) }}</a
                                                >
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-secondary"
                                                >{{ $doc->category->nama ?? '-' }}</span
                                            >
                                        </td>
                                        <td
                                            class="pe-4 text-end text-muted small"
                                        >
                                            {{ $doc->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="3"
                                            class="text-center py-4 text-muted"
                                        >
                                            Belum ada dokumen.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div
                    class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center"
                >
                    <h6 class="mb-0 fw-bold">Aktivitas Terbaru</h6>
                    <a
                        href="{{ route('aktivitas.index') }}"
                        class="btn btn-sm btn-light"
                        >Lihat Semua</a
                    >
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table
                            class="table table-hover table-striped align-middle mb-0"
                        >
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">User</th>
                                    <th>Aktivitas</th>
                                    <th class="pe-4 text-end">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestActivities as $log)
                                    <tr>
                                        <td class="ps-4">
                                            <div
                                                class="fw-medium text-truncate"
                                                style="max-width: 150px"
                                            >
                                                {{ $log->user->nama_lengkap ?? 'Sistem' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="text-muted small"
                                                >{{ Str::limit($log->detail, 40) }}</span
                                            >
                                        </td>
                                        <td
                                            class="pe-4 text-end text-muted small"
                                        >
                                            {{ $log->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="3"
                                            class="text-center py-4 text-muted"
                                        >
                                            Belum ada aktivitas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push ('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Data Kategori
            const categoryCtx = document
                .getElementById("categoryChart")
                .getContext("2d");
            const categoryLabels = {!! json_encode($chartKategoriLabels) !!};
            const categoryData = {!! json_encode($chartKategoriData) !!};

            // Warna tema dasar
            const baseColors = [
                "#0d6efd",
                "#198754",
                "#ffc107",
                "#dc3545",
                "#0dcaf0",
                "#6610f2",
                "#fd7e14",
                "#20c997",
                "#052c65",
                "#d63384",
                "#6f42c1",
                "#17a2b8",
                "#28a745",
                "#e83e8c",
                "#343a40",
            ];

            // Fungsi otomatis generate warna sesuai dengan jumlah data kategori
            const backgroundColors = categoryLabels.map((_, index) => {
                if (index < baseColors.length) {
                    return baseColors[index];
                }
                // Generate warna acak cerah menggunakan Golden Ratio jika baseColors habis
                const hue = (index * 137.508) % 360;
                return `hsl(${hue}, 70%, 50%)`;
            });

            new Chart(categoryCtx, {
                type: "doughnut",
                data: {
                    labels:
                        categoryLabels.length > 0 ? categoryLabels : ["Belum ada data"],
                    datasets: [
                        {
                            data: categoryData.length > 0 ? categoryData : [1],
                            backgroundColor:
                                categoryData.length > 0
                                    ? backgroundColors
                                    : ["#e9ecef"],
                            borderWidth: 0,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                boxWidth: 12,
                                padding: 15,
                                font: { size: 11 },
                            },
                        },
                    },
                    cutout: "70%",
                },
            });

            // Data Upload
            const uploadCtx = document.getElementById("uploadChart").getContext("2d");
            new Chart(uploadCtx, {
                type: "line",
                data: {
                    labels: {!! json_encode($chartUploadLabels) !!},
                    datasets: [
                        {
                            label: "Jumlah Upload",
                            data: {!! json_encode($chartUploadData) !!},
                            borderColor: "#198754",
                            backgroundColor: "rgba(25, 135, 84, 0.1)",
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: "#fff",
                            pointBorderColor: "#198754",
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: "rgba(0,0,0,0.8)",
                            padding: 10,
                            displayColors: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { size: 11 },
                            },
                            grid: { borderDash: [2, 4], color: "#f0f0f0" },
                        },
                        x: {
                            ticks: { font: { size: 11 } },
                            grid: { display: false },
                        },
                    },
                },
            });
        });
    </script>
@endpush
