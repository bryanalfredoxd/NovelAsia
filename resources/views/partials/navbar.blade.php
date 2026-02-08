{{-- Desktop Navigation --}}
<nav class="hidden md:block fixed top-0 left-0 right-0 z-50 glass border-b border-white/10 px-6 lg:px-8 py-3 transition-all duration-300">
    <div class="max-w-[1440px] mx-auto flex items-center justify-between gap-8">
        
        {{-- LOGO --}}
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="size-10 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg sakura-glow group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-2xl">auto_stories</span>
                </div>
                <h1 class="text-2xl font-display font-bold tracking-tight bg-gradient-to-r from-primary to-indigo-400 bg-clip-text text-transparent">
                    NovelAsia
                </h1>
            </a>
        </div>
        
        {{-- SEARCH BAR (Oculto en Login/Registro) --}}
        @unless(request()->routeIs('login') || request()->routeIs('register'))
        <div class="flex-1 max-w-xl lg:max-w-2xl">
            <label class="relative flex items-center w-full group">
                <div class="absolute left-4 text-slate-400 group-focus-within:text-primary transition-colors">
                    <span class="material-symbols-outlined">search</span>
                </div>
                <input 
                    class="w-full h-11 bg-slate-900/50 border border-white/10 rounded-full pl-12 pr-4 text-sm text-white focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none placeholder:text-slate-500 transition-all" 
                    placeholder="Buscar títulos, autores o géneros..." 
                    type="text"
                />
            </label>
        </div>
        @endunless

        {{-- RIGHT SIDE ACTIONS --}}
        <div class="flex items-center gap-4 shrink-0">
            <a href="#" class="px-5 py-2 text-sm font-semibold text-slate-300 hover:text-primary transition-colors flex items-center gap-2 group">
                <span class="material-symbols-outlined group-hover:animate-bounce">library_books</span>
                Biblioteca
            </a>
            
            <div class="h-6 w-[1px] bg-white/10"></div>
            
            {{-- AUTH CHECK --}}
            @auth
                {{-- === USUARIO LOGUEADO === --}}
                
                {{-- Notifications --}}
                <button class="size-10 rounded-full hover:bg-white/5 flex items-center justify-center text-slate-300 hover:text-white transition-colors relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2.5 right-2.5 size-2 bg-primary rounded-full border border-slate-900 animate-pulse"></span>
                </button>

                {{-- User Dropdown --}}
                <div class="relative group/dropdown">
                    <button class="flex items-center gap-3 pl-2 pr-1 py-1 rounded-full hover:bg-white/5 transition-colors focus:outline-none">
                        <div class="text-right hidden lg:block leading-tight">
                            <p class="text-xs font-bold text-white">{{ Auth::user()->username }}</p>
                            <p class="text-[10px] text-primary font-bold uppercase tracking-wider">{{ Auth::user()->rol ?? 'Lector' }}</p>
                        </div>
                        
                        {{-- Avatar --}}
                        <div class="size-9 rounded-full bg-slate-800 border-2 border-primary/50 overflow-hidden relative shadow-lg sakura-glow">
                            <img 
                                src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->username).'&background=ec136d&color=fff&bold=true' }}" 
                                alt="{{ Auth::user()->username }}" 
                                class="w-full h-full object-cover"
                            >
                        </div>
                        <span class="material-symbols-outlined text-slate-400 group-hover/dropdown:rotate-180 transition-transform duration-300">expand_more</span>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div class="absolute right-0 top-full mt-2 w-56 bg-slate-900/95 backdrop-blur-xl border border-white/10 rounded-xl shadow-2xl opacity-0 invisible group-hover/dropdown:opacity-100 group-hover/dropdown:visible transition-all duration-200 transform origin-top-right z-50 overflow-hidden translate-y-2 group-hover/dropdown:translate-y-0">
                        <div class="p-2 space-y-1">
                            <div class="px-3 py-2 border-b border-white/5 mb-1 lg:hidden">
                                <p class="text-sm font-bold text-white">{{ Auth::user()->username }}</p>
                            </div>
                            
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[20px]">person</span> Perfil
                            </a>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[20px]">settings</span> Ajustes
                            </a>
                            <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[20px]">diamond</span> Suscripción
                            </a>
                            
                            <div class="h-px bg-white/10 my-1"></div>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-lg transition-colors text-left">
                                    <span class="material-symbols-outlined text-[20px]">logout</span> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            @else
                {{-- === GUEST (NO LOGUEADO) === --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-bold text-slate-300 hover:text-white hover:bg-white/5 rounded-lg transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">login</span>
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-sm font-bold rounded-xl transition-all sakura-glow flex items-center gap-2 transform active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                        Registrarse
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>

{{-- Mobile Top Bar --}}
<nav class="md:hidden fixed top-0 left-0 right-0 z-50 glass px-4 py-3 flex items-center justify-between border-b border-white/5 backdrop-blur-md bg-slate-950/80">
    <div class="flex items-center gap-2">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div class="size-8 bg-primary rounded-lg flex items-center justify-center text-white shadow-lg sakura-glow">
                <span class="material-symbols-outlined text-lg">auto_stories</span>
            </div>
            <h1 class="text-lg font-bold tracking-tight bg-gradient-to-r from-primary to-indigo-400 bg-clip-text text-transparent">
                NovelAsia
            </h1>
        </a>
    </div>
    
    <div class="flex items-center gap-3">
        @auth
            <button class="text-slate-300 hover:text-white relative">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-0 right-0 size-2 bg-primary rounded-full animate-pulse"></span>
            </button>
            <a href="#" class="size-8 rounded-full bg-slate-700 border border-primary/50 overflow-hidden">
                <img 
                    src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->username).'&background=ec136d&color=fff' }}" 
                    alt="User" 
                    class="w-full h-full object-cover"
                >
            </a>
        @else
            <a href="{{ route('login') }}" class="text-slate-300 hover:text-white p-2">
                <span class="material-symbols-outlined">login</span>
            </a>
        @endauth
    </div>
</nav>

{{-- Mobile Bottom Navigation --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 glass px-6 py-3 pb-6 border-t border-white/10 bg-slate-950/90 backdrop-blur-xl flex items-center justify-between">
    <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('home') ? 'text-primary' : 'text-slate-400 hover:text-slate-200' }} transition-colors group">
        <span class="material-symbols-outlined group-active:scale-90 transition-transform {{ request()->routeIs('home') ? 'fill-current' : '' }}">home</span>
        <span class="text-[10px] font-bold">Inicio</span>
    </a>
    
    <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-200 transition-colors group">
        <span class="material-symbols-outlined group-active:scale-90 transition-transform">library_books</span>
        <span class="text-[10px] font-bold">Librería</span>
    </a>
    
    <button class="size-14 -mt-10 bg-primary hover:bg-primary-hover text-white rounded-full flex items-center justify-center sakura-glow shadow-xl border-4 border-slate-950 active:scale-90 transition-all transform">
        <span class="material-symbols-outlined text-2xl">search</span>
    </button>
    
    <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-200 transition-colors group">
        <span class="material-symbols-outlined group-active:scale-90 transition-transform">auto_awesome</span>
        <span class="text-[10px] font-bold">Explorar</span>
    </a>
    
    @auth
        <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-200 transition-colors group">
            <div class="size-6 rounded-full overflow-hidden border border-current group-active:scale-90 transition-transform">
                <img 
                    src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->username).'&background=ec136d&color=fff' }}" 
                    class="w-full h-full object-cover"
                >
            </div>
            <span class="text-[10px] font-bold">Perfil</span>
        </a>
    @else
        <a href="{{ route('register') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-200 transition-colors group">
            <span class="material-symbols-outlined group-active:scale-90 transition-transform">person</span>
            <span class="text-[10px] font-bold">Cuenta</span>
        </a>
    @endauth
</nav>