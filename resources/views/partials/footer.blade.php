<footer class="relative mt-20 border-t border-white/10 bg-slate-950/50 backdrop-blur-sm">
    {{-- Decorative Top Line --}}
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-primary/50 to-transparent opacity-50"></div>

    <div class="max-w-[1440px] mx-auto px-6 md:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-12 mb-12">
            
            {{-- Brand Column --}}
            <div class="md:col-span-2 lg:col-span-2">
                <a href="/" class="flex items-center gap-3 mb-6 group w-fit">
                    <div class="size-10 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg sakura-glow group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-2xl">auto_stories</span>
                    </div>
                    <span class="text-2xl font-display font-bold tracking-tight text-white">
                        Novel<span class="text-primary">Asia</span>
                    </span>
                </a>
                
                <p class="text-slate-400 text-sm leading-relaxed max-w-sm mb-8">
                    El destino definitivo para entusiastas de las Novelas Ligeras. Descubre miles de historias traducidas con la mejor calidad y únete a una comunidad global apasionada.
                </p>
                
                <div class="flex gap-4">
                    <a href="#" class="size-10 glass rounded-full flex items-center justify-center text-slate-300 hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 group" aria-label="Sitio Global">
                        <span class="material-symbols-outlined text-xl group-hover:rotate-12 transition-transform">public</span>
                    </a>
                    <a href="#" class="size-10 glass rounded-full flex items-center justify-center text-slate-300 hover:bg-[#5865F2] hover:text-white hover:border-[#5865F2] transition-all duration-300 group" aria-label="Discord">
                        <span class="material-symbols-outlined text-xl group-hover:scale-110 transition-transform">forum</span>
                    </a>
                    <a href="#" class="size-10 glass rounded-full flex items-center justify-center text-slate-300 hover:bg-sky-500 hover:text-white hover:border-sky-500 transition-all duration-300 group" aria-label="Twitter">
                        <span class="material-symbols-outlined text-xl group-hover:rotate-12 transition-transform">share</span>
                    </a>
                </div>
            </div>

            {{-- Links Columns --}}
            <div>
                <h4 class="font-display font-bold text-white mb-6 flex items-center gap-2">
                    <span class="size-1.5 bg-primary rounded-full"></span> Explorar
                </h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Ranking Semanal</a></li>
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Nuevos Estrenos</a></li>
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Novelas Completas</a></li>
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Buscador Avanzado</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-display font-bold text-white mb-6 flex items-center gap-2">
                    <span class="size-1.5 bg-primary rounded-full"></span> Comunidad
                </h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Nuestra Misión</a></li>
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Discord Oficial</a></li>
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Sugerir Novela</a></li>
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Reportar Error</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-display font-bold text-white mb-6 flex items-center gap-2">
                    <span class="size-1.5 bg-primary rounded-full"></span> Legal
                </h4>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Centro de Ayuda</a></li>
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Términos de Servicio</a></li>
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">Política de Privacidad</a></li>
                    <li><a class="hover:text-primary hover:translate-x-1 transition-all inline-block" href="#">DMCA</a></li>
                </ul>
            </div>
        </div>
        
        <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} <strong class="text-slate-400">NovelAsia</strong>. Todos los derechos reservados.</p>
            <div class="flex items-center gap-2">
                <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
                <span>Sistemas Operativos</span>
            </div>
        </div>
    </div>
</footer>