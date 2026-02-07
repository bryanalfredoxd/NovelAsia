@extends('layouts.app')

@section('title', 'Registro - NovelAsia')

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
                            <lottie-player
                                id="lottie-animation"
                                src="https://assets9.lottiefiles.com/packages/lf20_1a8dx7zj.json"
                                background="transparent"
                                speed="1"
                                style="width: 100%; height: 100%;"
                                loop
                                autoplay
                            ></lottie-player>
                        </div>
                        
                        <div class="text-center mt-8">
                            <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-4">
                                Únete a Nuestra Comunidad
                            </h2>
                            <p class="text-slate-400 text-lg">
                                Descubre miles de novelas ligeras y conecta con lectores de todo el mundo
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Right Side - Register Form --}}
                <div class="w-full max-w-md mx-auto lg:mx-0 overflow-y-auto h-full flex items-center py-8">
                    <div class="w-full glass rounded-3xl p-6 md:p-10 shadow-2xl">
                        
                        {{-- Mobile Header --}}
                        <div class="lg:hidden text-center mb-8">
                            <div class="inline-flex items-center justify-center size-16 bg-primary/20 rounded-2xl mb-4 border border-primary/30">
                                <span class="material-symbols-outlined text-4xl text-primary">person_add</span>
                            </div>
                            <h2 class="text-2xl md:text-3xl font-display font-bold text-white mb-2">Crear Cuenta</h2>
                            <p class="text-slate-400 text-sm">Únete a la comunidad NovelAsia</p>
                        </div>

                        {{-- Desktop Header --}}
                        <div class="hidden lg:block mb-8">
                            <h2 class="text-3xl font-display font-bold text-white mb-2">Crear Cuenta</h2>
                            <p class="text-slate-400 text-sm">Completa tus datos para comenzar</p>
                        </div>

                        {{-- Form --}}
                        <form class="space-y-5" id="register-form" action="/register" method="POST">
                            @csrf
                            
                            {{-- Username --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-300 mb-2" for="username">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-primary">badge</span>
                                        Nombre de Usuario
                                    </span>
                                </label>
                                <input 
                                    type="text" 
                                    id="username" 
                                    name="username"
                                    class="w-full px-4 py-3 bg-slate-800/50 border border-white/10 rounded-xl text-white placeholder:text-slate-500 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none transition-all"
                                    placeholder="Elige un nombre de usuario"
                                    required
                                />
                            </div>

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
                                        placeholder="Crea una contraseña segura"
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

                            {{-- Confirm Password --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-300 mb-2" for="password_confirmation">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-primary">lock_reset</span>
                                        Confirmar Contraseña
                                    </span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation"
                                    class="w-full px-4 py-3 bg-slate-800/50 border border-white/10 rounded-xl text-white placeholder:text-slate-500 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none transition-all"
                                    placeholder="Confirma tu contraseña"
                                    required
                                />
                            </div>

                            {{-- Terms Checkbox --}}
                            <div class="flex items-start gap-3 pt-2">
                                <input 
                                    type="checkbox" 
                                    id="terms" 
                                    name="terms"
                                    class="mt-1 size-4 rounded border-white/10 bg-slate-800/50 text-primary focus:ring-2 focus:ring-primary/50 focus:ring-offset-0 cursor-pointer"
                                    required
                                />
                                <label for="terms" class="text-sm text-slate-400 cursor-pointer">
                                    Acepto los <a href="#" class="text-primary hover:underline font-semibold">Términos de Servicio</a> y la <a href="#" class="text-primary hover:underline font-semibold">Política de Privacidad</a>
                                </label>
                            </div>

                            {{-- Submit Button --}}
                            <button 
                                type="submit"
                                class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3.5 rounded-xl transition-all sakura-glow flex items-center justify-center gap-2 text-base mt-6"
                            >
                                <span class="material-symbols-outlined">person_add</span>
                                Crear Cuenta
                            </button>

                        </form>


                        {{-- Login Link --}}
                        <div class="text-center mt-6">
                            <p class="text-sm text-slate-400">
                                ¿Ya tienes una cuenta? 
                                <a href="/login" class="text-primary hover:underline font-semibold ml-1">Inicia sesión aquí</a>
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
     {{-- Lottie Player --}}
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <script>
        // Password toggle visibility (Reused logic, could be in a shared script)
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
    </script>
@endpush
