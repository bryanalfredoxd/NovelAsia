<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'NovelAsia')</title>

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    
    {{-- Iconos --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 font-sans text-slate-200 min-h-screen flex flex-col antialiased selection:bg-primary selection:text-white overflow-x-hidden">

    {{-- 1. Navbar con Lógica de Sesión --}}
    @include('partials.navbar')

    {{-- 2. Sistema de Notificaciones Flash (Toasts) --}}
    @if(session('success') || session('error'))
        <div id="toast-notification" class="fixed top-24 right-4 z-50 animate-fade-in-left">
            <div class="glass px-6 py-4 rounded-xl shadow-2xl border-l-4 {{ session('error') ? 'border-red-500' : 'border-primary' }} flex items-center gap-4 max-w-sm">
                <div class="p-2 rounded-full {{ session('error') ? 'bg-red-500/20 text-red-400' : 'bg-primary/20 text-primary' }}">
                    <span class="material-symbols-outlined">{{ session('error') ? 'error' : 'check_circle' }}</span>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm">{{ session('error') ? 'Error' : '¡Éxito!' }}</h4>
                    <p class="text-xs text-slate-300">{{ session('success') ?? session('error') }}</p>
                </div>
                <button onclick="document.getElementById('toast-notification').remove()" class="text-slate-500 hover:text-white ml-2">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
        </div>
        
        <script>
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                const toast = document.getElementById('toast-notification');
                if(toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 5000);
        </script>
    @endif

    {{-- 3. Contenido Principal --}}
    <main class="flex-grow relative z-0">
        @yield('content')
    </main>

    {{-- 4. Footer --}}
    @include('partials.footer')

    {{-- Scripts adicionales --}}
    @stack('scripts')
</body>
</html>