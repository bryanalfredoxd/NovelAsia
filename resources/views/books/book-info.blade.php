@extends('layouts.app')

@section('title', 'Vista Previa - NovelAsia')

@section('content')
<div class="fixed inset-0 bg-[var(--color-background)] -z-10 transition-colors duration-300"></div>

<main class="app-container pt-24 pb-12">
    {{-- Breadcrumbs Estáticos --}}
    <nav class="flex mb-6 text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)]">
        <a href="#" class="hover:text-[var(--color-primary)]">Inicio</a>
        <span class="mx-2 text-[var(--color-border)]">/</span>
        <span class="text-[var(--color-primary)] line-clamp-1">The Celestial Journey: Gate of Nine Heavens</span>
    </nav>

    {{-- Hero Section --}}
    <section class="bg-[var(--color-surface)] rounded-[2.5rem] border border-[var(--color-border)] shadow-sm overflow-hidden mb-8">
        <div class="relative p-6 md:p-10 flex flex-col md:flex-row gap-8 items-start">
            
            {{-- Portada --}}
            <div class="w-full md:w-64 shrink-0 aspect-[2/3] rounded-2xl shadow-xl overflow-hidden border border-[var(--color-border)] group relative">
                <img 
                    src="https://images.unsplash.com/photo-1614726365723-49faaa564763?q=80&w=1887&auto=format&fit=crop" 
                    alt="Portada de ejemplo"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                    <span class="text-white text-xs font-bold uppercase tracking-tighter">Ampliar portada</span>
                </div>
            </div>

            <div class="flex-1 space-y-4">
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-0.5 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)] text-[10px] font-bold uppercase tracking-wider border border-[var(--color-primary)]/20">Verificado</span>
                        <span class="px-2 py-0.5 rounded-lg bg-[var(--color-accent)]/10 text-[var(--color-accent)] text-[10px] font-bold uppercase tracking-wider border border-[var(--color-accent)]/20">
                            En Progreso
                        </span>
                    </div>
                    
                    <h2 class="text-3xl md:text-5xl font-bold text-[var(--color-reading-text)] leading-tight">
                        The Celestial Journey: Gate of Nine Heavens
                    </h2>
                    
                    <div class="flex items-center gap-4 text-[var(--color-muted-text)] font-semibold text-sm">
                        <span class="flex items-center gap-1 hover:text-[var(--color-primary)] cursor-pointer">
                            <span class="material-symbols-outlined text-lg">person</span> 
                            Master Zen
                        </span>
                        <span class="size-1 rounded-full bg-[var(--color-border)]"></span>
                        <span class="flex items-center gap-1 text-[var(--color-accent)]">
                            <span class="material-symbols-outlined text-lg fill-1">star</span> 
                            4.8
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="bg-[var(--color-background)] border border-[var(--color-border)] px-3 py-1 rounded-full text-xs font-bold text-[var(--color-muted-text)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] transition-all cursor-default">Xianxia</span>
                    <span class="bg-[var(--color-background)] border border-[var(--color-border)] px-3 py-1 rounded-full text-xs font-bold text-[var(--color-muted-text)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] transition-all cursor-default">Acción</span>
                    <span class="bg-[var(--color-background)] border border-[var(--color-border)] px-3 py-1 rounded-full text-xs font-bold text-[var(--color-muted-text)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] transition-all cursor-default">Fantasía</span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 py-4 border-y border-[var(--color-border)]">
                    <div>
                        <p class="text-[10px] text-[var(--color-muted-text)] uppercase font-bold tracking-widest mb-1">Vistas</p>
                        <p class="text-xl font-bold">542.8K</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-[var(--color-muted-text)] uppercase font-bold tracking-widest mb-1">Favoritos</p>
                        <p class="text-xl font-bold">89.2K</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-[var(--color-muted-text)] uppercase font-bold tracking-widest mb-1">Capítulos</p>
                        <p class="text-xl font-bold">542</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-[var(--color-muted-text)] uppercase font-bold tracking-widest mb-1">Origen</p>
                        <p class="text-xl font-bold">China</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="{{ route('chapter.read') }}" class="flex items-center gap-2 px-8 py-3 bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white font-bold rounded-xl transition-all shadow-lg shadow-[var(--color-primary)]/20 transform active:scale-95">
                        <span class="material-symbols-outlined">auto_stories</span>
                        Comenzar Lectura
                    </a>
                    <button class="flex items-center gap-2 px-8 py-3 border-2 border-[var(--color-primary)] text-[var(--color-primary)] hover:bg-[var(--color-primary)]/5 font-bold rounded-xl transition-all transform active:scale-95">
                        <span class="material-symbols-outlined">library_add</span>
                        Biblioteca
                    </button>
                    <button class="p-3 border border-[var(--color-border)] rounded-xl text-[var(--color-muted-text)] hover:text-[var(--color-primary)] hover:bg-[var(--color-background)] transition-all">
                        <span class="material-symbols-outlined">share</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Cuerpo Principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-8 space-y-8">
            <div x-data="{ tab: 'synopsis' }" class="bg-[var(--color-surface)] rounded-[2rem] border border-[var(--color-border)] overflow-hidden shadow-sm">
                <div class="flex border-b border-[var(--color-border)] bg-[var(--color-background)]/50">
                    <button @click="tab = 'synopsis'" :class="tab === 'synopsis' ? 'text-[var(--color-primary)] border-b-2 border-[var(--color-primary)] bg-[var(--color-surface)]' : 'text-[var(--color-muted-text)]'" class="flex-1 px-6 py-4 font-bold text-sm transition-all uppercase tracking-tighter">Sinopsis</button>
                    <button @click="tab = 'chapters'" :class="tab === 'chapters' ? 'text-[var(--color-primary)] border-b-2 border-[var(--color-primary)] bg-[var(--color-surface)]' : 'text-[var(--color-muted-text)]'" class="flex-1 px-6 py-4 font-bold text-sm transition-all uppercase tracking-tighter">Capítulos (542)</button>
                </div>

                <div class="p-6 md:p-8">
                    <div x-show="tab === 'synopsis'" class="animate-fade-in">
                        <div class="font-novel text-lg leading-[1.8] text-[var(--color-reading-text)] space-y-4">
                            En un mundo donde los cielos están divididos por nueve puertas, un joven huérfano descubre un pergamino antiguo que desafía las leyes del cultivo. Mientras las sectas chocan por el dominio y los antiguos dioses despiertan, debe elegir entre el camino de un demonio o el ascenso de un dios.
                        </div>
                    </div>

                    <div x-show="tab === 'chapters'" class="animate-fade-in space-y-2">
                        @for ($i = 542; $i >= 539; $i--)
                        <div class="flex items-center justify-between p-4 rounded-xl hover:bg-[var(--color-background)] border border-transparent hover:border-[var(--color-border)] transition-all cursor-pointer group">
                            <div class="flex items-center gap-4">
                                <span class="text-[var(--color-muted-text)] text-xs font-bold w-12 italic">#{{ $i }}</span>
                                <span class="font-bold text-[var(--color-reading-text)] group-hover:text-[var(--color-primary)] transition-colors">Título del Capítulo {{ $i }}</span>
                            </div>
                            <span class="text-[10px] font-bold text-[var(--color-muted-text)] uppercase tracking-widest">Hace 2 horas</span>
                        </div>
                        @endfor
                        <button class="w-full mt-4 py-3 text-[var(--color-primary)] font-bold text-sm hover:underline">Ver todos los capítulos</button>
                    </div>
                </div>
            </div>

            {{-- Reviews Estáticos --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-xl font-bold text-[var(--color-reading-text)]">Opiniones de la Comunidad</h3>
                    <button class="text-[var(--color-primary)] text-xs font-bold bg-[var(--color-primary)]/10 px-4 py-2 rounded-xl hover:bg-[var(--color-primary)]/20 transition-all border border-[var(--color-primary)]/20 uppercase tracking-widest">Escribir Reseña</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-[var(--color-surface)] p-5 rounded-3xl border border-[var(--color-border)] space-y-3 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 font-bold text-sm text-[var(--color-reading-text)]">
                                <span class="material-symbols-outlined text-[var(--color-primary)]">account_circle</span>
                                <span>Lector_Cultivador</span>
                            </div>
                            <span class="text-[var(--color-accent)] font-bold text-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm fill-1">star</span> 5.0
                            </span>
                        </div>
                        <p class="text-xs text-[var(--color-muted-text)] leading-relaxed italic line-clamp-3">"La traducción automática al español es sorprendentemente fluida. El sistema de cultivo se entiende perfectamente."</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Estático --}}
        <aside class="lg:col-span-4 space-y-8">
            <div class="bg-[var(--color-surface)] p-6 rounded-[2rem] border border-[var(--color-border)] space-y-4 shadow-sm">
                <h4 class="text-[10px] uppercase font-bold text-[var(--color-muted-text)] tracking-widest px-1">Acerca del Autor</h4>
                <div class="flex items-center gap-4">
                    <div class="size-14 rounded-2xl bg-[var(--color-background)] border border-[var(--color-border)] flex items-center justify-center text-[var(--color-primary)]">
                        <span class="material-symbols-outlined text-3xl">edit_square</span>
                    </div>
                    <div>
                        <a href="{{ route('author.info') }}" class="text-lg font-bold text-[var(--color-reading-text)]">Master Zen</a>
                        <p class="text-[10px] font-bold text-[var(--color-accent)] uppercase">Autor Verificado</p>
                    </div>
                </div>
                <button class="w-full py-2.5 border border-[var(--color-primary)] text-[var(--color-primary)] font-bold rounded-xl hover:bg-[var(--color-primary)] hover:text-white transition-all text-xs">Seguir Autor</button>
            </div>

            <div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-dark)] p-6 rounded-[2rem] text-white relative overflow-hidden shadow-lg">
                <div class="relative z-10">
                    <h4 class="text-xs uppercase font-bold text-white/80 tracking-widest flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-lg">workspace_premium</span> Pase de Batalla
                    </h4>
                    <p class="text-xs text-white/70 mb-4">Apoya la traducción y desbloquea 10 capítulos avanzados.</p>
                    <button class="w-full py-3 bg-[var(--color-accent)] hover:scale-105 text-[var(--color-primary-dark)] font-bold rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-xs uppercase tracking-tighter">
                        <span class="material-symbols-outlined text-lg">diamond</span> Desbloquear VIP
                    </button>
                </div>
                <div class="absolute -right-4 -bottom-4 text-white/10">
                    <span class="material-symbols-outlined text-[8rem]">military_tech</span>
                </div>
            </div>
        </aside>
    </div>
</main>
@endsection