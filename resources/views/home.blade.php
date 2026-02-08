{{-- 
    NOTA: Este archivo NO extiende de layouts.app porque ya está siendo 
    incluido dentro de welcome.blade.php (que ya tiene el layout).
    Aquí va solo el HTML puro del contenido.
--}}

<main class="pt-24 pb-12 max-w-[1440px] mx-auto px-4 md:px-8 lg:px-12 min-h-screen">
    
    {{-- Mobile Search (Visible solo en móvil) --}}
    <div class="md:hidden mb-6 mt-2 animate-fade-in-down">
        <label class="relative flex items-center w-full group">
            <div class="absolute left-4 text-slate-400 group-focus-within:text-primary transition-colors">
                <span class="material-symbols-outlined">search</span>
            </div>
            <input 
                class="w-full h-12 glass rounded-full pl-12 pr-4 text-sm text-white focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none border border-white/10 placeholder:text-slate-500 transition-all" 
                placeholder="Buscar novelas, autores o géneros..." 
                type="text"
            />
        </label>
    </div>

    {{-- Featured Section (Hero) --}}
    <section class="relative h-[420px] md:h-[500px] rounded-2xl md:rounded-3xl overflow-hidden mb-12 group shadow-2xl animate-fade-in">
        {{-- Imagen de Fondo con Efecto Zoom --}}
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" 
             style='background-image: url("https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568&auto=format&fit=crop");'>
        </div>
        
        {{-- Degradados para legibilidad --}}
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent md:bg-gradient-to-r md:from-slate-950 md:via-slate-950/60 md:to-transparent"></div>

        {{-- Contenido del Hero --}}
        <div class="relative h-full flex flex-col justify-end md:justify-center p-6 md:px-16 max-w-full md:max-w-3xl">
            <div class="flex gap-2 mb-4 animate-fade-in-up">
                <span class="px-3 py-1 glass bg-primary/20 rounded-full text-[10px] md:text-xs font-bold text-primary uppercase tracking-widest border border-primary/30 backdrop-blur-md">
                    Destacado
                </span>
                <span class="px-3 py-1 glass rounded-full text-[10px] md:text-xs font-bold text-indigo-300 uppercase tracking-widest border border-indigo-400/30">
                    Fantasía
                </span>
            </div>
            
            <h2 class="text-3xl md:text-6xl font-display font-bold text-white mb-4 leading-tight drop-shadow-lg animate-fade-in-up delay-100">
                El Alquimista Reencarnado
            </h2>
            
            <p class="text-slate-300 text-sm md:text-lg mb-8 leading-relaxed line-clamp-3 md:line-clamp-none max-w-2xl animate-fade-in-up delay-200">
                Un alquimista genio renace en un mundo donde la magia está muriendo. Con conocimientos prohibidos de su vida pasada, debe restaurar el equilibrio de los elementos antes de que el caos consuma todo.
            </p>
            
            <div class="flex items-center gap-3 md:gap-4 animate-fade-in-up delay-300">
                <button class="bg-primary hover:bg-primary-hover text-white px-6 md:px-8 py-3 md:py-4 rounded-xl font-bold text-sm md:text-lg sakura-glow flex items-center gap-2 md:gap-3 transition-all transform active:scale-95">
                    <span class="material-symbols-outlined">auto_stories</span>
                    Leer Ahora
                </button>
                <button class="glass hover:bg-white/10 text-white px-4 md:px-5 py-3 md:py-4 rounded-xl transition-all border border-white/10 hover:border-white/30" title="Guardar en Biblioteca">
                    <span class="material-symbols-outlined">bookmark_add</span>
                </button>
                <button class="hidden md:flex glass hover:bg-white/10 text-white px-5 py-4 rounded-xl transition-all border border-white/10 hover:border-white/30" title="Compartir">
                    <span class="material-symbols-outlined">share</span>
                </button>
            </div>
        </div>
    </section>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
        
        {{-- Main Content (Left Column) --}}
        <div class="flex-1">
            
            {{-- Section Header --}}
            <div class="flex items-center justify-between mb-6 md:mb-8">
                <h3 class="text-xl md:text-2xl font-display font-bold flex items-center gap-2 text-white">
                    <span class="material-symbols-outlined text-primary text-2xl md:text-3xl">local_fire_department</span>
                    Últimas Actualizaciones
                </h3>
                <a class="text-primary hover:text-white hover:underline font-semibold flex items-center gap-1 text-sm md:text-base transition-colors" href="#">
                    Ver Todo <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            {{-- Novel Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 mb-12">
                
                {{-- Novel Card 1 --}}
                <div class="group cursor-pointer">
                    <div class="relative aspect-[2/3] rounded-xl md:rounded-2xl overflow-hidden mb-3 shadow-lg bg-slate-800 border border-white/5 group-hover:border-primary/50 transition-colors">
                        <div class="absolute top-2 left-2 z-10 glass rounded-md px-2 py-1 flex items-center gap-1.5 border border-primary/30">
                            <span class="size-1.5 md:size-2 rounded-full bg-primary animate-pulse"></span>
                            <span class="text-[9px] md:text-[10px] font-bold text-white tracking-wide">HACE 2H</span>
                        </div>
                        <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" 
                             style='background-image: url("https://images.unsplash.com/photo-1614726365723-49faaa564763?q=80&w=1887&auto=format&fit=crop");'>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-90"></div>
                        
                        {{-- Info on Hover (Optional) --}}
                        <div class="absolute bottom-0 left-0 w-full p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <span class="text-[10px] font-bold text-primary uppercase bg-slate-950/80 px-2 py-1 rounded">Acción</span>
                        </div>
                    </div>
                    <h4 class="font-bold text-sm md:text-base text-slate-200 group-hover:text-primary transition-colors truncate leading-tight">Solo Swordmaster</h4>
                    <p class="text-slate-500 text-xs mt-1 font-medium">Capítulo 245</p>
                </div>

                {{-- Novel Card 2 --}}
                <div class="group cursor-pointer">
                    <div class="relative aspect-[2/3] rounded-xl md:rounded-2xl overflow-hidden mb-3 shadow-lg bg-slate-800 border border-white/5 group-hover:border-primary/50 transition-colors">
                        <div class="absolute top-2 left-2 z-10 glass rounded-md px-2 py-1 flex items-center gap-1.5 border border-white/10">
                            <span class="text-[9px] md:text-[10px] font-bold text-slate-300 tracking-wide">HACE 5H</span>
                        </div>
                        <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" 
                             style='background-image: url("https://images.unsplash.com/photo-1592496001020-d31bd830651f?q=80&w=1887&auto=format&fit=crop");'>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-90"></div>
                    </div>
                    <h4 class="font-bold text-sm md:text-base text-slate-200 group-hover:text-primary transition-colors truncate leading-tight">Cyber Rebirth 2077</h4>
                    <p class="text-slate-500 text-xs mt-1 font-medium">Capítulo 18</p>
                </div>

                {{-- Novel Card 3 --}}
                <div class="group cursor-pointer">
                    <div class="relative aspect-[2/3] rounded-xl md:rounded-2xl overflow-hidden mb-3 shadow-lg bg-slate-800 border border-white/5 group-hover:border-primary/50 transition-colors">
                        <div class="absolute top-2 left-2 z-10 glass rounded-md px-2 py-1 flex items-center gap-1.5 border border-white/10">
                            <span class="text-[9px] md:text-[10px] font-bold text-slate-300 tracking-wide">AYER</span>
                        </div>
                        <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" 
                             style='background-image: url("https://images.unsplash.com/photo-1635805737707-575885ab0820?q=80&w=1887&auto=format&fit=crop");'>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-90"></div>
                    </div>
                    <h4 class="font-bold text-sm md:text-base text-slate-200 group-hover:text-primary transition-colors truncate leading-tight">Moonlight Sculptor</h4>
                    <p class="text-slate-500 text-xs mt-1 font-medium">Capítulo 89</p>
                </div>

                {{-- Novel Card 4 --}}
                <div class="group cursor-pointer">
                    <div class="relative aspect-[2/3] rounded-xl md:rounded-2xl overflow-hidden mb-3 shadow-lg bg-slate-800 border border-white/5 group-hover:border-primary/50 transition-colors">
                        <div class="absolute top-2 left-2 z-10 glass rounded-md px-2 py-1 flex items-center gap-1.5 border border-white/10">
                            <span class="text-[9px] md:text-[10px] font-bold text-slate-300 tracking-wide">AYER</span>
                        </div>
                        <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" 
                             style='background-image: url("https://images.unsplash.com/photo-1542259681-dadcd731560b?q=80&w=1887&auto=format&fit=crop");'>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-90"></div>
                    </div>
                    <h4 class="font-bold text-sm md:text-base text-slate-200 group-hover:text-primary transition-colors truncate leading-tight">Shadow Sovereign</h4>
                    <p class="text-slate-500 text-xs mt-1 font-medium">Capítulo 142</p>
                </div>
            </div>

            {{-- Genres Section --}}
            <div class="mt-8 mb-8 md:mb-0">
                <h3 class="text-xl md:text-2xl font-display font-bold mb-6 text-white">Explorar por Género</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="#" class="px-6 py-3 glass bg-primary/10 rounded-xl text-primary text-sm font-bold hover:bg-primary hover:text-white transition-all border border-primary/30 hover:border-primary">Isekai</a>
                    <a href="#" class="px-6 py-3 glass rounded-xl text-slate-300 text-sm font-bold hover:bg-white/10 hover:text-white transition-all border border-white/5">Fantasía</a>
                    <a href="#" class="px-6 py-3 glass rounded-xl text-slate-300 text-sm font-bold hover:bg-white/10 hover:text-white transition-all border border-white/5">Acción</a>
                    <a href="#" class="px-6 py-3 glass rounded-xl text-slate-300 text-sm font-bold hover:bg-white/10 hover:text-white transition-all border border-white/5">Romance</a>
                    <a href="#" class="px-6 py-3 glass rounded-xl text-slate-300 text-sm font-bold hover:bg-white/10 hover:text-white transition-all border border-white/5">Sci-Fi</a>
                    <a href="#" class="px-6 py-3 glass rounded-xl text-slate-300 text-sm font-bold hover:bg-white/10 hover:text-white transition-all border border-white/5">Escolar</a>
                    <a href="#" class="px-6 py-3 glass rounded-xl text-slate-300 text-sm font-bold hover:bg-white/10 hover:text-white transition-all border border-white/5">Wuxia</a>
                </div>
            </div>
        </div>

        {{-- Sidebar (Right Column) --}}
        <aside class="w-full lg:w-80 shrink-0">
            <div class="lg:sticky lg:top-28 space-y-8">
                
                {{-- Trending Widget --}}
                <div>
                    <h3 class="text-lg md:text-xl font-display font-bold mb-6 flex items-center gap-2 text-white">
                        <span class="material-symbols-outlined text-primary">trending_up</span>
                        Tendencia Semanal
                    </h3>
                    
                    <div class="space-y-4">
                        {{-- Top Novel 1 --}}
                        <div class="flex items-center glass p-3 rounded-xl gap-4 group cursor-pointer hover:bg-slate-800/80 hover:border-primary/40 border border-white/5 transition-all">
                            <div class="text-3xl font-black text-primary/30 italic w-8 text-center font-display">1</div>
                            <div class="size-14 rounded-lg bg-cover bg-center shrink-0 shadow-lg border border-white/10" 
                                 style='background-image: url("https://images.unsplash.com/photo-1542259681-dadcd731560b?q=80&w=1887&auto=format&fit=crop");'>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-sm text-slate-200 line-clamp-1 group-hover:text-primary transition-colors">El Alquimista</h4>
                                <span class="text-[10px] text-primary font-bold tracking-wider uppercase mt-1 inline-block">Fantasía</span>
                            </div>
                        </div>

                        {{-- Top Novel 2 --}}
                        <div class="flex items-center glass p-3 rounded-xl gap-4 group cursor-pointer hover:bg-slate-800/80 hover:border-white/20 border border-white/5 transition-all">
                            <div class="text-3xl font-black text-slate-700 italic w-8 text-center font-display">2</div>
                            <div class="size-14 rounded-lg bg-cover bg-center shrink-0 shadow-lg border border-white/10" 
                                 style='background-image: url("https://images.unsplash.com/photo-1614726365723-49faaa564763?q=80&w=1887&auto=format&fit=crop");'>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-sm text-slate-200 line-clamp-1 group-hover:text-primary transition-colors">Solo Swordmaster</h4>
                                <span class="text-[10px] text-slate-500 font-bold tracking-wider uppercase mt-1 inline-block">Acción</span>
                            </div>
                        </div>

                        {{-- Top Novel 3 --}}
                        <div class="flex items-center glass p-3 rounded-xl gap-4 group cursor-pointer hover:bg-slate-800/80 hover:border-white/20 border border-white/5 transition-all">
                            <div class="text-3xl font-black text-slate-700 italic w-8 text-center font-display">3</div>
                            <div class="size-14 rounded-lg bg-cover bg-center shrink-0 shadow-lg border border-white/10" 
                                 style='background-image: url("https://images.unsplash.com/photo-1635805737707-575885ab0820?q=80&w=1887&auto=format&fit=crop");'>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-sm text-slate-200 line-clamp-1 group-hover:text-primary transition-colors">Shadow Sovereign</h4>
                                <span class="text-[10px] text-slate-500 font-bold tracking-wider uppercase mt-1 inline-block">Misterio</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Premium Promo --}}
                <div class="glass rounded-2xl p-6 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-transparent to-purple-900/20 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative z-10">
                        <div class="size-10 rounded-full bg-primary/20 flex items-center justify-center mb-3 text-primary border border-primary/30">
                            <span class="material-symbols-outlined">diamond</span>
                        </div>
                        <h5 class="font-display font-bold text-lg text-white mb-2">NovelAsia Premium</h5>
                        <p class="text-xs text-slate-300 mb-4 leading-relaxed">
                            Elimina la publicidad, descarga capítulos offline y apoya a los traductores.
                        </p>
                        <button class="w-full py-2.5 glass bg-white/5 hover:bg-primary hover:text-white hover:border-primary border border-white/10 rounded-lg text-xs font-bold transition-all text-slate-200">
                            Ver Planes
                        </button>
                    </div>
                </div>

            </div>
        </aside>
    </div>
</main>