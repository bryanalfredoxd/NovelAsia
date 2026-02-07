    {{-- Desktop Navigation --}}
    <nav class="hidden md:block fixed top-0 left-0 right-0 z-50 glass border-b border-white/10 px-6 lg:px-8 py-3">
        <div class="max-w-[1440px] mx-auto flex items-center justify-between gap-8">
            <div class="flex items-center gap-3 shrink-0">
                <a href="/" class="flex items-center gap-3">
                    <div class="size-10 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg sakura-glow">
                        <span class="material-symbols-outlined text-2xl">psychology_alt</span>
                    </div>
                    <h1 class="text-2xl font-display font-bold tracking-tight bg-gradient-to-r from-primary to-indigo-400 bg-clip-text text-transparent">NovelAsia</h1>
                </a>
            </div>
            
            @unless(request()->is('login') || request()->is('register'))
            <div class="flex-1 max-w-xl lg:max-w-2xl">
                <label class="relative flex items-center w-full">
                    <div class="absolute left-4 text-primary/70">
                        <span class="material-symbols-outlined">search</span>
                    </div>
                    <input class="w-full h-11 bg-slate-800/50 border border-white/10 rounded-full pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary/50 focus:outline-none placeholder:text-slate-500 transition-all" placeholder="Buscar títulos..." type="text"/>
                </label>
            </div>
            @endunless

            <div class="flex items-center gap-4 shrink-0">
                <a href="#" class="px-5 py-2 text-sm font-semibold text-slate-300 hover:text-primary transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined">library_books</span>
                    Biblioteca
                </a>
                
                <div class="h-6 w-[1px] bg-white/10"></div>
                
                {{-- Auth Check --}}
                @if(request()->is('dashboard'))
                    {{-- User Logged In Interface --}}
                    {{-- Notifications --}}
                    <button class="size-10 rounded-full hover:bg-white/5 flex items-center justify-center text-slate-300 hover:text-white transition-colors relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-2 size-2 bg-primary rounded-full border border-slate-900"></span>
                    </button>

                    {{-- User Dropdown --}}
                    <div class="relative group">
                        <button class="flex items-center gap-3 pl-2 pr-1 py-1 rounded-full hover:bg-white/5 transition-colors">
                            <div class="text-right hidden lg:block">
                                <p class="text-xs font-bold text-white">Kirito Kazuto</p>
                            </div>
                            <div class="size-9 rounded-full bg-slate-700 border-2 border-primary/50 overflow-hidden relative">
                                <img src="https://ui-avatars.com/api/?name=Kirito+Kazuto&background=ec136d&color=fff" alt="User" class="w-full h-full object-cover">
                            </div>
                            <span class="material-symbols-outlined text-slate-400">expand_more</span>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div class="absolute right-0 top-full mt-2 w-48 bg-slate-900 border border-white/10 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all transform origin-top-right z-50 overflow-hidden">
                            <div class="p-2 space-y-1">
                                <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-lg">person</span>
                                    Perfil
                                </a>
                                <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-lg">settings</span>
                                    Ajustes
                                </a>
                                <div class="h-[1px] bg-white/10 my-1"></div>
                                <a href="/" class="flex items-center gap-2 px-3 py-2 text-sm text-red-400 hover:bg-red-500/10 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-lg">logout</span>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Guest Interface --}}
                    <a href="/login" class="px-5 py-2 text-sm font-semibold text-slate-300 hover:text-white transition-colors rounded-lg hover:bg-white/5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">login</span>
                        Iniciar Sesión
                    </a>
                    <a href="/register" class="px-5 py-2 text-sm font-bold bg-primary hover:bg-primary/90 text-white rounded-lg transition-all sakura-glow flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">person_add</span>
                        Registrarse
                    </a>
                @endif
            </div>
        </div>
    </nav>

    {{-- Mobile Navigation (Top) --}}
    <nav class="md:hidden fixed top-0 left-0 right-0 z-50 glass px-4 py-3 flex items-center justify-between border-b border-white/5">
        <div class="flex items-center gap-2">
            <div class="size-8 bg-primary rounded-lg flex items-center justify-center text-white shadow-lg">
                <span class="material-symbols-outlined text-lg">psychology_alt</span>
            </div>
            <h1 class="text-lg font-bold tracking-tight bg-gradient-to-r from-primary to-indigo-400 bg-clip-text text-transparent">NovelAsia</h1>
        </div>
        
        @if(request()->is('dashboard'))
            <div class="flex items-center gap-3">
                <button class="text-slate-300 hover:text-white">
                    <span class="material-symbols-outlined">notifications</span>
                </button>
                <div class="size-8 rounded-full bg-slate-700 border border-primary/50 overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=Kirito+Kazuto&background=ec136d&color=fff" alt="User" class="w-full h-full object-cover">
                </div>
            </div>
        @else
             <div class="flex items-center gap-2">
                <a href="/login" class="text-slate-300 hover:text-white p-2">
                    <span class="material-symbols-outlined">login</span>
                </a>
            </div>
        @endif
    </nav>

    {{-- Mobile Bottom Navigation (Only for Dashboard) --}}
    @if(request()->is('dashboard'))
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 glass px-6 py-4 flex items-center justify-between pb-6 border-t border-white/10">
        <button class="flex flex-col items-center gap-1 text-primary">
            <span class="material-symbols-outlined fill-current">home</span>
            <span class="text-[10px] font-bold">Inicio</span>
        </button>
        <button class="flex flex-col items-center gap-1 text-slate-400 hover:text-white transition-colors">
            <span class="material-symbols-outlined">library_books</span>
            <span class="text-[10px] font-bold">Biblioteca</span>
        </button>
        <button class="size-14 -mt-10 bg-primary text-white rounded-full flex items-center justify-center sakura-glow shadow-xl border-4 border-slate-900 active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-2xl">search</span>
        </button>
        <button class="flex flex-col items-center gap-1 text-slate-400 hover:text-white transition-colors">
            <span class="material-symbols-outlined">auto_awesome</span>
            <span class="text-[10px] font-bold">Explorar</span>
        </button>
        <button class="flex flex-col items-center gap-1 text-slate-400 hover:text-white transition-colors">
            <div class="size-6 rounded-full overflow-hidden border border-slate-400">
                 <img src="https://ui-avatars.com/api/?name=Kirito+Kazuto&background=ec136d&color=fff" alt="User" class="w-full h-full object-cover">
            </div>
            <span class="text-[10px] font-bold">Perfil</span>
        </button>
    </nav>
    @endif
