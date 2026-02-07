@extends('layouts.app')

@section('title', 'Iniciar Sesión - NovelAsia')

@section('content')
    {{-- Background Pattern (Specific for Auth pages) --}}
    <div class="fixed inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 -z-10"></div>
    <div class="fixed inset-0 bg-[url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568')] bg-cover bg-center opacity-5 -z-10"></div>

    <main class="pt-20 h-screen flex items-center justify-center px-4 md:px-8">
        <div class="w-full max-w-7xl h-[calc(100vh-120px)] flex items-center">
            
            {{-- Split Screen Container --}}
            <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center h-full">
                
                {{-- Left Side - Animated Illustration --}}
                <div class="hidden lg:flex flex-col items-center justify-center h-full">
                    <div class="w-full max-w-lg">
                        {{-- Lottie Animation Container --}}
                        <div id="lottie-container" class="w-full aspect-square flex items-center justify-center">
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
                        
                        <div class="text-center mt-8">
                            <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-4">
                                Bienvenido de Vuelta
                            </h2>
                            <p class="text-slate-400 text-lg">
                                Continúa tu aventura en el mundo de las novelas ligeras
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Right Side - Login Form --}}
                <div class="w-full max-w-md mx-auto lg:mx-0 overflow-y-auto h-full flex items-center py-8">
                    <div class="w-full glass rounded-3xl p-6 md:p-10 shadow-2xl">
                        
                        {{-- Mobile Header --}}
                        <div class="lg:hidden text-center mb-8">
                            <div class="inline-flex items-center justify-center size-16 bg-primary/20 rounded-2xl mb-4 border border-primary/30">
                                <span class="material-symbols-outlined text-4xl text-primary">login</span>
                            </div>
                            <h2 class="text-2xl md:text-3xl font-display font-bold text-white mb-2">Iniciar Sesión</h2>
                            <p class="text-slate-400 text-sm">Accede a tu cuenta</p>
                        </div>

                        {{-- Desktop Header --}}
                        <div class="hidden lg:block mb-8">
                            <h2 class="text-3xl font-display font-bold text-white mb-2">Iniciar Sesión</h2>
                            <p class="text-slate-400 text-sm">Ingresa tus credenciales para continuar</p>
                        </div>

                        {{-- Form --}}
                        <form class="space-y-5" id="login-form" action="/login" method="POST">
                            @csrf
                            
                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-300 mb-2" for="email">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-primary">mail</span>
                                        Correo Electrónico
                                    </span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email"
                                    class="w-full px-4 py-3 bg-slate-800/50 border border-white/10 rounded-xl text-white placeholder:text-slate-500 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none transition-all"
                                    placeholder="tu@email.com"
                                    required
                                />
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-300 mb-2" for="password">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-primary">lock</span>
                                        Contraseña
                                    </span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password"
                                        class="w-full px-4 py-3 bg-slate-800/50 border border-white/10 rounded-xl text-white placeholder:text-slate-500 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none transition-all"
                                        placeholder="Tu contraseña"
                                        required
                                    />
                                    <button 
                                        type="button"
                                        id="toggle-password"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition-colors"
                                    >
                                        <span class="material-symbols-outlined text-xl" id="eye-icon">visibility_off</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Remember & Forgot --}}
                            <div class="flex items-center justify-between pt-2">
                                <div class="flex items-center gap-2">
                                    <input 
                                        type="checkbox" 
                                        id="remember" 
                                        name="remember"
                                        class="size-4 rounded border-white/10 bg-slate-800/50 text-primary focus:ring-2 focus:ring-primary/50 focus:ring-offset-0 cursor-pointer"
                                    />
                                    <label for="remember" class="text-sm text-slate-400 cursor-pointer">
                                        Recordarme
                                    </label>
                                </div>
                                <a href="#" class="text-sm text-primary hover:underline font-semibold">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>

                            {{-- Submit Button --}}
                            <button 
                                type="submit"
                                class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3.5 rounded-xl transition-all sakura-glow flex items-center justify-center gap-2 text-base mt-6"
                            >
                                <span class="material-symbols-outlined">login</span>
                                Iniciar Sesión
                            </button>

                        </form>

                        {{-- Register Link --}}
                        <div class="text-center mt-6">
                            <p class="text-sm text-slate-400">
                                ¿No tienes una cuenta? 
                                <a href="/register" class="text-primary hover:underline font-semibold ml-1">Regístrate aquí</a>
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
    {{-- DotLottie Player (para .lottie files) --}}
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
    <script>
        // Password toggle visibility
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (togglePassword && passwordInput && eyeIcon) {
             togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.textContent = type === 'password' ? 'visibility_off' : 'visibility';
            });
        }

        // Lottie animation control
        const lottiePlayer = document.getElementById('lottie-animation');
        const formInputs = document.querySelectorAll('#login-form input');
        
        formInputs.forEach(input => {
            input.addEventListener('focus', () => {
                if (lottiePlayer) {
                    lottiePlayer.play();
                }
            });
        });
    </script>
@endpush
