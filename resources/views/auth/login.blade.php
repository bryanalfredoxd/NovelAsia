@extends('layouts.app')

@section('title', 'Iniciar Sesión - NovelAsia')

@section('content')
    {{-- Background Pattern --}}
    <div class="fixed inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 -z-10"></div>
    <div class="fixed inset-0 bg-[url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568')] bg-cover bg-center opacity-5 -z-10"></div>

    <main class="min-h-screen pt-24 pb-12 flex items-center justify-center px-4 md:px-8">
        <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            
            {{-- Lado Izquierdo: Animación --}}
            <div class="hidden lg:flex flex-col items-center justify-center h-full animate-fade-in-up">
                <div class="w-full max-w-lg relative">
                    {{-- Brillo ambiental --}}
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-primary/20 rounded-full blur-3xl -z-10"></div>
                    
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
                    
                    <div class="text-center mt-6">
                        <h2 class="text-4xl font-display font-bold text-white mb-3">
                            Bienvenido de Vuelta
                        </h2>
                        <p class="text-slate-400 text-lg">
                            Continúa tu aventura en el mundo de las novelas ligeras.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Lado Derecho: Formulario Login --}}
            <div class="w-full max-w-md mx-auto lg:mx-0">
                <div class="glass rounded-3xl p-6 md:p-10 shadow-2xl relative overflow-hidden group">
                    
                    {{-- Borde superior brillante --}}
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/50 to-transparent opacity-50"></div>

                    {{-- Header --}}
                    <div class="mb-8 text-center lg:text-left">
                        <h2 class="text-3xl font-display font-bold text-white mb-2 flex items-center justify-center lg:justify-start gap-3">
                            <span class="lg:hidden material-symbols-outlined text-primary text-3xl">login</span>
                            Iniciar Sesión
                        </h2>
                        <p class="text-slate-400 text-sm">Ingresa tus credenciales para continuar</p>
                    </div>

                    {{-- Form --}}
                    <form class="space-y-6" id="login-form" action="{{ route('login') }}" method="POST">
                        @csrf
                        
 {{-- Usuario o Email --}}
                        <div class="group/field">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 group-focus-within/field:text-primary transition-colors" for="identity">
                                Usuario o Correo
                            </label>
                            <div class="relative">
                                {{-- Icono dinámico (cambia mentalmente para el usuario, es visual) --}}
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 material-symbols-outlined text-[20px]">person</span>
                                
                                <input 
                                    type="text" 
                                    id="identity" 
                                    name="identity" 
                                    value="{{ old('identity') }}"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-950/50 border @error('identity') border-red-500 @else border-white/10 @enderror rounded-xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none transition-all hover:border-white/20"
                                    placeholder="Ej: Kirito o kirito@sao.com"
                                    required
                                    autofocus
                                />
                            </div>
                            @error('identity')
                                <p class="text-red-400 text-xs mt-1 ml-1 font-medium flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="group/field">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 group-focus-within/field:text-primary transition-colors" for="password">
                                Contraseña
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 material-symbols-outlined text-[20px]">lock</span>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password"
                                    class="w-full pl-11 pr-12 py-3 bg-slate-950/50 border @error('password') border-red-500 @else border-white/10 @enderror rounded-xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none transition-all hover:border-white/20"
                                    placeholder="••••••••"
                                    required
                                />
                                <button 
                                    type="button"
                                    id="toggle-password"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors p-1"
                                >
                                    <span class="material-symbols-outlined text-[20px]" id="eye-icon">visibility_off</span>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-400 text-xs mt-1 ml-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember & Forgot --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <input 
                                    type="checkbox" 
                                    id="remember" 
                                    name="remember"
                                    class="peer size-4 rounded border-white/20 bg-slate-950/50 text-primary focus:ring-2 focus:ring-primary/50 focus:ring-offset-0 cursor-pointer transition-all checked:bg-primary checked:border-primary"
                                />
                                <label for="remember" class="text-sm text-slate-400 cursor-pointer select-none peer-checked:text-white transition-colors">
                                    Recordarme
                                </label>
                            </div>
                            <a href="#" class="text-sm text-primary hover:text-white transition-colors font-semibold hover:underline">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>

                        {{-- Submit Button --}}
                        <button 
                            type="submit"
                            class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-4 rounded-xl transition-all sakura-glow flex items-center justify-center gap-2 text-base mt-2 transform active:scale-[0.98]"
                        >
                            <span class="material-symbols-outlined">login</span>
                            Iniciar Sesión
                        </button>

                    </form>

                    {{-- Register Link --}}
                    <div class="text-center mt-8 pt-6 border-t border-white/5">
                        <p class="text-sm text-slate-400">
                            ¿No tienes una cuenta? 
                            <a href="{{ route('register') }}" class="text-primary hover:text-white font-semibold ml-1 transition-colors hover:underline">Regístrate gratis</a>
                        </p>
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
                
                // Efecto visual
                eyeIcon.classList.add('scale-110');
                setTimeout(() => eyeIcon.classList.remove('scale-110'), 200);
            });
        }

        // Lottie animation control on Input Focus
        const lottiePlayer = document.getElementById('lottie-animation');
        const formInputs = document.querySelectorAll('#login-form input');
        
        formInputs.forEach(input => {
            input.addEventListener('focus', () => {
                if (lottiePlayer && lottiePlayer.play) {
                    lottiePlayer.play();
                }
            });
        });
    </script>
@endpush