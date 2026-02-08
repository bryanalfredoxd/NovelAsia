@extends('layouts.app')

@section('title', 'Únete a NovelAsia')

@section('content')
    {{-- Fondo dinámico --}}
    <div class="fixed inset-0 bg-[var(--color-background)] -z-10 transition-colors duration-300"></div>
    <div class="fixed inset-0 bg-[url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568')] bg-cover bg-center opacity-[0.03] dark:opacity-[0.05] -z-10"></div>

    <main class="min-h-screen pt-24 pb-12 flex items-center justify-center px-4 md:px-8">
        <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            
            {{-- Lado Izquierdo: Ilustración --}}
            <div class="hidden lg:flex flex-col items-center justify-center animate-fade-in">
                <div class="w-full max-w-lg relative">
                    {{-- Brillo ambiental dinámico --}}
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-[var(--color-primary)]/10 rounded-full blur-3xl -z-10"></div>
                    
                    <div class="w-full aspect-square flex items-center justify-center drop-shadow-2xl">
                        {{-- Usamos dotlottie-player porque es el estándar más moderno y estable --}}
                        <dotlottie-player
                            id="lottie-animation"
                            src="{{ asset('assets/animations/books.json') }}" {{-- Ruta local --}}
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
                <div class="bg-[var(--color-surface)] rounded-[2.5rem] p-6 md:p-10 shadow-2xl border border-[var(--color-border)] relative overflow-hidden">
                    
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-transparent via-[var(--color-primary)] to-transparent opacity-40"></div>

                    <div class="mb-8">
                        <h2 class="text-2xl md:text-3xl font-bold text-[var(--color-reading-text)] mb-2 flex items-center gap-3">
                            Crear Cuenta
                            <span class="text-[10px] uppercase px-3 py-1 rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)] border border-[var(--color-primary)]/20">Gratis</span>
                        </h2>
                        <p class="text-[var(--color-muted-text)] text-sm">Rellena tus datos para empezar tu aventura.</p>
                    </div>

                    <form class="space-y-5" id="register-form" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Nombre Completo --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1" for="nombre">Nombre Completo</label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] material-symbols-outlined text-[20px]">person</span>
                                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required
                                    class="w-full pl-12 pr-4 py-3 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                    placeholder="Ej. Juan Pérez" />
                            </div>
                            @error('nombre') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        {{-- Grid Username & Email --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1" for="username">Usuario</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] material-symbols-outlined text-[20px]">badge</span>
                                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                                        class="w-full pl-12 pr-4 py-3 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                        placeholder="Kirito" />
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1" for="email">Email</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] material-symbols-outlined text-[20px]">mail</span>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                        class="w-full pl-12 pr-4 py-3 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                        placeholder="correo@ejemplo.com" />
                                </div>
                            </div>
                        </div>

                        {{-- Avatar --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1">Avatar (Opcional)</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-[var(--color-border)] rounded-2xl cursor-pointer bg-[var(--color-background)] hover:bg-[var(--color-surface)] transition-all group">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <span class="material-symbols-outlined text-[var(--color-muted-text)] group-hover:text-[var(--color-primary)]">cloud_upload</span>
                                        <p class="text-xs text-[var(--color-muted-text)]">JPG, PNG o WEBP (Máx. 8MB)</p>
                                    </div>
                                    <input type="file" name="avatar" class="hidden" accept="image/*" />
                                </label>
                            </div>
                        </div>

                        {{-- Passwords --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1" for="password">Contraseña</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] material-symbols-outlined text-[20px]">lock</span>
                                    <input type="password" id="password" name="password" required
                                        class="w-full pl-12 pr-4 py-3 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                        placeholder="••••••••" />
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1" for="password_confirmation">Confirmar</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] material-symbols-outlined text-[20px]">lock_reset</span>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required
                                        class="w-full pl-12 pr-4 py-3 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                        placeholder="••••••••" />
                                </div>
                            </div>
                        </div>

                        {{-- Términos --}}
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" name="terms" required class="mt-1 size-5 rounded border-[var(--color-border)] bg-[var(--color-background)] text-[var(--color-primary)] focus:ring-[var(--color-primary)] transition-all">
                            <span class="text-xs text-[var(--color-muted-text)] leading-snug">
                                Acepto los <a href="#" class="text-[var(--color-primary)] font-bold hover:underline">Términos de Servicio</a> y la <a href="#" class="text-[var(--color-primary)] font-bold hover:underline">Política de Privacidad</a> de NovelAsia.
                            </span>
                        </label>

                        <button type="submit" class="w-full bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white font-bold py-4 rounded-2xl shadow-lg transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">person_add</span>
                            Crear Cuenta
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-[var(--color-border)] text-center">
                        <p class="text-sm text-[var(--color-muted-text)]">
                            ¿Ya eres parte de NovelAsia? 
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
@endpush