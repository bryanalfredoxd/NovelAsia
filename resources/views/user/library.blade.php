@extends('layouts.app')

@section('title', 'Mi Biblioteca - NovelAsia')

@section('content')
{{-- Fondo dinámico --}}
<div class="fixed inset-0 bg-[var(--color-background)] -z-10 transition-colors duration-300"></div>

<main class="app-container pt-20 pb-12 flex flex-col lg:flex-row gap-8">
    
    <aside class="w-full lg:w-64 shrink-0">
        <div class="lg:sticky lg:top-24 space-y-6">
            <div class="bg-[var(--color-surface)] rounded-[2rem] border border-[var(--color-border)] p-6 shadow-sm">
                <h3 class="mb-4 text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-muted-text)]">Mi Colección</h3>
                <ul class="flex flex-col gap-2">
                    <li>
                        <a href="#" class="flex items-center gap-3 rounded-xl bg-[var(--color-primary)]/10 px-4 py-2.5 text-[var(--color-primary)] font-bold transition-all">
                            <span class="material-symbols-outlined text-xl">library_books</span>
                            <span class="text-sm">Todo</span>
                            <span class="ml-auto text-xs opacity-60">128</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-[var(--color-muted-text)] hover:bg-[var(--color-background)] transition-all">
                            <span class="material-symbols-outlined text-xl">pending_actions</span>
                            <span class="text-sm">Leyendo</span> {{-- Tipo 'leyendo' en BD --}}
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-[var(--color-muted-text)] hover:bg-[var(--color-background)] transition-all">
                            <span class="material-symbols-outlined text-xl">favorite</span>
                            <span class="text-sm">Favoritos</span> {{-- Tipo 'favoritos' en BD --}}
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-[var(--color-muted-text)] hover:bg-[var(--color-background)] transition-all">
                            <span class="material-symbols-outlined text-xl">verified</span>
                            <span class="text-sm">Terminados</span> {{-- Tipo 'leido' en BD --}}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Card de Suscripción (Tabla 'planes_suscripcion') --}}
            <div class="bg-gradient-to-br from-[var(--color-accent)] to-[#b8950b] rounded-[2rem] p-6 text-[var(--color-primary-dark)] shadow-lg relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-[10px] font-bold uppercase tracking-widest mb-1 opacity-80">Membresía</p>
                    <p class="text-sm font-bold leading-tight mb-4">Pásate a Premium para lectura offline.</p>
                    <button class="w-full rounded-xl bg-[var(--color-surface)] py-2.5 text-xs font-bold uppercase tracking-tighter hover:scale-105 transition-transform">Mejorar Ahora</button>
                </div>
                <span class="absolute -right-4 -bottom-4 material-symbols-outlined text-7xl opacity-20">diamond</span>
            </div>
        </div>
    </aside>

    <div class="flex-1 space-y-12">
        
        <section>
            <div class="flex items-center justify-between mb-6 px-2">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[var(--color-primary)]">history</span>
                    <h2 class="text-2xl font-bold text-[var(--color-reading-text)]">Continuar Leyendo</h2>
                </div>
                <a href="#" class="text-xs font-bold text-[var(--color-primary)] uppercase tracking-widest hover:underline">Ver Historial</a>
            </div>
            
            <div class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide">
                {{-- Card de lectura reciente --}}
                @for ($i = 1; $i <= 3; $i++)
                <div class="flex min-w-[320px] md:min-w-[360px] gap-4 rounded-[2rem] bg-[var(--color-surface)] p-4 border border-[var(--color-border)] shadow-sm hover:shadow-md transition-all group cursor-pointer">
                    <div class="h-32 w-24 shrink-0 overflow-hidden rounded-2xl shadow-sm border border-[var(--color-border)]">
                        <img class="h-full w-full object-cover transition-transform group-hover:scale-110" src="https://images.unsplash.com/photo-1614726365723-49faaa564763?q=80&w=200" alt="Cover"/>
                    </div>
                    <div class="flex flex-1 flex-col justify-between py-1 min-w-0">
                        <div>
                            <h3 class="font-bold text-[var(--color-reading-text)] leading-tight truncate">The Scholar's Path</h3>
                            <p class="text-[10px] font-bold text-[var(--color-muted-text)] mt-1 uppercase">Capítulo 142 / 500</p>
                        </div>
                        <div class="space-y-3">
                            {{-- Barra de progreso (Campo 'progreso' en tabla lecturas_usuario) --}}
                            <div class="w-full h-1.5 bg-[var(--color-background)] rounded-full overflow-hidden">
                                <div class="bg-[var(--color-accent)] h-full rounded-full" style="width: 45%"></div>
                            </div>
                            <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-[var(--color-primary)] px-4 py-2 text-[10px] font-bold text-white hover:bg-[var(--color-primary-dark)] transition-all">
                                <span class="material-symbols-outlined text-sm">play_arrow</span>
                                REANUDAR LECTURA
                            </button>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </section>

        <section>
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 px-2">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[var(--color-primary)]">collections_bookmark</span>
                    <h2 class="text-2xl font-bold text-[var(--color-reading-text)]">Mi Colección <span class="text-sm font-normal text-[var(--color-muted-text)] ml-2">(128)</span></h2>
                </div>
                
                <div class="flex items-center gap-3">
                    <select class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] py-2 pl-3 pr-8 text-xs font-bold text-[var(--color-muted-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 outline-none">
                        <option>Recientes</option>
                        <option>A-Z</option>
                        <option>Última Actualización</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-6">
                {{-- Novela Card --}}
                @for ($i = 1; $i <= 9; $i++)
                <div class="group flex flex-col gap-3">
                    <div class="relative aspect-[2/3] w-full overflow-hidden rounded-[2rem] shadow-sm border border-[var(--color-border)] bg-[var(--color-surface)]">
                        <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" src="https://images.unsplash.com/photo-1614726365723-49faaa564763?q=80&w=300" alt="Cover"/>
                        
                        {{-- Badge de actualización (Tabla 'capitulos') --}}
                        <div class="absolute top-3 right-3 bg-[var(--color-accent)] text-white text-[8px] font-black uppercase px-2 py-1 rounded-lg shadow-md">Nuevo</div>
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-[var(--color-primary-dark)]/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-4">
                            <button class="w-full rounded-xl bg-white py-2 text-[10px] font-bold text-[var(--color-primary)] uppercase">Leer Ahora</button>
                        </div>
                    </div>
                    <div class="px-1">
                        <h4 class="font-bold text-[var(--color-reading-text)] text-sm line-clamp-1 group-hover:text-[var(--color-primary)] transition-colors">Shadow Monarch</h4>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-[10px] font-bold text-[var(--color-muted-text)] uppercase tracking-tighter">Wu Kong</span>
                            <span class="flex items-center text-[10px] text-[var(--color-accent)] font-bold">
                                <span class="material-symbols-outlined text-xs !font-bold">star</span> 4.9
                            </span>
                        </div>
                    </div>
                </div>
                @endfor

                <div class="flex aspect-[2/3] flex-col items-center justify-center rounded-[2rem] border-2 border-dashed border-[var(--color-border)] hover:border-[var(--color-primary)] hover:bg-[var(--color-primary)]/5 transition-all cursor-pointer text-[var(--color-muted-text)] hover:text-[var(--color-primary)] group">
                    <span class="material-symbols-outlined text-4xl mb-2 transition-transform group-hover:scale-110">add_circle</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Añadir Novela</span>
                </div>
            </div>

            <div class="mt-16 flex items-center justify-center">
                <button class="flex items-center gap-2 rounded-xl bg-[var(--color-surface)] border border-[var(--color-border)] px-10 py-3 text-xs font-bold text-[var(--color-primary)] shadow-sm hover:bg-[var(--color-background)] transition-all uppercase tracking-widest">
                    <span class="material-symbols-outlined text-sm">expand_more</span>
                    Cargar más de mi colección
                </button>
            </div>
        </section>
    </div>
</main>
@endsection