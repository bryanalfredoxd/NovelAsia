@extends('layouts.app')

@section('content')
<main class="app-container pt-20 pb-20 md:pb-12">
    
    {{-- Búsqueda Móvil: Usando variables de superficie --}}
    <div class="md:hidden mb-6 mt-4">
        <label class="relative flex items-center w-full group">
            <div class="absolute left-4 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] transition-colors">
                <span class="material-symbols-outlined">search</span>
            </div>
            <input 
                class="w-full h-12 bg-[var(--color-surface)] rounded-xl pl-12 pr-4 text-sm text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:outline-none border border-[var(--color-border)] shadow-sm transition-all" 
                placeholder="Buscar novelas en NovelAsia..." 
                type="text"
            />
        </label>
    </div>

    {{-- Hero Section: Mejorado con gradientes dinámicos --}}
    <section class="relative h-[400px] md:h-[480px] rounded-3xl overflow-hidden mb-12 group shadow-lg border border-[var(--color-border)]">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-105" 
             style='background-image: url("https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568&auto=format&fit=crop");'>
        </div>
        
        {{-- Overlay: Usamos un degradado que se adapta al fondo oscuro --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[var(--color-background)] via-[var(--color-background)]/40 to-transparent"></div>

        <div class="relative h-full flex flex-col justify-end p-6 md:p-12 lg:p-16 max-w-3xl">
            <div class="flex gap-2 mb-4">
                <span class="px-3 py-1 bg-[var(--color-primary)] text-white rounded-full text-[10px] font-bold uppercase tracking-widest">
                    Destacado
                </span>
                <span class="px-3 py-1 bg-[var(--color-surface)] text-[var(--color-primary)] border border-[var(--color-primary)]/20 rounded-full text-[10px] font-bold uppercase tracking-widest">
                    Fantasía
                </span>
            </div>
            
            <h2 class="text-3xl md:text-5xl font-bold text-[var(--color-reading-text)] mb-4 leading-tight">
                El Alquimista Reencarnado
            </h2>
            
            <p class="text-[var(--color-muted-text)] text-sm md:text-base mb-8 line-clamp-2 md:line-clamp-3">
                Un alquimista genio renace en un mundo donde la magia está muriendo. Con conocimientos prohibidos de su vida pasada, debe restaurar el equilibrio.
            </p>
            
            <div class="flex items-center gap-3">
                <button class="bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white px-8 py-3 rounded-xl font-bold text-sm transition-all active:scale-95 flex items-center gap-2 shadow-md">
                    <span class="material-symbols-outlined">auto_stories</span>
                    Leer Ahora
                </button>
                <button class="p-3 rounded-xl bg-[var(--color-surface)] border border-[var(--color-border)] text-[var(--color-reading-text)] hover:bg-[var(--color-background)] transition-all">
                    <span class="material-symbols-outlined text-xl">bookmark</span>
                </button>
            </div>
        </div>
    </section>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
        
        {{-- Columna Principal --}}
        <div class="flex-1">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl md:text-2xl font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-[var(--color-primary)]">local_fire_department</span>
                    Actualizaciones
                </h3>
                <a class="text-[var(--color-primary)] font-bold text-sm flex items-center gap-1" href="#">
                    Ver Todo <span class="material-symbols-outlined text-sm">chevron_right</span>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                {{-- Novel Card --}}
                @for ($i = 1; $i <= 4; $i++)
                <div class="novel-card group cursor-pointer border-none shadow-none bg-transparent">
                    <div class="relative aspect-[2/3] rounded-2xl overflow-hidden mb-3 shadow-md">
                        <div class="absolute top-2 left-2 z-10 bg-[var(--color-surface)]/90 backdrop-blur-md border border-[var(--color-border)] rounded-lg px-2 py-1 text-[9px] font-bold text-[var(--color-reading-text)]">
                            CAP. 245
                        </div>
                        <img src="https://images.unsplash.com/photo-1614726365723-49faaa564763?q=80&w=1887&auto=format&fit=crop" 
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <h4 class="font-bold text-sm text-[var(--color-reading-text)] group-hover:text-[var(--color-primary)] transition-colors truncate">Solo Swordmaster</h4>
                    <p class="text-[var(--color-muted-text)] text-[11px] mt-1 uppercase font-semibold">Acción • Hace 2h</p>
                </div>
                @endfor
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="w-full lg:w-80 shrink-0">
            <div class="lg:sticky lg:top-24 space-y-8">
                
                <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[var(--color-accent)]">star</span>
                        Top Semanal
                    </h3>
                    
                    <div class="space-y-5">
                        @foreach([1,2,3] as $rank)
                        <div class="flex items-center gap-4 group cursor-pointer">
                            <span class="text-2xl font-black text-[var(--color-border)] group-hover:text-[var(--color-primary)] transition-colors">{{ $rank }}</span>
                            <div class="size-12 rounded-lg bg-gray-200 overflow-hidden shrink-0">
                                <img src="https://images.unsplash.com/photo-1542259681-dadcd731560b?q=80&w=100" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-sm text-[var(--color-reading-text)] truncate">Título de la Novela</h4>
                                <span class="text-[10px] text-[var(--color-muted-text)] uppercase tracking-wider">4.8 ★ Fantasía</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Card Premium: Usando el color de acento --}}
                <div class="bg-[var(--color-primary)] rounded-2xl p-6 text-white relative overflow-hidden shadow-lg">
                    <div class="relative z-10">
                        <span class="material-symbols-outlined mb-2 text-3xl text-[var(--color-accent)]">workspace_premium</span>
                        <h5 class="font-bold text-lg mb-1">Pase VIP NovelAsia</h5>
                        <p class="text-white/80 text-xs mb-4 leading-relaxed">Capítulos adelantados y lectura sin publicidad.</p>
                        <button class="w-full py-2 bg-white text-[var(--color-primary)] rounded-lg text-xs font-bold hover:bg-[var(--color-accent)] hover:text-white transition-colors">
                            Suscribirse
                        </button>
                    </div>
                    {{-- Decoración sutil --}}
                    <div class="absolute -bottom-4 -right-4 size-24 bg-white/10 rounded-full blur-2xl"></div>
                </div>

            </div>
        </aside>
    </div>
</main>

{{-- Navegación Móvil (Web/App Style) --}}
<nav class="mobile-nav">
    <a href="#" class="flex flex-col items-center text-[var(--color-primary)]">
        <span class="material-symbols-outlined">home</span>
        <span class="text-[10px] font-bold">Inicio</span>
    </a>
    <a href="#" class="flex flex-col items-center text-[var(--color-muted-text)]">
        <span class="material-symbols-outlined">auto_stories</span>
        <span class="text-[10px] font-bold">Biblioteca</span>
    </a>
    <a href="#" class="flex flex-col items-center text-[var(--color-muted-text)]">
        <span class="material-symbols-outlined">explore</span>
        <span class="text-[10px] font-bold">Géneros</span>
    </a>
    <a href="#" class="flex flex-col items-center text-[var(--color-muted-text)]">
        <span class="material-symbols-outlined">person</span>
        <span class="text-[10px] font-bold">Perfil</span>
    </a>
</nav>
@endsection