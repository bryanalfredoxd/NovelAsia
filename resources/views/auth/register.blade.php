@extends('layouts.app')

@section('title', 'Únete a NovelAsia')

@section('content')
    {{-- Fondo con degradado y textura --}}
    <div class="fixed inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 -z-10"></div>
    <div class="fixed inset-0 bg-[url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568')] bg-cover bg-center opacity-5 -z-10"></div>

    {{-- Contenedor Principal (Min-h-screen para evitar cortes en móvil) --}}
    <main class="min-h-screen pt-24 pb-12 flex items-center justify-center px-4 md:px-8">
        <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            
            {{-- Lado Izquierdo: Ilustración (Oculto en móvil para priorizar formulario) --}}
            <div class="hidden lg:flex flex-col items-center justify-center h-full animate-fade-in-up">
                <div class="w-full max-w-lg relative">
                    {{-- Efecto de brillo detrás de la animación --}}
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-primary/20 rounded-full blur-3xl -z-10"></div>
                    
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
                    
                    <div class="text-center mt-6">
                        <h2 class="text-4xl font-display font-bold text-white mb-3 tracking-tight">
                            Tu Puerta a <span class="text-primary">Oriente</span>
                        </h2>
                        <p class="text-slate-400 text-lg leading-relaxed">
                            Únete a la comunidad más grande de traducción de novelas ligeras en español.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Lado Derecho: Formulario de Registro --}}
            <div class="w-full max-w-lg mx-auto lg:mx-0">
                <div class="glass rounded-3xl p-6 md:p-8 shadow-2xl relative overflow-hidden group">
                    
                    {{-- Borde superior brillante --}}
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/50 to-transparent opacity-50"></div>

                    {{-- Encabezado del Formulario --}}
                    <div class="mb-8">
                        <h2 class="text-2xl md:text-3xl font-display font-bold text-white mb-2 flex items-center gap-3">
                            Crear Cuenta
                            <span class="text-xs font-sans font-normal px-2 py-1 rounded-full bg-primary/10 text-primary border border-primary/20">Gratis</span>
                        </h2>
                        <p class="text-slate-400 text-sm">Rellena tus datos para empezar tu aventura.</p>
                    </div>

                    {{-- Formulario (Con Multipart para Avatar) --}}
                    <form class="space-y-5" id="register-form" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- 1. Nombre Completo (Nuevo Campo) --}}
                        <div class="group/field">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 group-focus-within/field:text-primary transition-colors" for="nombre">
                                Nombre Completo
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 material-symbols-outlined text-[20px]">person</span>
                                <input 
                                    type="text" 
                                    id="nombre" 
                                    name="nombre"
                                    value="{{ old('nombre') }}"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-950/50 border @error('nombre') border-red-500 @else border-white/10 @enderror rounded-xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none transition-all hover:border-white/20"
                                    placeholder="Ej. Juan Pérez"
                                    required
                                />
                            </div>
                            @error('nombre') <p class="text-red-400 text-xs mt-1 ml-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Grid para Username y Email en pantallas grandes --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- 2. Username --}}
                            <div class="group/field">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 group-focus-within/field:text-primary transition-colors" for="username">
                                    Usuario
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 material-symbols-outlined text-[20px]">badge</span>
                                    <input 
                                        type="text" 
                                        id="username" 
                                        name="username"
                                        value="{{ old('username') }}"
                                        class="w-full pl-11 pr-4 py-3 bg-slate-950/50 border @error('username') border-red-500 @else border-white/10 @enderror rounded-xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none transition-all hover:border-white/20"
                                        placeholder="Nick"
                                        required
                                    />
                                </div>
                                @error('username') <p class="text-red-400 text-xs mt-1 ml-1 font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- 3. Email --}}
                            <div class="group/field">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 group-focus-within/field:text-primary transition-colors" for="email">
                                    Email
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 material-symbols-outlined text-[20px]">mail</span>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email"
                                        value="{{ old('email') }}"
                                        class="w-full pl-11 pr-4 py-3 bg-slate-950/50 border @error('email') border-red-500 @else border-white/10 @enderror rounded-xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none transition-all hover:border-white/20"
                                        placeholder="correo@ej.com"
                                        required
                                    />
                                </div>
                                @error('email') <p class="text-red-400 text-xs mt-1 ml-1 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- 4. Avatar Upload (Estilizado) --}}
                        <div class="group/field">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5" for="avatar">
                                Avatar (Opcional)
                            </label>
                            <input 
                                type="file" 
                                id="avatar" 
                                name="avatar"
                                accept="image/*"
                                class="w-full px-3 py-2 bg-slate-950/50 border border-white/10 rounded-xl text-sm text-slate-300 
                                file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold 
                                file:bg-primary/10 file:text-primary hover:file:bg-primary/20 
                                cursor-pointer focus:outline-none focus:border-primary/50 transition-all"
                            />
                            <p class="text-[10px] text-slate-500 mt-1 ml-1">Máximo 8MB. Formatos: JPG, PNG, WEBP.</p>
                            @error('avatar') <p class="text-red-400 text-xs mt-1 ml-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- 5. Password --}}
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
                                <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors p-1">
                                    <span class="material-symbols-outlined text-[20px]" id="eye-icon">visibility_off</span>
                                </button>
                            </div>
                            @error('password') <p class="text-red-400 text-xs mt-1 ml-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- 6. Confirm Password --}}
                        <div class="group/field">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 group-focus-within/field:text-primary transition-colors" for="password_confirmation">
                                Confirmar Contraseña
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 material-symbols-outlined text-[20px]">lock_reset</span>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-950/50 border border-white/10 rounded-xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 focus:outline-none transition-all hover:border-white/20"
                                    placeholder="••••••••"
                                    required
                                />
                            </div>
                        </div>

                        {{-- 7. Términos --}}
                        <div class="flex items-start gap-3 pt-2">
                            <div class="relative flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="terms" 
                                    name="terms"
                                    class="peer size-5 rounded border-white/20 bg-slate-950/50 text-primary focus:ring-2 focus:ring-primary/50 focus:ring-offset-0 cursor-pointer transition-all checked:bg-primary checked:border-primary"
                                    required
                                />
                            </div>
                            <label for="terms" class="text-sm text-slate-400 cursor-pointer select-none leading-tight">
                                He leído y acepto los <a href="#" class="text-white hover:text-primary hover:underline font-semibold transition-colors">Términos de Servicio</a> y la <a href="#" class="text-white hover:text-primary hover:underline font-semibold transition-colors">Política de Privacidad</a>.
                            </label>
                        </div>
                        @error('terms') <p class="text-red-400 text-xs ml-8 font-medium">Debes aceptar los términos.</p> @enderror

                        {{-- Botón Submit (Con estilo Sakura) --}}
                        <button 
                            type="submit"
                            class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-4 rounded-xl transition-all sakura-glow flex items-center justify-center gap-2 text-base mt-2 transform active:scale-[0.98]"
                        >
                            <span class="material-symbols-outlined">person_add</span>
                            Crear mi Cuenta
                        </button>
                    </form>

                    {{-- Login Link --}}
                    <div class="text-center mt-8 pt-6 border-t border-white/5">
                        <p class="text-sm text-slate-400">
                            ¿Ya tienes una cuenta? 
                            <a href="{{ route('login') }}" class="text-primary hover:text-white font-semibold ml-1 transition-colors hover:underline">Inicia sesión</a>
                        </p>
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
        // Lógica para mostrar/ocultar contraseña
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (togglePassword && passwordInput && eyeIcon) {
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.textContent = type === 'password' ? 'visibility_off' : 'visibility';
                
                // Efecto visual al cambiar
                eyeIcon.classList.add('scale-110');
                setTimeout(() => eyeIcon.classList.remove('scale-110'), 200);
            });
        }
    </script>
@endpush