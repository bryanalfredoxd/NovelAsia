@extends('layouts.app')

@section('title', 'Leyendo: Capítulo 124 - NovelAsia')

@section('content')
{{-- Barra de Progreso Superior (Refleja el campo 'progreso' de la tabla lecturas_usuario) --}}
<div class="fixed top-0 left-0 w-full h-1.5 bg-[var(--color-border)] z-[60]">
    <div class="h-full bg-[var(--color-primary)] transition-all duration-500" style="width: 45%;"></div>
</div>

{{-- Fondo dinámico --}}
<div class="fixed inset-0 bg-[var(--color-background)] -z-10 transition-colors duration-300"></div>

<main class="app-container pt-20 pb-24">
    
    {{-- Header del Lector (Datos de la tabla 'novelas' y 'capitulos') --}}
    <div class="max-w-[800px] mx-auto mb-12 text-center">
        <nav class="flex justify-center mb-6 text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-muted-text)]">
            <a href="#" class="hover:text-[var(--color-primary)]">The Great Cultivator</a>
            <span class="mx-2">/</span>
            <span class="text-[var(--color-primary)]">Capítulo 124</span>
        </nav>
        
        <h1 class="text-4xl md:text-6xl font-bold text-[var(--color-reading-text)] leading-tight mb-6">
            El Despertar de las Sombras
        </h1>

        <div class="flex items-center justify-center gap-6 text-[10px] font-bold uppercase tracking-widest text-[var(--color-muted-text)]">
            <span class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">schedule</span> 8 min de lectura
            </span>
            <span class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">visibility</span> 12.4k vistas {{-- Campo 'total_vistas' --}}
            </span>
        </div>
    </div>

    <div class="relative flex justify-center">
        
        {{-- Barra Lateral Izquierda: Navegación y Ajustes --}}
        <aside class="fixed left-8 top-40 hidden xl:flex flex-col gap-4 z-40">
            <div class="flex flex-col gap-2 p-2 bg-[var(--color-surface)] rounded-2xl shadow-xl border border-[var(--color-border)]">
                <button class="p-3 rounded-xl hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)] transition-all" title="Índice de capítulos">
                    <span class="material-symbols-outlined">list</span>
                </button>
                <button class="p-3 rounded-xl hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)] transition-all" title="Ajustes de lectura">
                    <span class="material-symbols-outlined">settings</span>
                </button>
                <div class="h-px bg-[var(--color-border)] mx-2 my-1"></div>
                <button onclick="toggleTheme()" class="p-3 rounded-xl hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)] transition-all">
                    <span class="material-symbols-outlined dark:hidden">dark_mode</span>
                    <span class="material-symbols-outlined hidden dark:block">light_mode</span>
                </button>
            </div>
        </aside>

        {{-- Área de Lectura Principal --}}
        {{-- Usamos 'font-novel' (Merriweather) para el contenido largo como definiste --}}
        <article class="w-full max-w-[850px] bg-[var(--color-surface)] px-6 md:px-16 py-16 rounded-[3rem] shadow-sm border border-[var(--color-border)] transition-colors duration-300">
            
            <div class="font-novel text-xl md:text-2xl leading-[2] text-[var(--color-reading-text)] space-y-10 selection:bg-[var(--color-primary)] selection:text-white">
                <p>
                    El aire en el paso de la montaña se había vuelto fino y quebradizo, como un pergamino viejo. Wei Long estaba al borde del precipicio, con la mano temblando ligeramente contra la empuñadura de su espada con incrustaciones de jade. Podía sentir el cambio en las venas espirituales bajo la tierra—un latido bajo y rítmico que recordaba el latir de un corazón olvidado por los cielos.
                </p>

                <p>
                    —Está sucediendo —susurró una voz desde las sombras. El Anciano Han salió a la pálida luz de la luna, con sus túnicas raídas y manchadas con la sangre plateada de los guardianes celestiales—. Los sellos de los Nueve Reinos se están debilitando. Lo que una vez estuvo encerrado en el Vacío está encontrando su camino de regreso al mundo mortal.
                </p>

                <div class="flex justify-center py-10">
                    <span class="material-symbols-outlined text-[var(--color-primary)]/30 text-4xl">flare</span>
                </div>

                <p>
                    Wei Long no respondió de inmediato. Observó cómo una niebla oscura comenzaba a enroscarse alrededor de la base de los picos sagrados, tragándose los pinos antiguos en un abrazo silencioso y sofocante. El despertar no fue una explosión estrepitosa, sino una marea de sombras silenciosa e inexorable.
                </p>

                <p>
                    En lo profundo de su propio dantian, el Núcleo del Fénix brilló con un calor repentino y violento. Era una advertencia. Durante trescientos años, la Secta del Sol Naciente había protegido el fragmento del alma del Emperador Sol, creyendo que era una bendición de luz eterna. Pero a medida que las sombras crecían, Wei Long se dio cuenta de la aterradora verdad.
                </p>

                <p>
                    La luz no existe para desterrar a la oscuridad; existe para definirla. Y cuanto más brillante ardía su núcleo, más largas se extendían las sombras a través del mundo.
                </p>
            </div>

            {{-- Navegación Inferior (Tabla 'capitulos') --}}
            <nav class="mt-20 pt-12 border-t border-[var(--color-border)] flex flex-col sm:flex-row gap-4 items-center justify-between">
                <button class="w-full sm:w-auto flex items-center justify-center gap-3 px-8 py-4 bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10 text-[var(--color-reading-text)] font-bold rounded-2xl transition-all group">
                    <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    Anterior
                </button>
                
                {{-- Botón para abrir comentarios (Tabla 'comentarios_capitulo') --}}
                <button class="flex items-center gap-2 p-4 text-[var(--color-muted-text)] hover:text-[var(--color-primary)] transition-colors">
                    <span class="material-symbols-outlined">chat_bubble</span>
                    <span class="text-xs font-bold uppercase tracking-widest">124 Comentarios</span>
                </button>

                <button class="w-full sm:w-auto flex items-center justify-center gap-3 px-10 py-4 bg-[var(--color-primary)] text-white font-bold rounded-2xl hover:bg-[var(--color-primary-dark)] shadow-lg shadow-[var(--color-primary)]/20 transition-all group">
                    Siguiente Capítulo
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </nav>
        </article>

        {{-- Barra Lateral Derecha: Interacción Social --}}
        <aside class="fixed right-8 top-40 hidden xl:flex flex-col gap-4 z-40">
            <div class="flex flex-col gap-6 p-4 bg-[var(--color-surface)] rounded-[2rem] shadow-xl border border-[var(--color-border)]">
                <div class="flex flex-col items-center gap-1 group">
                    <button class="p-3 rounded-2xl bg-[var(--color-background)] text-[var(--color-muted-text)] hover:text-[var(--color-primary)] transition-all">
                        <span class="material-symbols-outlined">favorite</span> {{-- tabla biblioteca_usuario --}}
                    </button>
                    <span class="text-[9px] font-black text-[var(--color-muted-text)] uppercase">Favorito</span>
                </div>
                <div class="flex flex-col items-center gap-1 group">
                    <button class="p-3 rounded-2xl bg-[var(--color-accent)]/10 text-[var(--color-accent)] hover:bg-[var(--color-accent)] hover:text-white transition-all">
                        <span class="material-symbols-outlined">thumb_up</span> {{-- tabla comentarios_capitulo / likes --}}
                    </button>
                    <span class="text-[9px] font-black text-[var(--color-muted-text)] uppercase">Votar</span>
                </div>
                <div class="h-px bg-[var(--color-border)] mx-2"></div>
                <button class="p-3 rounded-2xl text-[var(--color-muted-text)] hover:text-[var(--color-primary)] transition-all">
                    <span class="material-symbols-outlined">share</span>
                </button>
            </div>
        </aside>
    </div>
</main>

{{-- Mobile Reader Menu (Flotante para mejor UX en App) --}}
<div class="fixed bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-1 p-1.5 bg-[var(--color-surface)]/90 backdrop-blur-xl border border-[var(--color-border)] rounded-full shadow-2xl xl:hidden z-[100]">
    <button class="p-4 rounded-full text-[var(--color-muted-text)]"><span class="material-symbols-outlined">list</span></button>
    <button class="p-4 rounded-full text-[var(--color-muted-text)]"><span class="material-symbols-outlined">settings</span></button>
    <button class="p-5 bg-[var(--color-primary)] text-white rounded-full shadow-lg shadow-[var(--color-primary)]/40"><span class="material-symbols-outlined">bolt</span></button>
    <button class="p-4 rounded-full text-[var(--color-accent)]"><span class="material-symbols-outlined">star</span></button>
    <button class="p-4 rounded-full text-[var(--color-muted-text)]"><span class="material-symbols-outlined">bookmark</span></button>
</div>
@endsection