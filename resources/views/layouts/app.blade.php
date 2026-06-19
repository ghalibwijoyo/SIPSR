<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIPSR - Sistem Informasi Pengarsipan PSR Tanaman PTPN IV Regional IV">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPSR')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex vh-100 overflow-hidden">
        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Main Content Area --}}
        <div class="d-flex flex-column flex-grow-1 overflow-hidden">
            {{-- Navbar --}}
            @include('components.navbar')

            {{-- Page Content --}}
            <main class="flex-grow-1 overflow-auto bg-body-secondary">
                <div class="container-fluid p-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Toast Container --}}
    @include('components.toast')

    @stack('scripts')
</body>
</html>
