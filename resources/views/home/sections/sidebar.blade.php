{{-- Sidebar (Top semanal y tarjeta premium) --}}
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
        <div class="absolute -bottom-4 -right-4 size-24 bg-white/10 rounded-full blur-2xl"></div>
    </div>
</div>
