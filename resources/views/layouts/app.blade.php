<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'NovelAsia')</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#ec136d",
                        "background-light": "#f8f6f7",
                        "background-dark": "#0f172a",
                        "surface-dark": "#1e293b",
                    },
                    fontFamily: {
                        "sans": ["Inter", "sans-serif"],
                        "display": ["Outfit", "sans-serif"]
                    },
                    screens: {
                        'xs': '480px',
                    }
                },
            },
        }
    </script>

    <style type="text/tailwindcss">
        /* Estilos Glassmorphism Unificados */
        .glass {
            @apply bg-slate-900/80 backdrop-blur-md border border-white/10;
        }
        
        .sakura-glow {
            box-shadow: 0 0 20px rgba(236, 19, 109, 0.3);
        }
        
        @media (min-width: 768px) {
            ::-webkit-scrollbar {
                width: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #0f172a;
            }
            ::-webkit-scrollbar-thumb {
                background: #334155;
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #ec136d;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-slate-950 font-sans text-slate-200 min-h-screen pb-24 md:pb-0 transition-colors duration-300">

    @include('partials.navbar')

    @yield('content')

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
