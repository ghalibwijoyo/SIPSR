<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
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

    @if(session('login_success'))
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var duration = 3000;
            var end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 5,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: ['#3B6D11', '#4a8a15', '#8bc34a', '#ffffff'],
                    zIndex: 2000
                });
                confetti({
                    particleCount: 5,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: ['#3B6D11', '#4a8a15', '#8bc34a', '#ffffff'],
                    zIndex: 2000
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());
        });
    </script>
    @endif
</body>
</html>
