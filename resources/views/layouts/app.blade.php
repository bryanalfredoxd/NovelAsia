<!DOCTYPE html>
{{-- Dejamos que JS gestione la clase 'dark' según la preferencia guardada --}}
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'NovelAsia - Lee Novelas Chinas en Español')</title>

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    
    {{-- Iconos Material Symbols --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    {{-- Script Anti-Flicker para Modo Oscuro (Ejecutar antes de renderizar body) --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[var(--color-background)] text-[var(--color-reading-text)] min-h-screen flex flex-col antialiased transition-colors duration-300 selection:bg-[var(--color-primary)] selection:text-white overflow-x-hidden">

    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- Sistema de Notificaciones Flash (Toasts) --}}
    @if(session('success') || session('error'))
        <div id="toast-notification" class="fixed top-20 right-4 z-[100] transition-all duration-500 transform translate-x-0">
            <div class="bg-[var(--color-surface)] px-5 py-4 rounded-2xl shadow-xl border border-[var(--color-border)] flex items-center gap-4 max-w-sm">
                {{-- Icono Dinámico --}}
                <div class="size-10 rounded-xl flex items-center justify-center shrink-0 {{ session('error') ? 'bg-red-500/10 text-red-500' : 'bg-[var(--color-primary)]/10 text-[var(--color-primary)]' }}">
                    <span class="material-symbols-outlined font-bold">
                        {{ session('error') ? 'error' : 'verified_user' }}
                    </span>
                </div>
                
                <div class="flex-1">
                    <h4 class="font-bold text-sm">{{ session('error') ? 'Atención' : '¡Excelente!' }}</h4>
                    <p class="text-xs text-[var(--color-muted-text)] leading-snug">
                        {{ session('success') ?? session('error') }}
                    </p>
                </div>

                <button onclick="closeToast()" class="text-[var(--color-muted-text)] hover:text-[var(--color-primary)] transition-colors">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
        </div>
        
        <script>
            function closeToast() {
                const toast = document.getElementById('toast-notification');
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }
            setTimeout(closeToast, 5000);
        </script>
    @endif

    {{-- Contenido Principal --}}
    {{-- 'flex-grow' asegura que el footer siempre esté abajo incluso con poco contenido --}}
    <main class="flex-grow w-full">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-auto">
        @include('partials.footer')
    </footer>

    {{-- Scripts adicionales --}}
    @stack('scripts')

    {{-- Script Global para el Switch de Modo Oscuro --}}
    <script>
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
</body>
</html>