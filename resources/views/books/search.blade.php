@extends('layouts.app')

@section('title', 'Explorar Novelas - NovelAsia')

@section('content')
{{-- Fondo dinámico --}}
<div class="fixed inset-0 bg-[var(--color-background)] -z-10 transition-colors duration-300"></div>

<main class="app-container pt-24 pb-12 flex flex-col lg:flex-row gap-8">
    
    <aside class="w-full lg:w-72 shrink-0 space-y-6">
        <div class="bg-[var(--color-surface)] rounded-[2.5rem] border border-[var(--color-border)] p-6 shadow-sm sticky top-24">
            <div class="flex items-center justify-between mb-8 px-2">
                <h2 class="text-xl font-bold text-[var(--color-reading-text)] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[var(--color-primary)]">tune</span>
                    Filtros
                </h2>
                <button class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-muted-text)] hover:text-[var(--color-primary)] underline">Limpiar</button>
            </div>

            <div class="space-y-8 custom-scrollbar max-h-[70vh] overflow-y-auto px-2">
                
                <div>
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-muted-text)] mb-4">Géneros Principales</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Xianxia', 'Wuxia', 'Fantasía', 'Acción', 'Romance', 'Sci-Fi', 'Misterio'] as $genero)
                            <button class="px-3 py-1.5 text-xs font-bold rounded-xl border border-[var(--color-border)] bg-[var(--color-background)] text-[var(--color-muted-text)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] transition-all">
                                {{ $genero }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-muted-text)] mb-4">Estado de la Obra</h3>
                    <div class="space-y-3">
                        @foreach(['en_progreso' => 'En Progreso', 'completado' => 'Completado', 'pausado' => 'Pausado'] as $key => $label)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" value="{{ $key }}" class="size-5 rounded border-[var(--color-border)] bg-[var(--color-background)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/20 transition-all">
                                <span class="text-sm font-semibold text-[var(--color-muted-text)] group-hover:text-[var(--color-reading-text)]">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-muted-text)] mb-4">N° de Capítulos</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="caps" class="size-5 border-[var(--color-border)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/20">
                            <span class="text-sm font-semibold text-[var(--color-muted-text)]">0 - 100</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="caps" class="size-5 border-[var(--color-border)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/20">
                            <span class="text-sm font-semibold text-[var(--color-muted-text)]">100 - 500</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="caps" class="size-5 border-[var(--color-border)] text-[var(--color-primary)] focus:ring-[var(--color-primary)]/20">
                            <span class="text-sm font-semibold text-[var(--color-muted-text)]">500+ Capítulos</span>
                        </label>
                    </div>
                </div>

                <div>
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-muted-text)] mb-4">Valoración Mínima</h3>
                    <select class="w-full bg-[var(--color-background)] border border-[var(--color-border)] rounded-xl py-2 px-3 text-xs font-bold text-[var(--color-reading-text)] outline-none focus:border-[var(--color-primary)]">
                        <option>Cualquier estrella</option>
                        <option>4.5+ Estrellas</option>
                        <option>4.0+ Estrellas</option>
                        <option>3.0+ Estrellas</option>
                    </select>
                </div>
            </div>

            <button class="w-full mt-8 py-4 bg-[var(--color-primary)] text-white font-bold rounded-2xl shadow-lg shadow-[var(--color-primary)]/20 hover:bg-[var(--color-primary-dark)] transition-all active:scale-95 uppercase text-xs tracking-widest">
                Aplicar Filtros
            </button>
        </div>
    </aside>

    <section class="flex-1 min-w-0">
        
        <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4 px-2">
            <div>
                <p class="text-[var(--color-muted-text)] text-sm">Mostrando <span class="font-bold text-[var(--color-reading-text)]">1,240</span> novelas encontradas</p>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-muted-text)]">Ordenar por:</span>
                <select class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-xl py-2 pl-4 pr-10 text-xs font-bold text-[var(--color-reading-text)] outline-none focus:ring-2 focus:ring-[var(--color-primary)]/10">
                    <option>Popularidad (Vistas)</option> {{-- Campo total_vistas --}}
                    <option>Valoración</option> {{-- Campo promedio_calificacion --}}
                    <option>Recién Actualizado</option> {{-- Campo ultimo_scraping --}}
                    <option>Nuevas</option> {{-- Campo fecha_creacion --}}
                </select>
            </div>
        </div>

        <div class="space-y-4">
            @for ($i = 1; $i <= 5; $i++)
            <div class="bg-[var(--color-surface)] rounded-[2rem] p-4 md:p-6 border border-[var(--color-border)] shadow-sm hover:shadow-md hover:border-[var(--color-primary)]/30 transition-all group flex flex-col md:flex-row gap-6">
                
                <div class="w-full md:w-32 h-48 md:h-44 shrink-0 rounded-2xl overflow-hidden shadow-sm border border-[var(--color-border)] relative">
                    <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="https://images.unsplash.com/photo-1614726365723-49faaa564763?q=80&w=200" alt="Cover"/>
                    @if($i == 1)
                        <div class="absolute top-2 left-2 bg-[var(--color-accent)] text-white text-[8px] font-black uppercase px-2 py-0.5 rounded-lg shadow-md">TOP</div>
                    @endif
                </div>

                <div class="flex-1 flex flex-col justify-between py-1">
                    <div>
                        <div class="flex flex-col md:flex-row justify-between items-start mb-2 gap-2">
                            <h3 class="text-xl font-bold text-[var(--color-reading-text)] group-hover:text-[var(--color-primary)] transition-colors cursor-pointer line-clamp-1">Path to Eternity: The Peerless Cultivator</h3>
                            <div class="flex items-center gap-1 text-[var(--color-accent)] shrink-0">
                                <span class="material-symbols-outlined text-lg !font-bold">star</span>
                                <span class="text-sm font-bold">4.9</span>
                                <span class="text-[10px] text-[var(--color-muted-text)] font-normal ml-1">(2.1k votos)</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] font-bold uppercase tracking-widest text-[var(--color-muted-text)] mb-4">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">person</span> Ink_Master</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">list_alt</span> 842 Caps</span>
                            <span class="flex items-center gap-1 text-[var(--color-primary)]"><span class="material-symbols-outlined text-sm">update</span> Actualizado hoy</span>
                        </div>

                        {{-- Descripción (Campo 'descripcion_traducida') --}}
                        <p class="text-xs text-[var(--color-muted-text)] line-clamp-2 leading-relaxed mb-4 italic">
                            En un mundo donde los fuertes consumen a los débiles, un discípulo descartado encuentra un colgante de jade con el alma de un antiguo dios de la guerra...
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex gap-2">
                            <span class="px-2 py-1 bg-[var(--color-background)] text-[9px] font-bold uppercase rounded-lg border border-[var(--color-border)] text-[var(--color-muted-text)]">Xianxia</span>
                            <span class="px-2 py-1 bg-[var(--color-background)] text-[9px] font-bold uppercase rounded-lg border border-[var(--color-border)] text-[var(--color-muted-text)]">Acción</span>
                        </div>
                        <button class="flex items-center gap-2 text-xs font-bold text-[var(--color-primary)] uppercase tracking-widest hover:translate-x-1 transition-transform">
                            Leer Ahora <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>
            @endfor
        </div>

        <div class="mt-12 flex justify-center items-center gap-2">
            <button class="size-10 rounded-xl flex items-center justify-center border border-[var(--color-border)] text-[var(--color-muted-text)] hover:bg-[var(--color-surface)] transition-all">
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button class="size-10 rounded-xl flex items-center justify-center bg-[var(--color-primary)] text-white font-bold text-sm shadow-md">1</button>
            <button class="size-10 rounded-xl flex items-center justify-center border border-[var(--color-border)] text-[var(--color-muted-text)] hover:bg-[var(--color-surface)] transition-all font-bold text-sm">2</button>
            <button class="size-10 rounded-xl flex items-center justify-center border border-[var(--color-border)] text-[var(--color-muted-text)] hover:bg-[var(--color-surface)] transition-all font-bold text-sm">3</button>
            <span class="px-2 text-[var(--color-border)]">...</span>
            <button class="size-10 rounded-xl flex items-center justify-center border border-[var(--color-border)] text-[var(--color-muted-text)] hover:bg-[var(--color-surface)] transition-all">
                <span class="material-symbols-outlined">chevron_right</span>
            </button>
        </div>
    </section>
</main>
@endsection