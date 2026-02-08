{{-- Actualizaciones (grid de novelas) --}}
<div>
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
