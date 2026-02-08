<footer class="relative mt-auto border-t border-[var(--color-border)] bg-[var(--color-surface)] transition-colors duration-300">
    {{-- Línea decorativa superior con tu color primario --}}
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-[var(--color-primary)]/40 to-transparent"></div>

    <div class="max-w-[1440px] mx-auto px-6 md:px-8 py-12 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-12 mb-12">
            
            {{-- Columna de Marca --}}
            <div class="md:col-span-2 lg:col-span-2">
                <a href="/" class="flex items-center gap-3 mb-6 group w-fit">
                    <div class="size-10 bg-[var(--color-primary)] rounded-xl flex items-center justify-center text-white shadow-lg transition-transform group-hover:scale-105">
                        <span class="material-symbols-outlined text-2xl">auto_stories</span>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-[var(--color-reading-text)]">
                        Novel<span class="text-[var(--color-primary)]">Asia</span>
                    </span>
                </a>
                
                <p class="text-[var(--color-muted-text)] text-sm leading-relaxed max-w-sm mb-8">
                    Tu portal predilecto para novelas ligeras en español. Historias épicas, traducciones de calidad y una comunidad que no deja de crecer.
                </p>
                
                {{-- RRSS: Usando variables para los círculos --}}
                <div class="flex gap-3">
                    <a href="#" class="size-10 rounded-full border border-[var(--color-border)] flex items-center justify-center text-[var(--color-muted-text)] hover:bg-[var(--color-primary)] hover:text-white hover:border-[var(--color-primary)] transition-all duration-300" aria-label="Discord">
                        <span class="material-symbols-outlined text-xl">forum</span>
                    </a>
                    <a href="#" class="size-10 rounded-full border border-[var(--color-border)] flex items-center justify-center text-[var(--color-muted-text)] hover:bg-[var(--color-primary)] hover:text-white hover:border-[var(--color-primary)] transition-all duration-300" aria-label="X">
                        <span class="material-symbols-outlined text-xl">share</span>
                    </a>
                </div>
            </div>

            {{-- Columnas de Links --}}
            @php
                $footerLinks = [
                    'Explorar' => ['Ranking Semanal', 'Nuevos Estrenos', 'Novelas Completas', 'Buscador'],
                    'Comunidad' => ['Nuestra Misión', 'Discord Oficial', 'Sugerir Novela', 'Reportar'],
                    'Legal' => ['Ayuda', 'Términos', 'Privacidad', 'DMCA']
                ];
            @endphp

            @foreach($footerLinks as $title => $links)
            <div>
                <h4 class="font-bold text-[var(--color-reading-text)] mb-6 flex items-center gap-2 text-sm uppercase tracking-wider">
                    <span class="size-1.5 bg-[var(--color-primary)] rounded-full"></span> 
                    {{ $title }}
                </h4>
                <ul class="space-y-3 text-sm">
                    @foreach($links as $link)
                    <li>
                        <a class="text-[var(--color-muted-text)] hover:text-[var(--color-primary)] hover:translate-x-1 transition-all inline-block" href="#">
                            {{ $link }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
        
        {{-- Línea Final --}}
        <div class="pt-8 border-t border-[var(--color-border)] flex flex-col md:flex-row items-center justify-between gap-4 text-[10px] md:text-xs text-[var(--color-muted-text)]">
            <p>&copy; {{ date('Y') }} <strong class="text-[var(--color-reading-text)]">NovelAsia</strong>. Hecho para lectores por lectores.</p>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <span class="size-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span>
                    <span>Servidores Online</span>
                </div>
                <span class="hidden md:inline text-[var(--color-border)]">|</span>
                <span>v2.1.0-beta</span>
            </div>
        </div>
    </div>
</footer>
