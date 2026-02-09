{{-- Desktop Navigation --}}
<nav class="hidden md:block fixed top-0 left-0 right-0 z-50 bg-[var(--color-surface)]/80 backdrop-blur-md border-b border-[var(--color-border)] px-6 lg:px-8 py-3 transition-all duration-300">
    <div class="max-w-[1440px] mx-auto flex items-center justify-between gap-8">
        
        {{-- LOGO --}}
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="size-10 bg-[var(--color-primary)] rounded-xl flex items-center justify-center text-white shadow-lg transition-transform group-hover:scale-105">
                    <span class="material-symbols-outlined text-2xl">auto_stories</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-[var(--color-reading-text)]">
                    Novel<span class="text-[var(--color-primary)]">Asia</span>
                </h1>
            </a>
        </div>
        
        {{-- SEARCH BAR (desktop) --}}
        <div class="flex-1 max-w-xl">
            <label class="relative flex items-center w-full group">
                <div class="absolute left-4 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] transition-colors">
                    <span class="material-symbols-outlined text-xl">search</span>
                </div>
                <input 
                    class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border)] rounded-full pl-12 pr-4 text-sm text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] focus:outline-none transition-all" 
                    placeholder="Buscar historias épicas..." 
                    type="text"
                />
            </label>
        </div>

        {{-- ACTIONS --}}
        <div class="flex items-center gap-2 shrink-0">
            {{-- Botón de Modo Oscuro/Claro --}}
            <button onclick="toggleTheme()" class="size-10 rounded-full flex items-center justify-center text-[var(--color-muted-text)] hover:bg-[var(--color-background)] hover:text-[var(--color-primary)] transition-all" title="Cambiar tema">
                <span class="material-symbols-outlined dark:hidden">dark_mode</span>
                <span class="material-symbols-outlined hidden dark:block">light_mode</span>
            </button>

            <a href="{{ route('library') }}" class="px-4 py-2 text-sm font-bold text-[var(--color-muted-text)] hover:text-[var(--color-primary)] transition-colors flex items-center gap-2 group">
                <span class="material-symbols-outlined group-hover:animate-pulse">library_books</span>
                <span class="hidden lg:inline">Biblioteca</span>
            </a>
            
            <div class="h-6 w-px bg-[var(--color-border)] mx-2"></div>
            
            @auth
                {{-- USUARIO LOGUEADO --}}
                <div class="relative group/dropdown">
                    <button class="flex items-center gap-3 p-1 rounded-full hover:bg-[var(--color-background)] transition-colors focus:outline-none">
                        <div class="size-9 rounded-full border-2 border-[var(--color-primary)]/30 overflow-hidden shadow-sm">
                            <img src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->username).'&background=C0392B&color=fff' }}" class="w-full h-full object-cover">
                        </div>
                        <span class="material-symbols-outlined text-[var(--color-muted-text)] group-hover/dropdown:rotate-180 transition-transform">expand_more</span>
                    </button>

                    <div class="absolute right-0 top-full mt-2 w-52 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl shadow-xl opacity-0 invisible group-hover/dropdown:opacity-100 group-hover/dropdown:visible transition-all duration-200 z-50 overflow-hidden">
                        <div class="p-2">
                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[var(--color-reading-text)] hover:bg-[var(--color-background)] rounded-xl transition-colors">
                                <span class="material-symbols-outlined text-lg">person</span> Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                    <span class="material-symbols-outlined text-lg">logout</span> Salir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                {{-- INVITADO --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-bold text-[var(--color-reading-text)] hover:bg-[var(--color-background)] rounded-xl transition-all">Entrar</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-[var(--color-primary)] text-white text-sm font-bold rounded-xl shadow-md hover:bg-[var(--color-primary-dark)] transition-all active:scale-95">Registrarse</a>
                </div>
            @endauth
        </div>
    </div>
</nav>

{{-- Mobile Top Bar --}}
<nav class="md:hidden fixed top-0 left-0 right-0 z-50 bg-[var(--color-surface)]/90 backdrop-blur-md px-4 py-3 flex items-center justify-between border-b border-[var(--color-border)]">
    <a href="{{ route('home') }}" class="flex items-center gap-2">
        <div class="size-8 bg-[var(--color-primary)] rounded-lg flex items-center justify-center text-white">
            <span class="material-symbols-outlined text-lg">auto_stories</span>
        </div>
        <h1 class="text-lg font-bold text-[var(--color-reading-text)]">NovelAsia</h1>
    </a>
    
    <div class="flex items-center gap-2">
        <button onclick="toggleTheme()" class="p-2 text-[var(--color-muted-text)]">
            <span class="material-symbols-outlined dark:hidden">dark_mode</span>
            <span class="material-symbols-outlined hidden dark:block">light_mode</span>
        </button>
        @auth
            <div class="size-8 rounded-full border border-[var(--color-primary)]/50 overflow-hidden">
                <img src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->username) }}" class="w-full h-full object-cover">
            </div>
        @endauth
    </div>
</nav>

{{-- Mobile Search (below top bar) --}}
<div class="md:hidden pt-16 px-4 bg-transparent">
    <div class="w-full max-w-xl mx-auto">
        <label class="relative flex items-center w-full group">
            <div class="absolute left-4 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] transition-colors">
                <span class="material-symbols-outlined text-xl">search</span>
            </div>
            <input 
                class="w-full h-11 bg-[var(--color-background)] border border-[var(--color-border)] rounded-full pl-12 pr-4 text-sm text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] focus:outline-none transition-all" 
                placeholder="Buscar historias épicas..." 
                type="text"
            />
        </label>
    </div>
</div>


