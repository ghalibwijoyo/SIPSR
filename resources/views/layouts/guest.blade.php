<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIPSR - Sistem Informasi Pengarsipan PSR Tanaman PTPN IV Regional IV">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login — SIPSR')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sipsr-gradient">
    <div class="min-vh-100 d-flex align-items-center justify-content-center">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
