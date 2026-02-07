@extends('layouts.app')

@section('title', 'Dashboard - NovelAsia')

@section('content')
    <main class="pt-20 md:pt-24 max-w-[1440px] mx-auto px-4 md:px-8 lg:px-12">
        
        {{-- Mobile Search --}}
        <div class="md:hidden mb-6 mt-2">
            <label class="relative flex items-center w-full">
                <div class="absolute left-4 text-primary/70">
                    <span class="material-symbols-outlined">search</span>
                </div>
                <input class="w-full h-12 glass rounded-full pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary/50 focus:outline-none border-none placeholder:text-slate-400" placeholder="Buscar novelas ligeras..." type="text"/>
            </label>
        </div>

        {{-- Featured Section --}}
        <section class="relative h-[400px] md:h-[480px] rounded-2xl md:rounded-3xl overflow-hidden mb-8 md:mb-12 group shadow-2xl">
            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" 
                 style='background-image: linear-gradient(to top, #0f172a 10%, transparent 90%), linear-gradient(to right, #0f172a 0%, transparent 50%), url("https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568&auto=format&fit=crop");'>
            </div>
            
            <div class="md:hidden absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>

            <div class="relative h-full flex flex-col justify-end md:justify-center p-6 md:px-16 max-w-full md:max-w-2xl">
                <div class="flex gap-2 mb-3 md:mb-6">
                    <span class="px-3 py-1 glass rounded-full text-[10px] md:text-xs font-bold text-primary uppercase tracking-widest border border-primary/30">Destacado</span>
                    <span class="px-3 py-1 glass rounded-full text-[10px] md:text-xs font-bold text-indigo-400 uppercase tracking-widest border border-indigo-400/30">Popular</span>
                </div>
                
                <h2 class="text-3xl md:text-6xl font-display font-bold text-white mb-3 md:mb-6 leading-tight">El Alquimista Reencarnado</h2>
                
                <p class="text-slate-300 text-sm md:text-lg mb-6 md:mb-8 leading-relaxed line-clamp-3 md:line-clamp-none">
                    Un alquimista genio renace en un mundo donde la magia está muriendo. Con conocimientos prohibidos de su vida pasada, debe restaurar el equilibrio de los elementos.
                </p>
                
                <div class="flex items-center gap-3 md:gap-4">
                    <button class="bg-primary hover:bg-primary/90 text-white px-6 md:px-8 py-3 md:py-4 rounded-xl font-bold text-sm md:text-lg sakura-glow flex items-center gap-2 md:gap-3 transition-all transform active:scale-95">
                        <span class="material-symbols-outlined">auto_stories</span>
                        Continuar Leyendo
                    </button>
                    <button class="glass hover:bg-white/10 text-white px-4 md:px-5 py-3 md:py-4 rounded-xl transition-all">
                        <span class="material-symbols-outlined">bookmark_add</span>
                    </button>
                    <button class="hidden md:flex glass hover:bg-white/10 text-white px-5 py-4 rounded-xl transition-all">
                        <span class="material-symbols-outlined">share</span>
                    </button>
                </div>
            </div>
        </section>

        <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
            
            {{-- Main Content --}}
            <div class="flex-1">
                <div class="flex items-center justify-between mb-6 md:mb-8">
                    <h3 class="text-xl md:text-2xl font-display font-bold flex items-center gap-2 md:gap-3">
                        <span class="material-symbols-outlined text-primary text-2xl md:text-3xl">history</span>
                        Continuar Leyendo
                    </h3>
                    <a class="text-primary hover:underline font-semibold flex items-center gap-1 text-sm md:text-base" href="#">
                        Ver Biblioteca <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>

                {{-- Continue Reading Carousel --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6 mb-12">
                     {{-- Novel Card 1 --}}
                     <div class="group cursor-pointer">
                        <div class="relative aspect-[3/4.5] rounded-xl md:rounded-2xl overflow-hidden mb-3 shadow-xl bg-slate-800">
                            <div class="absolute top-2 left-2 md:top-3 md:left-3 z-10 glass rounded-lg px-2 py-1 flex items-center gap-1 border border-primary/30">
                                <span class="size-1.5 md:size-2 rounded-full bg-primary animate-pulse"></span>
                                <span class="text-[9px] md:text-[10px] font-bold text-white">Ch. 245</span>
                            </div>
                            <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" style='background-image: url("https://images.unsplash.com/photo-1614726365723-49faaa564763?q=80&w=1887&auto=format&fit=crop");'></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-80"></div>
                            {{-- Progress Bar --}}
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-700">
                                <div class="h-full bg-primary w-[75%]"></div>
                            </div>
                        </div>
                        <h4 class="font-bold text-sm md:text-base text-white group-hover:text-primary transition-colors truncate">Solo Swordmaster</h4>
                        <p class="text-slate-500 text-[10px] md:text-xs mt-1">Capítulo 245 / 300</p>
                    </div>

                    {{-- Novel Card 2 --}}
                     <div class="group cursor-pointer">
                        <div class="relative aspect-[3/4.5] rounded-xl md:rounded-2xl overflow-hidden mb-3 shadow-xl bg-slate-800">
                             <div class="absolute top-2 left-2 md:top-3 md:left-3 z-10 glass rounded-lg px-2 py-1 flex items-center gap-1 border border-indigo-400/30">
                                <span class="text-[9px] md:text-[10px] font-bold text-white">Ch. 18</span>
                            </div>
                            <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" style='background-image: url("https://images.unsplash.com/photo-1592496001020-d31bd830651f?q=80&w=1887&auto=format&fit=crop");'></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-80"></div>
                             {{-- Progress Bar --}}
                             <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-700">
                                <div class="h-full bg-indigo-500 w-[30%]"></div>
                            </div>
                        </div>
                        <h4 class="font-bold text-sm md:text-base text-white group-hover:text-primary transition-colors truncate">Cyber Rebirth 2077</h4>
                        <p class="text-slate-500 text-[10px] md:text-xs mt-1">Capítulo 18 / 150</p>
                    </div>
                </div>

                {{-- Recommendations --}}
                <div class="flex items-center justify-between mb-6 md:mb-8">
                    <h3 class="text-xl md:text-2xl font-display font-bold flex items-center gap-2 md:gap-3">
                        <span class="material-symbols-outlined text-primary text-2xl md:text-3xl">recommend</span>
                        Recomendado para ti
                    </h3>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                    {{-- Novel Card 3 --}}
                    <div class="group cursor-pointer">
                        <div class="relative aspect-[3/4.5] rounded-xl md:rounded-2xl overflow-hidden mb-3 shadow-xl bg-slate-800">
                            <div class="absolute top-2 left-2 md:top-3 md:left-3 z-10 glass rounded-lg px-2 py-1 flex items-center gap-1 border border-primary/30">
                                <span class="text-[9px] md:text-[10px] font-bold text-white">98% Match</span>
                            </div>
                            <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" style='background-image: url("https://images.unsplash.com/photo-1635805737707-575885ab0820?q=80&w=1887&auto=format&fit=crop");'></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-80"></div>
                        </div>
                        <h4 class="font-bold text-sm md:text-base text-white group-hover:text-primary transition-colors truncate">Moonlight Sculptor</h4>
                        <p class="text-slate-500 text-[10px] md:text-xs mt-1">Fantasía / Aventura</p>
                    </div>

                    {{-- Novel Card 4 --}}
                    <div class="group cursor-pointer">
                        <div class="relative aspect-[3/4.5] rounded-xl md:rounded-2xl overflow-hidden mb-3 shadow-xl bg-slate-800">
                             <div class="absolute top-2 left-2 md:top-3 md:left-3 z-10 glass rounded-lg px-2 py-1 flex items-center gap-1 border border-primary/30">
                                <span class="text-[9px] md:text-[10px] font-bold text-white">95% Match</span>
                            </div>
                            <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" style='background-image: url("https://images.unsplash.com/photo-1542259681-dadcd731560b?q=80&w=1887&auto=format&fit=crop");'></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-80"></div>
                        </div>
                        <h4 class="font-bold text-sm md:text-base text-white group-hover:text-primary transition-colors truncate">Shadow Sovereign</h4>
                        <p class="text-slate-500 text-[10px] md:text-xs mt-1">Acción / Misterio</p>
                    </div>

                    {{-- Novel Card 5 --}}
                    <div class="hidden sm:block group cursor-pointer">
                        <div class="relative aspect-[3/4.5] rounded-xl md:rounded-2xl overflow-hidden mb-3 shadow-xl bg-slate-800">
                             <div class="absolute top-2 left-2 md:top-3 md:left-3 z-10 glass rounded-lg px-2 py-1 flex items-center gap-1 border border-primary/30">
                                <span class="text-[9px] md:text-[10px] font-bold text-white">92% Match</span>
                            </div>
                            <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" style='background-image: url("https://images.unsplash.com/photo-1519074069444-1ba4fff66d16?q=80&w=1887&auto=format&fit=crop");'></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-80"></div>
                        </div>
                        <h4 class="font-bold text-sm md:text-base text-white group-hover:text-primary transition-colors truncate">Elementalist Legend</h4>
                        <p class="text-slate-500 text-[10px] md:text-xs mt-1">Magia / Escuela</p>
                    </div>
                </div>

                {{-- Genres Section --}}
                <div class="mt-12 md:mt-16 mb-8 md:mb-0">
                    <h3 class="text-xl md:text-2xl font-display font-bold mb-6 md:mb-8">Tus Géneros Favoritos</h3>
                    <div class="flex flex-wrap gap-3 md:gap-4">
                        <div class="px-5 md:px-8 py-3 md:py-4 glass bg-primary/20 rounded-xl md:rounded-2xl text-primary text-sm font-bold cursor-pointer hover:bg-primary/30 transition-all border border-primary/40">Isekai</div>
                        <div class="px-5 md:px-8 py-3 md:py-4 glass rounded-xl md:rounded-2xl text-slate-300 text-sm font-bold cursor-pointer hover:bg-white/5 transition-all">Fantasía</div>
                        <div class="px-5 md:px-8 py-3 md:py-4 glass rounded-xl md:rounded-2xl text-slate-300 text-sm font-bold cursor-pointer hover:bg-white/5 transition-all">Acción</div>
                        <div class="px-5 md:px-8 py-3 md:py-4 glass rounded-xl md:rounded-2xl text-slate-300 text-sm font-bold cursor-pointer hover:bg-white/5 transition-all">Romance</div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="w-full lg:w-80 shrink-0">
                <div class="lg:sticky lg:top-24">
                    <h3 class="text-lg md:text-xl font-display font-bold mb-4 md:mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">trending_up</span>
                        Tendencia
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-4">
                        
                        {{-- Top Novel 1 --}}
                        <div class="flex items-center glass p-3 md:p-4 rounded-xl gap-4 group cursor-pointer hover:border-primary/50 transition-all">
                            <div class="text-2xl md:text-3xl font-black text-primary/40 italic w-8 text-center">01</div>
                            <div class="size-14 md:size-16 rounded-xl bg-cover bg-center shrink-0 shadow-lg" style='background-image: url("https://images.unsplash.com/photo-1542259681-dadcd731560b?q=80&w=1887&auto=format&fit=crop");'></div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-sm text-white line-clamp-1 group-hover:text-primary">El Alquimista Reencarnado</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] text-slate-500 uppercase font-black tracking-widest">Fantasía</span>
                                </div>
                            </div>
                        </div>

                        {{-- Top Novel 2 --}}
                        <div class="flex items-center glass p-3 md:p-4 rounded-xl gap-4 group cursor-pointer hover:border-white/30 transition-all">
                            <div class="text-2xl md:text-3xl font-black text-slate-700 italic w-8 text-center">02</div>
                            <div class="size-14 md:size-16 rounded-xl bg-cover bg-center shrink-0 shadow-lg" style='background-image: url("https://images.unsplash.com/photo-1614726365723-49faaa564763?q=80&w=1887&auto=format&fit=crop");'></div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-sm text-white line-clamp-1 group-hover:text-primary">Solo Swordmaster</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] text-slate-500 uppercase font-black tracking-widest">Acción</span>
                                </div>
                            </div>
                        </div>

                        {{-- Top Novel 3 --}}
                        <div class="flex items-center glass p-3 md:p-4 rounded-xl gap-4 group cursor-pointer hover:border-white/30 transition-all">
                            <div class="text-2xl md:text-3xl font-black text-slate-700 italic w-8 text-center">03</div>
                            <div class="size-14 md:size-16 rounded-xl bg-cover bg-center shrink-0 shadow-lg" style='background-image: url("https://images.unsplash.com/photo-1635805737707-575885ab0820?q=80&w=1887&auto=format&fit=crop");'></div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-sm text-white line-clamp-1 group-hover:text-primary">The Shadow Sovereign</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] text-slate-500 uppercase font-black tracking-widest">Misterio</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Premium Card --}}
                    <div class="hidden md:block mt-8 glass rounded-2xl p-6 bg-gradient-to-br from-primary/20 to-indigo-900/40 border border-primary/20">
                        <h5 class="font-display font-bold text-lg mb-2">Estado Premium</h5>
                        <p class="text-xs text-slate-300 mb-4">¡Gracias por tu soporte! Tienes acceso a todos los capítulos.</p>
                        <button class="w-full py-2 glass rounded-lg text-xs font-bold hover:bg-white/10 transition-all">Ver Beneficios</button>
                    </div>
                </div>
            </aside>
        </div>
    </main>
@endsection
