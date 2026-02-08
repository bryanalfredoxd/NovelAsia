{{-- Hero Section --}}
<section class="relative h-[400px] md:h-[480px] rounded-3xl overflow-hidden mb-12 group shadow-lg border border-[var(--color-border)]">
    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-105" 
         style='background-image: url("https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568&auto=format&fit=crop");'>
    </div>
    
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
