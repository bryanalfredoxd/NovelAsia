@extends('layouts.app')

@section('title', 'Únete a NovelAsia')

@section('content')
    {{-- Fondo dinámico basado en variables --}}
    <div class="fixed inset-0 bg-[var(--color-background)] -z-10 transition-colors duration-300"></div>
    <div class="fixed inset-0 bg-[url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568')] bg-cover bg-center opacity-[0.03] dark:opacity-[0.05] -z-10"></div>

    <main class="min-h-screen pt-24 pb-12 flex items-center justify-center px-4 md:px-8">
        <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            
            {{-- Lado Izquierdo: Ilustración (Solo Desktop) --}}
            <div class="hidden lg:flex flex-col items-center justify-center animate-fade-in">
                <div class="w-full max-w-lg relative">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-[var(--color-primary)]/15 rounded-full blur-3xl -z-10"></div>
                    
                    <div class="w-full aspect-square flex items-center justify-center drop-shadow-2xl">
                        <dotlottie-player
                            src="{{ asset('assets/animations/books.json') }}"
                            background="transparent"
                            speed="1"
                            style="width: 100%; height: 100%;"
                            loop
                            autoplay
                        ></dotlottie-player>
                    </div>
                    
                    <div class="text-center mt-6">
                        <h2 class="text-4xl font-bold text-[var(--color-reading-text)] mb-3 tracking-tight">
                            Tu Puerta a <span class="text-[var(--color-primary)]">Oriente</span>
                        </h2>
                        <p class="text-[var(--color-muted-text)] text-lg leading-relaxed">
                            Únete a la mayor comunidad de novelas ligeras y empieza a coleccionar tus historias favoritas.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Lado Derecho: Formulario --}}
            <div class="w-full max-w-xl mx-auto lg:mx-0">
                <div class="bg-[var(--color-surface)] rounded-[2.5rem] p-6 md:p-10 shadow-2xl border border-[var(--color-border)] relative overflow-hidden transition-colors duration-300">
                    
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-transparent via-[var(--color-primary)] to-transparent opacity-40"></div>

                    <div class="mb-8">
                        <h2 class="text-2xl md:text-3xl font-bold text-[var(--color-reading-text)] mb-2 flex items-center gap-3">
                            Crear Cuenta
                            <span class="text-[10px] uppercase px-3 py-1 rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)] border border-[var(--color-primary)]/20 font-bold tracking-wider">Gratis</span>
                        </h2>
                        <p class="text-[var(--color-muted-text)] text-sm">Rellena tus datos para empezar tu aventura.</p>
                    </div>

                    {{-- Errores Globales --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 animate-fade-in">
                            <ul class="space-y-1 text-xs text-red-500 font-medium">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-center gap-2">
                                        <span class="size-1 bg-red-500 rounded-full"></span> {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="space-y-5" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Nombre Completo --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1">Nombre Completo</label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] transition-colors material-symbols-outlined text-[20px]">person</span>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                                    class="w-full pl-12 pr-4 py-3 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                    placeholder="Ej. Juan Pérez" />
                            </div>
                        </div>

                        {{-- Grid Username & Email --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1">Usuario</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] material-symbols-outlined text-[20px]">badge</span>
                                    <input type="text" name="username" value="{{ old('username') }}" required
                                        class="w-full pl-12 pr-4 py-3 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                        placeholder="Kirito" />
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1">Email</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] material-symbols-outlined text-[20px]">mail</span>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                        class="w-full pl-12 pr-4 py-3 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                        placeholder="correo@ejemplo.com" />
                                </div>
                            </div>
                        </div>

                        {{-- Avatar --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1">Avatar (Opcional)</label>
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-[var(--color-border)] rounded-2xl cursor-pointer bg-[var(--color-background)] hover:bg-[var(--color-surface)] transition-all group/upload relative overflow-hidden">
                                <div class="flex flex-col items-center justify-center pt-2 pb-3 z-10">
                                    <span class="material-symbols-outlined text-[var(--color-muted-text)] group-hover/upload:text-[var(--color-primary)] transition-colors mb-1">cloud_upload</span>
                                    <p class="text-[10px] text-[var(--color-muted-text)]">Click para subir foto</p>
                                </div>
                                <input type="file" name="avatar" class="hidden" accept="image/*" onchange="previewAvatar(event)" />
                                <img id="avatar-preview" class="absolute inset-0 w-full h-full object-cover opacity-40 hidden" />
                            </label>
                        </div>

                        {{-- Passwords --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1">Contraseña</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] material-symbols-outlined text-[20px]">lock</span>
                                    <input type="password" name="password" required
                                        class="w-full pl-12 pr-4 py-3 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                        placeholder="••••••••" />
                                </div>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1">Confirmar</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] material-symbols-outlined text-[20px]">lock_reset</span>
                                    <input type="password" name="password_confirmation" required
                                        class="w-full pl-12 pr-4 py-3 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                        placeholder="••••••••" />
                                </div>
                            </div>
                        </div>

                        {{-- Términos --}}
                        <label class="flex items-start gap-3 cursor-pointer group w-fit">
                            <input type="checkbox" name="terms" required class="mt-1 size-5 rounded border-[var(--color-border)] bg-[var(--color-background)] text-[var(--color-primary)] focus:ring-[var(--color-primary)] transition-all">
                            <span class="text-xs text-[var(--color-muted-text)] leading-snug group-hover:text-[var(--color-reading-text)] transition-colors">
                                Acepto los <a href="#" class="text-[var(--color-primary)] font-bold hover:underline">Términos</a> y la <a href="#" class="text-[var(--color-primary)] font-bold hover:underline">Privacidad</a>.
                            </span>
                        </label>

                        <button type="submit" class="w-full bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white font-bold py-4 rounded-2xl shadow-lg transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">person_add</span>
                            Crear Cuenta
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-[var(--color-border)] text-center">
                        <p class="text-sm text-[var(--color-muted-text)]">
                            ¿Ya eres parte? 
                            <a href="{{ route('login') }}" class="text-[var(--color-primary)] font-bold hover:underline ml-1">Inicia Sesión</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
    <script>
        function previewAvatar(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('avatar-preview');
                output.src = reader.result;
                output.classList.remove('hidden');
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endpush