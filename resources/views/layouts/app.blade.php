<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark"> <!-- Aquí habilitamos el modo oscuro -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Language" content="es">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Navigation transition overlay — hides all flashes during wire:navigate DOM swap */
        #nav-transition-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: #111827; /* dark:bg-gray-900 */
            opacity: 0;
            pointer-events: none;
            transition: opacity 80ms ease;
        }
        #nav-transition-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
    </style>
</head>

<body class="font-sans antialiased" x-data="{ mobileSidebarOpen: false }">

    <!-- Navigation transition overlay: covers the screen during wire:navigate page swap to eliminate any flash -->
    <div id="nav-transition-overlay" style="display:none;"></div>

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 bg-gray-100 dark:bg-gray-900">
                {{ $slot }}
            </main>
        </div>
    </div>
    <x-toast />
    @livewireScripts
    <script>
        // ─── Wire:navigate transition overlay ─────────────────────────────────
        (function () {
            const overlay = document.getElementById('nav-transition-overlay');
            let hideTimer = null;

            function showOverlay() {
                if (hideTimer) clearTimeout(hideTimer);
                overlay.style.display = 'block';
                // Force reflow so transition plays
                overlay.offsetHeight;
                overlay.classList.add('active');
            }

            function hideOverlay() {
                hideTimer = setTimeout(function () {
                    overlay.classList.remove('active');
                    // Hide completely after transition
                    setTimeout(function() { overlay.style.display = 'none'; }, 100);
                }, 80);
            }

            // Livewire v3 / wire:navigate events
            document.addEventListener('livewire:navigate',       showOverlay);
            document.addEventListener('livewire:navigating',     showOverlay);
            document.addEventListener('livewire:navigated',      hideOverlay);
            document.addEventListener('livewire:navigate-start', showOverlay);
            document.addEventListener('livewire:navigate-end',   hideOverlay);

            // Always ensure overlay is hidden on regular page loads
            document.addEventListener('DOMContentLoaded', function() {
                overlay.style.display = 'none';
                overlay.classList.remove('active');
            });
        })();

        // ─── Double-submit prevention ──────────────────────────────────────────
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.dataset.submitted === "true") {
                e.preventDefault();
                return;
            }
            form.dataset.submitted = "true";
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Procesando...';
            }
        });

        // ─── Task Locking Helper ──────────────────────────────────────────────
        window.segesLock = {
            lock(type, id) {
                return fetch(`/` + type + `/` + id + `/lock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(res => res.json());
            },
            unlock(type, id) {
                return fetch(`/` + type + `/` + id + `/unlock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(res => res.json());
            },
            unlockAll() {
                const fd = new FormData();
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                navigator.sendBeacon('/unlock-all', fd);
            }
        };

        // Release all locks held by the current user when leaving the app or tab
        window.addEventListener('pagehide', function() {
            window.segesLock.unlockAll();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    @stack('scripts')
</body>

</html>