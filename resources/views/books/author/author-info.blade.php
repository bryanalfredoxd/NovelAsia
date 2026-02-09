@extends('layouts.app')

@section('title', 'Perfil del Autor - NovelAsia')

@section('content')
{{-- Fondo dinámico --}}
<div class="fixed inset-0 bg-[var(--color-background)] -z-10 transition-colors duration-300"></div>

<main class="app-container pt-24 pb-12">
    <div class="bg-[var(--color-surface)] rounded-[2.5rem] shadow-sm border border-[var(--color-border)] p-6 md:p-10 mb-8 transition-colors duration-300">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            {{-- Avatar del Autor --}}
            <div class="relative">
                <div class="size-32 md:size-40 rounded-full border-4 border-[var(--color-background)] shadow-xl overflow-hidden bg-[var(--color-background)]">
                    <img class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name=Master+Inkwell&background=C0392B&color=fff&size=200" alt="Author Avatar"/>
                </div>
                {{-- Badge de Verificado (Campo 'esta_verificado' de la BD) --}}
                <div class="absolute bottom-2 right-2 bg-[var(--color-accent)] text-white size-9 rounded-full flex items-center justify-center border-4 border-[var(--color-surface)] shadow-md" title="Autor Verificado">
                    <span class="material-symbols-outlined text-lg !font-bold">verified</span>
                </div>
            </div>

            <div class="flex-1 text-center md:text-left space-y-4">
                <div>
                    <h1 class="text-3xl md:text-5xl font-bold text-[var(--color-reading-text)] mb-2">Master Inkwell</h1>
                    <div class="flex flex-wrap justify-center md:justify-start gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-[var(--color-primary)]/10 text-[var(--color-primary)] border border-[var(--color-primary)]/20 uppercase tracking-widest">
                            Escritor Profesional
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-[var(--color-accent)]/10 text-[var(--color-accent)] border border-[var(--color-accent)]/20 uppercase tracking-widest">
                            Origen: China {{-- Campo 'pais_origen' --}}
                        </span>
                    </div>
                </div>

                {{-- Biografía (Campo 'biografia' de la tabla autores_novelas) --}}
                <p class="text-[var(--color-muted-text)] text-lg italic leading-relaxed max-w-2xl font-novel">
                    "Forjando mundos de cultivo y leyendas ancestrales para el corazón moderno. Cada palabra es un paso en el camino al Dao."
                </p>

                <div class="flex flex-wrap justify-center md:justify-start gap-3 pt-2">
                    <button class="bg-[var(--color-primary)] text-white flex items-center gap-2 px-8 py-3 rounded-xl font-bold hover:bg-[var(--color-primary-dark)] transition-all shadow-lg shadow-[var(--color-primary)]/20 active:scale-95">
                        <span class="material-symbols-outlined text-sm">person_add</span> Seguir Autor
                    </button>
                    <button class="bg-[var(--color-surface)] text-[var(--color-primary)] border-2 border-[var(--color-primary)] flex items-center gap-2 px-6 py-3 rounded-xl font-bold hover:bg-[var(--color-primary)]/5 transition-all active:scale-95">
                        <span class="material-symbols-outlined text-sm">mail</span> Mensaje
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-10 pt-8 border-t border-[var(--color-border)]">
            <div class="text-center md:border-r border-[var(--color-border)]">
                <p class="text-[var(--color-primary)] text-3xl font-bold font-novel">14</p>
                <p class="text-[var(--color-muted-text)] text-[10px] uppercase tracking-widest font-bold">Obras Totales</p>
            </div>
            <div class="text-center md:border-r border-[var(--color-border)]">
                <p class="text-[var(--color-primary)] text-3xl font-bold font-novel">2.8M</p>
                <p class="text-[var(--color-muted-text)] text-[10px] uppercase tracking-widest font-bold">Palabras Escritas</p>
            </div>
            <div class="text-center md:border-r border-[var(--color-border)]">
                <p class="text-[var(--color-primary)] text-3xl font-bold font-novel">142K</p> {{-- Campo 'seguidores' --}}
                <p class="text-[var(--color-muted-text)] text-[10px] uppercase tracking-widest font-bold">Seguidores</p>
            </div>
            <div class="text-center">
                <p class="text-[var(--color-accent)] text-3xl font-bold font-novel">#12</p>
                <p class="text-[var(--color-muted-text)] text-[10px] uppercase tracking-widest font-bold">Ranking Autor</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <aside class="w-full lg:w-1/3 flex flex-col gap-8">
            <div class="bg-[var(--color-surface)] rounded-[2rem] border border-[var(--color-border)] p-8 shadow-sm">
                <div class="flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-[var(--color-primary)]">history_edu</span>
                    <h3 class="text-xl font-bold text-[var(--color-reading-text)]">Sobre el Autor</h3>
                </div>
                <div class="space-y-4 text-[var(--color-muted-text)] leading-relaxed text-sm">
                    <p>Especialista en Xianxia y Fantasía Épica con un enfoque en el crecimiento espiritual y profundidad filosófica. Escribiendo profesionalmente desde hace 5 años.</p>
                    <p>Miembro verificado desde: 15 de Octubre, 2023.</p>
                </div>
                
                <div class="mt-8 pt-8 border-t border-[var(--color-border)]">
                    <h4 class="text-[10px] font-bold uppercase text-[var(--color-muted-text)] tracking-widest mb-4">Redes Sociales</h4>
                    <div class="flex gap-3">
                        <a href="#" class="size-10 rounded-xl bg-[var(--color-background)] flex items-center justify-center text-[var(--color-muted-text)] hover:text-[var(--color-primary)] border border-[var(--color-border)] transition-all">
                            <span class="material-symbols-outlined">public</span>
                        </a>
                        <a href="#" class="size-10 rounded-xl bg-[var(--color-background)] flex items-center justify-center text-[var(--color-muted-text)] hover:text-[var(--color-primary)] border border-[var(--color-border)] transition-all">
                            <span class="material-symbols-outlined">forum</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Widget de Apoyo (Para monetización futura) --}}
            <div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-dark)] rounded-[2rem] p-8 text-white relative overflow-hidden shadow-lg shadow-[var(--color-primary)]/20">
                <div class="relative z-10">
                    <span class="material-symbols-outlined text-4xl text-[var(--color-accent)] mb-4">military_tech</span>
                    <h4 class="text-xl font-bold mb-2">Apoyar al Autor</h4>
                    <p class="text-white/70 text-xs leading-relaxed mb-6">Tus donaciones ayudan a que las traducciones sean más rápidas y de mejor calidad.</p>
                    <button class="w-full py-3 bg-[var(--color-accent)] text-[var(--color-primary-dark)] font-bold rounded-xl hover:scale-105 transition-transform flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                        <span class="material-symbols-outlined">diamond</span> Enviar Piedras
                    </button>
                </div>
                <div class="absolute -right-6 -bottom-6 text-white/10">
                    <span class="material-symbols-outlined text-[10rem]">auto_awesome</span>
                </div>
            </div>
        </aside>

        <div class="w-full lg:w-2/3">
            <div x-data="{ tab: 'works' }" class="bg-[var(--color-surface)] rounded-[2.5rem] border border-[var(--color-border)] overflow-hidden shadow-sm">
                {{-- Tabs Estilo App --}}
                <div class="flex border-b border-[var(--color-border)] bg-[var(--color-background)]/30">
                    <button @click="tab = 'works'" :class="tab === 'works' ? 'text-[var(--color-primary)] border-b-2 border-[var(--color-primary)] bg-[var(--color-surface)]' : 'text-[var(--color-muted-text)]'" class="flex-1 py-5 text-xs font-bold uppercase tracking-widest transition-all">Obras Publicadas</button>
                    <button @click="tab = 'news'" :class="tab === 'news' ? 'text-[var(--color-primary)] border-b-2 border-[var(--color-primary)] bg-[var(--color-surface)]' : 'text-[var(--color-muted-text)]'" class="flex-1 py-5 text-xs font-bold uppercase tracking-widest transition-all">Anuncios</button>
                </div>

                <div class="p-6 md:p-8">
                    <div x-show="tab === 'works'" class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in">
                        @for ($i = 1; $i <= 4; $i++)
                        <div class="flex gap-4 p-4 rounded-3xl hover:bg-[var(--color-background)] border border-transparent hover:border-[var(--color-border)] transition-all group cursor-pointer">
                            <div class="w-24 h-32 flex-shrink-0 rounded-2xl shadow-md overflow-hidden relative border border-[var(--color-border)]">
                                <img class="w-full h-full object-cover transition-transform group-hover:scale-110" src="https://images.unsplash.com/photo-1614726365723-49faaa564763?q=80&w=200" alt="Cover"/>
                                <div class="absolute top-0 right-0 bg-[var(--color-primary)] text-white text-[8px] px-2 py-0.5 font-bold uppercase tracking-tighter rounded-bl-lg">TOP</div>
                            </div>
                            <div class="flex flex-col justify-between py-1 min-w-0">
                                <div class="space-y-1">
                                    <h4 class="font-bold text-[var(--color-reading-text)] group-hover:text-[var(--color-primary)] transition-colors truncate">Título de la Novela {{ $i }}</h4>
                                    <div class="flex items-center gap-1 text-[var(--color-accent)]">
                                        <span class="material-symbols-outlined text-sm !font-bold">star</span>
                                        <span class="text-xs font-bold">4.9</span>
                                        <span class="text-[10px] text-[var(--color-muted-text)] ml-1">(12.4k votos)</span>
                                    </div>
                                    <p class="text-[10px] text-[var(--color-muted-text)] line-clamp-2 leading-relaxed">
                                        Una breve descripción de la historia que cautivará al lector...
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] bg-[var(--color-primary)]/10 text-[var(--color-primary)] px-2 py-0.5 rounded-lg font-bold uppercase">Xianxia</span>
                                    <span class="text-[9px] text-[var(--color-muted-text)] font-bold">1.2M Palabras</span>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>

                    {{-- Pestaña Anuncios (Basado en la lógica de comunidad de la BD) --}}
                    <div x-show="tab === 'news'" class="space-y-4 animate-fade-in" style="display: none;">
                        <div class="p-6 rounded-3xl bg-[var(--color-background)] border border-[var(--color-border)]">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-bold text-[var(--color-primary)]">¡Nuevo Arco Mañana!</h4>
                                <span class="text-[9px] font-bold text-[var(--color-muted-text)] uppercase tracking-widest">Hace 2 horas</span>
                            </div>
                            <p class="text-sm text-[var(--color-reading-text)] leading-relaxed">Prepárense para el arco 'Descenso Celestial'. Se revelará el secreto del noveno cielo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection