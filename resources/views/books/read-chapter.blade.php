{{-- resources/views/lectura/show.blade.php --}}

@extends('layouts.app')

@section('title', ($traduccion?->titulo_traducido ?? $capitulo->titulo_original) . ' - ' . $capitulo->novela->titulo)

@section('content')
{{-- Barra de Progreso Superior --}}
<div class="fixed top-0 left-0 w-full h-1.5 bg-[var(--color-border)] z-[60]" id="progress-bar-container">
    <div class="h-full bg-[var(--color-primary)] transition-all duration-500" id="progress-bar" style="width: 0%;"></div>
</div>

{{-- Fondo dinámico según tema --}}
<div class="fixed inset-0 bg-[var(--color-background)] -z-10 transition-colors duration-300"></div>

<main class="app-container pt-20 pb-24" 
      data-tamano-letra="{{ $preferencias['tamano_letra'] ?? 'normal' }}"
      data-tipo-letra="{{ $preferencias['tipo_letra'] ?? 'serif' }}"
      data-espaciado="{{ $preferencias['espaciado'] ?? 'normal' }}"
      data-tema="{{ $preferencias['tema'] ?? 'claro' }}">
    
    {{-- Header del Lector --}}
    <div class="max-w-[800px] mx-auto mb-12 text-center">
        <nav class="flex justify-center mb-6 text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-muted-text)]">
            <a href="{{ route('novelas.show', $capitulo->id_novela) }}" class="hover:text-[var(--color-primary)] transition-colors">
                {{ $capitulo->novela->titulo }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-[var(--color-primary)]">
                Capítulo {{ $capitulo->numero_capitulo }}
            </span>
        </nav>
        
        <h1 class="text-4xl md:text-6xl font-bold text-[var(--color-reading-text)] leading-tight mb-6" id="capitulo-titulo">
            {{ $traduccion?->titulo_traducido ?? $capitulo->titulo_original }}
        </h1>

        <div class="flex items-center justify-center gap-6 text-[10px] font-bold uppercase tracking-widest text-[var(--color-muted-text)]">
            <span class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">schedule</span> 
                {{ $capitulo->tiempoLectura() }} min de lectura
            </span>
            <span class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">visibility</span> 
                {{ number_format($capitulo->novela->total_vistas) }} vistas
            </span>
        </div>
    </div>

    <div class="relative flex justify-center">
        
        {{-- Barra Lateral Izquierda: Navegación y Ajustes --}}
        <aside class="fixed left-8 top-40 hidden xl:flex flex-col gap-4 z-40">
            <div class="flex flex-col gap-2 p-2 bg-[var(--color-surface)] rounded-2xl shadow-xl border border-[var(--color-border)]">
                <button onclick="toggleIndice()" class="p-3 rounded-xl hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)] transition-all" title="Índice de capítulos">
                    <span class="material-symbols-outlined">list</span>
                </button>
                <button onclick="toggleAjustes()" class="p-3 rounded-xl hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)] transition-all" title="Ajustes de lectura">
                    <span class="material-symbols-outlined">settings</span>
                </button>
                <div class="h-px bg-[var(--color-border)] mx-2 my-1"></div>
                <button onclick="toggleTheme()" class="p-3 rounded-xl hover:bg-[var(--color-primary)]/10 hover:text-[var(--color-primary)] transition-all">
                    <span class="material-symbols-outlined dark:hidden">dark_mode</span>
                    <span class="material-symbols-outlined hidden dark:block">light_mode</span>
                </button>
            </div>

            {{-- Panel de Ajustes (oculto por defecto) --}}
            <div id="panel-ajustes" class="hidden bg-[var(--color-surface)] rounded-2xl p-4 border border-[var(--color-border)] shadow-xl w-64">
                <h3 class="font-bold text-sm mb-3">Tamaño de letra</h3>
                <div class="flex gap-2 mb-4">
                    <button onclick="cambiarTamanoLetra('pequeno')" class="flex-1 px-3 py-2 text-xs rounded-lg bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10">A-</button>
                    <button onclick="cambiarTamanoLetra('normal')" class="flex-1 px-3 py-2 text-sm rounded-lg bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10">A</button>
                    <button onclick="cambiarTamanoLetra('grande')" class="flex-1 px-3 py-2 text-base rounded-lg bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10">A+</button>
                    <button onclick="cambiarTamanoLetra('muy_grande')" class="flex-1 px-3 py-2 text-lg rounded-lg bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10">A++</button>
                </div>

                <h3 class="font-bold text-sm mb-3">Espaciado</h3>
                <div class="flex gap-2 mb-4">
                    <button onclick="cambiarEspaciado('normal')" class="flex-1 px-3 py-2 text-xs rounded-lg bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10">Normal</button>
                    <button onclick="cambiarEspaciado('amplio')" class="flex-1 px-3 py-2 text-xs rounded-lg bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10">Amplio</button>
                    <button onclick="cambiarEspaciado('muy_amplio')" class="flex-1 px-3 py-2 text-xs rounded-lg bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10">Muy amplio</button>
                </div>

                <h3 class="font-bold text-sm mb-3">Tipo de letra</h3>
                <div class="flex gap-2">
                    <button onclick="cambiarTipoLetra('serif')" class="flex-1 px-3 py-2 text-xs rounded-lg bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10" style="font-family: Merriweather, serif">Serif</button>
                    <button onclick="cambiarTipoLetra('sans-serif')" class="flex-1 px-3 py-2 text-xs rounded-lg bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10" style="font-family: Inter, sans-serif">Sans</button>
                </div>
            </div>
        </aside>

        {{-- Área de Lectura Principal --}}
        <article class="w-full max-w-[850px] bg-[var(--color-surface)] px-6 md:px-16 py-16 rounded-[3rem] shadow-sm border border-[var(--color-border)] transition-colors duration-300">
            
            <div id="contenido-lectura" 
                 class="font-novel text-xl md:text-2xl leading-[2] text-[var(--color-reading-text)] space-y-10 selection:bg-[var(--color-primary)] selection:text-white"
                 data-tamano="{{ $preferencias['tamano_letra'] }}"
                 data-espaciado="{{ $preferencias['espaciado'] }}">
                
                @if($traduccion && $traduccion->contenido_traducido)
                    {!! nl2br(e($traduccion->contenido_traducido)) !!}
                @else
                    {!! nl2br(e($capitulo->contenido_original)) !!}
                @endif
                
            </div>

            {{-- Navegación Inferior --}}
            <nav class="mt-20 pt-12 border-t border-[var(--color-border)] flex flex-col sm:flex-row gap-4 items-center justify-between">
                @if($anterior)
                    <a href="{{ route('lectura.show', $anterior->id_capitulo) }}" 
                       class="w-full sm:w-auto flex items-center justify-center gap-3 px-8 py-4 bg-[var(--color-background)] hover:bg-[var(--color-primary)]/10 text-[var(--color-reading-text)] font-bold rounded-2xl transition-all group">
                        <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        Anterior
                    </a>
                @else
                    <div></div>
                @endif
                
                {{-- Botón para comentarios --}}
                <button onclick="toggleComentarios()" class="flex items-center gap-2 p-4 text-[var(--color-muted-text)] hover:text-[var(--color-primary)] transition-colors">
                    <span class="material-symbols-outlined">chat_bubble</span>
                    <span class="text-xs font-bold uppercase tracking-widest">Comentarios</span>
                </button>

                @if($siguiente)
                    <a href="{{ route('lectura.show', $siguiente->id_capitulo) }}" 
                       class="w-full sm:w-auto flex items-center justify-center gap-3 px-10 py-4 bg-[var(--color-primary)] text-white font-bold rounded-2xl hover:bg-[var(--color-primary-dark)] shadow-lg shadow-[var(--color-primary)]/20 transition-all group">
                        Siguiente Capítulo
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                @else
                    <div></div>
                @endif
            </nav>
        </article>

        {{-- Barra Lateral Derecha: Interacción Social --}}
        <aside class="fixed right-8 top-40 hidden xl:flex flex-col gap-4 z-40">
            <div class="flex flex-col gap-6 p-4 bg-[var(--color-surface)] rounded-[2rem] shadow-xl border border-[var(--color-border)]">
                <div class="flex flex-col items-center gap-1 group">
                    <button onclick="toggleFavorito({{ $capitulo->id_capitulo }})" 
                            class="p-3 rounded-2xl bg-[var(--color-background)] text-[var(--color-muted-text)] hover:text-[var(--color-primary)] transition-all">
                        <span class="material-symbols-outlined">favorite</span>
                    </button>
                    <span class="text-[9px] font-black text-[var(--color-muted-text)] uppercase">Favorito</span>
                </div>
                <div class="flex flex-col items-center gap-1 group">
                    <button onclick="votarCapitulo({{ $capitulo->id_capitulo }})" 
                            class="p-3 rounded-2xl bg-[var(--color-accent)]/10 text-[var(--color-accent)] hover:bg-[var(--color-accent)] hover:text-white transition-all">
                        <span class="material-symbols-outlined">thumb_up</span>
                    </button>
                    <span class="text-[9px] font-black text-[var(--color-muted-text)] uppercase">Votar</span>
                </div>
                <div class="h-px bg-[var(--color-border)] mx-2"></div>
                <button onclick="compartirCapitulo()" class="p-3 rounded-2xl text-[var(--color-muted-text)] hover:text-[var(--color-primary)] transition-all">
                    <span class="material-symbols-outlined">share</span>
                </button>
            </div>
        </aside>
    </div>
</main>

{{-- Mobile Reader Menu --}}
<div class="fixed bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-1 p-1.5 bg-[var(--color-surface)]/90 backdrop-blur-xl border border-[var(--color-border)] rounded-full shadow-2xl xl:hidden z-[100]">
    <button onclick="toggleIndice()" class="p-4 rounded-full text-[var(--color-muted-text)]">
        <span class="material-symbols-outlined">list</span>
    </button>
    <button onclick="toggleAjustesMobile()" class="p-4 rounded-full text-[var(--color-muted-text)]">
        <span class="material-symbols-outlined">settings</span>
    </button>
    <button class="p-5 bg-[var(--color-primary)] text-white rounded-full shadow-lg shadow-[var(--color-primary)]/40">
        <span class="material-symbols-outlined">bolt</span>
    </button>
    <button class="p-4 rounded-full text-[var(--color-accent)]">
        <span class="material-symbols-outlined">star</span>
    </button>
    <button class="p-4 rounded-full text-[var(--color-muted-text)]">
        <span class="material-symbols-outlined">bookmark</span>
    </button>
</div>

{{-- Modal de comentarios (opcional) --}}
<div id="modal-comentarios" class="fixed inset-0 bg-black/50 z-[200] hidden items-center justify-center">
    <div class="bg-[var(--color-surface)] w-full max-w-2xl max-h-[80vh] rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-[var(--color-border)] flex justify-between items-center">
            <h3 class="text-xl font-bold">Comentarios</h3>
            <button onclick="toggleComentarios()" class="p-2 hover:bg-[var(--color-border)] rounded-full">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto" id="lista-comentarios">
            <!-- Aquí irían los comentarios cargados vía AJAX -->
            <p class="text-[var(--color-muted-text)] text-center">Cargando comentarios...</p>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Estilos base para el contenido */
    #contenido-lectura {
        transition: all 0.2s ease;
    }
    
    /* Variables dinámicas para tamaños de letra - APLICADAS AL HTML */
    html[data-tamano-letra="pequeno"] #contenido-lectura {
        font-size: 1rem !important;
        line-height: 1.8 !important;
    }
    html[data-tamano-letra="normal"] #contenido-lectura {
        font-size: 1.25rem !important;
        line-height: 2 !important;
    }
    html[data-tamano-letra="grande"] #contenido-lectura {
        font-size: 1.5rem !important;
        line-height: 2.2 !important;
    }
    html[data-tamano-letra="muy_grande"] #contenido-lectura {
        font-size: 1.875rem !important;
        line-height: 2.4 !important;
    }
    
    /* Espaciado entre párrafos - APLICADOS AL HTML */
    html[data-espaciado="normal"] #contenido-lectura {
        --espaciado-parrafo: 2.5rem;
    }
    html[data-espaciado="amplio"] #contenido-lectura {
        --espaciado-parrafo: 4rem;
    }
    html[data-espaciado="muy_amplio"] #contenido-lectura {
        --espaciado-parrafo: 6rem;
    }
    
    #contenido-lectura > * + * {
        margin-top: var(--espaciado-parrafo, 2.5rem) !important;
    }
    
    /* Tipo de letra - APLICADOS AL HTML */
    html[data-tipo-letra="serif"] #contenido-lectura {
        font-family: Merriweather, Georgia, serif !important;
    }
    html[data-tipo-letra="sans-serif"] #contenido-lectura {
        font-family: Inter, -apple-system, BlinkMacSystemFont, sans-serif !important;
    }
    
    /* Temas */
    html[data-tema="sepia"] {
        --color-background: #fbf7f0;
        --color-surface: #f5efe6;
        --color-reading-text: #5f4b3a;
        --color-border: #e8d9cc;
    }
</style>
@endpush

@push('scripts')
<script>
// Variables globales
let progresoInterval;
let capituloId = {{ $capitulo->id_capitulo }};

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando...');
    inicializarProgreso();
    cargarPreferencias();
    
    // Debug: mostrar valores actuales
    console.log('Tamaño actual:', document.documentElement.getAttribute('data-tamano-letra'));
    console.log('Espaciado actual:', document.documentElement.getAttribute('data-espaciado'));
    console.log('Tipo letra actual:', document.documentElement.getAttribute('data-tipo-letra'));
});

// ============================================
// PROGRESO DE LECTURA
// ============================================
function inicializarProgreso() {
    window.addEventListener('scroll', actualizarProgreso);
    actualizarProgreso();
}

function actualizarProgreso() {
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight - windowHeight;
    const scrollTop = window.scrollY;
    const progreso = (scrollTop / documentHeight) * 100;
    
    document.getElementById('progress-bar').style.width = progreso + '%';
    
    if (Math.floor(progreso) % 5 === 0) {
        guardarProgreso(Math.floor(progreso));
    }
}

function guardarProgreso(progreso) {
    fetch(`/lectura/${capituloId}/progreso`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ progreso: progreso })
    }).catch(error => console.error('Error guardando progreso:', error));
}

// ============================================
// AJUSTES DE LECTURA - CORREGIDO
// ============================================
function toggleAjustes() {
    const panel = document.getElementById('panel-ajustes');
    panel.classList.toggle('hidden');
}

function toggleAjustesMobile() {
    alert('Ajustes en móvil - Implementar según necesidad');
}

function cambiarTamanoLetra(tamano) {
    console.log('Cambiando tamaño a:', tamano);
    // Aplicar al HTML element
    document.documentElement.setAttribute('data-tamano-letra', tamano);
    // Guardar en cookie
    guardarConfiguracion('tamano_letra', tamano);
    // Feedback visual
    mostrarNotificacion('Tamaño: ' + tamano);
}

function cambiarEspaciado(espaciado) {
    console.log('Cambiando espaciado a:', espaciado);
    document.documentElement.setAttribute('data-espaciado', espaciado);
    guardarConfiguracion('espaciado', espaciado);
    mostrarNotificacion('Espaciado: ' + espaciado);
}

function cambiarTipoLetra(tipo) {
    console.log('Cambiando tipo letra a:', tipo);
    document.documentElement.setAttribute('data-tipo-letra', tipo);
    guardarConfiguracion('tipo_letra', tipo);
    mostrarNotificacion('Tipo: ' + tipo);
}

function guardarConfiguracion(clave, valor) {
    fetch('/lectura/configuracion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ [clave]: valor })
    }).catch(error => console.error('Error guardando configuración:', error));
}

function cargarPreferencias() {
    console.log('Intentando cargar preferencias...');
    
    const main = document.querySelector('main');
    if (!main) {
        console.error('ERROR: No se encontró el elemento main');
        return;
    }
    
    // Obtener valores de los atributos data
    const tamano = main.dataset.tamanoLetra;
    const tipo = main.dataset.tipoLetra;
    const espaciado = main.dataset.espaciado;
    const tema = main.dataset.tema;
    
    console.log('Valores obtenidos del main:', { tamano, tipo, espaciado, tema });
    
    // Valores por defecto si algún atributo falta
    const valores = {
        tamano: tamano || 'normal',
        tipo: tipo || 'serif',
        espaciado: espaciado || 'normal',
        tema: tema || 'claro'
    };
    
    console.log('Aplicando valores:', valores);
    
    // Aplicar al elemento HTML
    document.documentElement.setAttribute('data-tamano-letra', valores.tamano);
    document.documentElement.setAttribute('data-tipo-letra', valores.tipo);
    document.documentElement.setAttribute('data-espaciado', valores.espaciado);
    document.documentElement.setAttribute('data-tema', valores.tema);
    
    // Verificar que se aplicaron
    console.log('Verificación - HTML attributes después de aplicar:', {
        tamano: document.documentElement.getAttribute('data-tamano-letra'),
        tipo: document.documentElement.getAttribute('data-tipo-letra'),
        espaciado: document.documentElement.getAttribute('data-espaciado'),
        tema: document.documentElement.getAttribute('data-tema')
    });
}

// ============================================
// INTERACCIÓN SOCIAL (sin cambios)
// ============================================
function toggleFavorito(idCapitulo) {
    fetch(`/capitulo/${idCapitulo}/favorito`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        mostrarNotificacion(data.favorito ? 'Añadido a favoritos' : 'Eliminado de favoritos');
    })
    .catch(error => console.error('Error:', error));
}

function votarCapitulo(idCapitulo) {
    fetch(`/capitulo/${idCapitulo}/votar`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        mostrarNotificacion('¡Gracias por tu voto!');
    })
    .catch(error => console.error('Error:', error));
}

function compartirCapitulo() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        mostrarNotificacion('Enlace copiado al portapapeles');
    }
}

// ============================================
// COMENTARIOS (sin cambios)
// ============================================
function toggleComentarios() {
    const modal = document.getElementById('modal-comentarios');
    modal.classList.toggle('hidden');
    
    if (!modal.classList.contains('hidden')) {
        cargarComentarios();
    }
}

function cargarComentarios() {
    fetch(`/capitulo/${capituloId}/comentarios`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('lista-comentarios');
            if (data.length === 0) {
                container.innerHTML = '<p class="text-[var(--color-muted-text)] text-center">No hay comentarios aún</p>';
            } else {
                container.innerHTML = data.map(comentario => `
                    <div class="mb-4 p-4 bg-[var(--color-background)] rounded-xl">
                        <p class="font-bold">${comentario.usuario}</p>
                        <p class="text-[var(--color-reading-text)]">${comentario.contenido}</p>
                    </div>
                `).join('');
            }
        })
        .catch(error => console.error('Error cargando comentarios:', error));
}

// ============================================
// UTILIDADES
// ============================================
function mostrarNotificacion(mensaje) {
    const notificacion = document.createElement('div');
    notificacion.className = 'fixed bottom-4 right-4 bg-[var(--color-primary)] text-white px-6 py-3 rounded-2xl shadow-xl z-[300] animate-fade-in-up';
    notificacion.textContent = mensaje;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.remove();
    }, 3000);
}

// Atajo de teclado
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft' && {{ $anterior ? 'true' : 'false' }}) {
        window.location.href = '{{ $anterior ? route("lectura.show", $anterior->id_capitulo) : "#" }}';
    } else if (e.key === 'ArrowRight' && {{ $siguiente ? 'true' : 'false' }}) {
        window.location.href = '{{ $siguiente ? route("lectura.show", $siguiente->id_capitulo) : "#" }}';
    }
});
</script>
@endpush