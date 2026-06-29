<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tautan Tidak Valid - ArsiPSR</title>
    @vite (['resources/css/app.css', 'resources/sass/app.scss', 'resources/js/app.js'])
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />
    <style>
        body {
            font-family: "Inter", sans-serif;
            background-color: #f8f9fc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            max-width: 450px;
            width: 100%;
            border: none;
            border-top: 5px solid #dc3545;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="card error-card text-center p-4">
        <div class="card-body">
            <i class="bi bi-exclamation-circle error-icon mb-3 d-block"></i>
            <h4 class="card-title fw-bold text-dark mb-3">
                Tautan Tidak Valid
            </h4>
            <p class="card-text text-muted mb-4">{{ $message }}</p>

            <a
                href="{{ route('dashboard') }}"
                class="btn btn-primary px-4 py-2"
            >
                <i class="bi bi-house-door me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
