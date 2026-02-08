@extends('layouts.app')

@section('title', 'Iniciar Sesión - NovelAsia')

@section('content')
    {{-- Fondo dinámico: usa las variables del tema --}}
    <div class="fixed inset-0 bg-[var(--color-background)] -z-10 transition-colors duration-300"></div>
    <div class="fixed inset-0 bg-[url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568')] bg-cover bg-center opacity-[0.03] dark:opacity-[0.05] -z-10"></div>

    <main class="min-h-screen pt-20 pb-12 flex items-center justify-center px-4 md:px-8">
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            
            {{-- Lado Izquierdo: Animación (Solo Desktop) --}}
            <div class="hidden lg:flex flex-col items-center justify-center animate-fade-in">
                <div class="w-full max-w-md relative">
                    {{-- Brillo ambiental dinámico --}}
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-[var(--color-primary)]/10 rounded-full blur-3xl -z-10"></div>
                    
                    <div id="lottie-container" class="w-full aspect-square drop-shadow-2xl">
                        <dotlottie-player
                            id="lottie-animation"
                            src="https://lottie.host/e9150446-f80c-4b33-a361-58e339d1dc1d/9NVtOMZI4C.lottie"
                            background="transparent"
                            speed="1"
                            style="width: 100%; height: 100%;"
                            loop
                            autoplay
                        ></dotlottie-player>
                    </div>
                    
                    <div class="text-center mt-4">
                        <h2 class="text-4xl font-bold text-[var(--color-reading-text)] mb-3">
                            Bienvenido de Vuelta
                        </h2>
                        <p class="text-[var(--color-muted-text)] text-lg">
                            Tu próxima gran historia te está esperando.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Lado Derecho: Formulario Login --}}
            <div class="w-full max-w-md mx-auto">
                <div class="bg-[var(--color-surface)] rounded-[2.5rem] p-8 md:p-12 shadow-2xl border border-[var(--color-border)] relative overflow-hidden">
                    
                    {{-- Indicador de marca --}}
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-transparent via-[var(--color-primary)] to-transparent opacity-40"></div>

                    <div class="mb-10 text-center">
                        <div class="lg:hidden size-16 bg-[var(--color-primary)]/10 rounded-2xl flex items-center justify-center mx-auto mb-4 text-[var(--color-primary)]">
                            <span class="material-symbols-outlined text-4xl">login</span>
                        </div>
                        <h2 class="text-3xl font-bold text-[var(--color-reading-text)]">Entrar</h2>
                        <p class="text-[var(--color-muted-text)] text-sm mt-2">Accede a tu biblioteca personal</p>
                    </div>

                    <form class="space-y-6" id="login-form" action="{{ route('login') }}" method="POST">
                        @csrf
                        
                        {{-- Identidad --}}
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)] ml-1" for="identity">
                                Usuario o Correo
                            </label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] transition-colors material-symbols-outlined">person</span>
                                <input 
                                    type="text" id="identity" name="identity" value="{{ old('identity') }}" required autofocus
                                    class="w-full pl-12 pr-4 py-4 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all placeholder:text-[var(--color-muted-text)]/50"
                                    placeholder="Nombre de usuario"
                                />
                            </div>
                            @error('identity')
                                <span class="text-red-500 text-xs font-medium flex items-center gap-1 ml-1">
                                    <span class="material-symbols-outlined text-sm">error</span> {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="space-y-2">
                            <div class="flex justify-between items-center ml-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted-text)]" for="password">Contraseña</label>
                                <a href="#" class="text-xs font-bold text-[var(--color-primary)] hover:underline">¿Olvidaste la clave?</a>
                            </div>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] group-focus-within:text-[var(--color-primary)] transition-colors material-symbols-outlined">lock</span>
                                <input 
                                    type="password" id="password" name="password" required
                                    class="w-full pl-12 pr-12 py-4 bg-[var(--color-background)] border border-[var(--color-border)] rounded-2xl text-[var(--color-reading-text)] focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] outline-none transition-all"
                                    placeholder="••••••••"
                                />
                                <button type="button" id="toggle-password" class="absolute right-4 top-1/2 -translate-y-1/2 text-[var(--color-muted-text)] hover:text-[var(--color-primary)] transition-colors">
                                    <span class="material-symbols-outlined text-xl" id="eye-icon">visibility_off</span>
                                </button>
                            </div>
                        </div>

                        {{-- Recordarme --}}
                        <label class="flex items-center gap-3 cursor-pointer group w-fit">
                            <input type="checkbox" name="remember" class="size-5 rounded-lg border-[var(--color-border)] bg-[var(--color-background)] text-[var(--color-primary)] focus:ring-[var(--color-primary)] transition-all cursor-pointer">
                            <span class="text-sm text-[var(--color-muted-text)] group-hover:text-[var(--color-reading-text)]">Recordar sesión</span>
                        </label>

                        {{-- Submit --}}
                        <button type="submit" class="w-full bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white font-bold py-4 rounded-2xl shadow-lg shadow-[var(--color-primary)]/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">login</span>
                            Iniciar Sesión
                        </button>
                    </form>

                    {{-- Registro --}}
                    <div class="mt-10 text-center">
                        <p class="text-sm text-[var(--color-muted-text)]">
                            ¿Nuevo en NovelAsia? 
                            <a href="{{ route('register') }}" class="text-[var(--color-primary)] font-bold hover:underline ml-1">Crea una cuenta</a>
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
        // Toggle Password
        const passInput = document.getElementById('password');
        const toggleBtn = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');
        
        toggleBtn?.addEventListener('click', () => {
            const isPass = passInput.type === 'password';
            passInput.type = isPass ? 'text' : 'password';
            eyeIcon.textContent = isPass ? 'visibility' : 'visibility_off';
        });

        // Lottie Focus Effect
        const player = document.getElementById('lottie-animation');
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', () => player?.play());
        });
    </script>
@endpush