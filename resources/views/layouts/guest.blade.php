<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
        name="description"
        content="ArsiPSR - Sistem Informasi Pengarsipan PSR Tanaman PTPN IV Regional IV"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield ('title', 'Login — ArsiPSR')</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png" />
    @vite (['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sipsr-gradient">
    <canvas id="palm-canopy-canvas" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; opacity: 0.9;"></canvas>
    
    <div class="min-vh-100 d-flex align-items-center justify-content-center" style="position: relative; z-index: 10;">
        @yield ('content')
    </div>

    @stack ('scripts')
    <script src="{{ asset('js/palm-canopy.js') }}"></script>
</body>
</html>
