@extends('layouts.app')

@section('title', 'Únete a NovelAsia')

@section('content')
    {{-- Fondo dinámico --}}
    <div class="fixed inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 -z-10"></div>
    <div class="fixed inset-0 bg-[url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2568')] bg-cover bg-center opacity-5 -z-10"></div>

    <main class="min-h-screen pt-24 pb-12 flex items-center justify-center px-4 md:px-8">
        <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            
            {{-- Lado Izquierdo: Ilustración --}}
            <div class="hidden lg:flex flex-col items-center justify-center animate-fade-in-up">
                <div class="w-full max-w-lg relative">
                    {{-- Brillo ambiental dinámico --}}
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-primary/20 rounded-full blur-3xl -z-10"></div>
                    
                    <div id="lottie-container" class="w-full aspect-square flex items-center justify-center drop-shadow-2xl">
                        <dotlottie-player
                            src="https://lottie.host/e9150446-f80c-4b33-a361-58e339d1dc1d/9NVtOMZI4C.lottie"
                            background="transparent"
                            speed="1"
                            style="width: 100%; height: 100%;"
                            loop
                            autoplay
                        ></dotlottie-player>
                    </div>
                    
                    <div class="text-center mt-6">
                        <h2 class="text-4xl font-display font-bold text-white mb-3 tracking-tight">
                            Tu Puerta a <span class="text-primary">Oriente</span>
                        </h2>
                        <p class="text-slate-400 text-lg leading-relaxed">
                            Únete a la mayor comunidad de novelas ligeras y empieza a coleccionar tus historias favoritas.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Lado Derecho: Formulario --}}
            <div class="w-full max-w-xl mx-auto lg:mx-0">
                <div class="glass rounded-[2.5rem] p-6 md:p-10 shadow-2xl border border-white/5 relative overflow-hidden group">
                    
                    {{-- Borde superior brillante --}}
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/50 to-transparent opacity-50"></div>

                    <div class="mb-6">
                        <h2 class="text-2xl md:text-3xl font-display font-bold text-white mb-2 flex items-center gap-3">
                            Crear Cuenta
                            <span class="text-[10px] uppercase px-3 py-1 rounded-full bg-primary/10 text-primary border border-primary/20 font-sans tracking-wider">Gratis</span>
                        </h2>
                        <p class="text-slate-400 text-sm">Rellena tus datos para empezar tu aventura.</p>
                    </div>

                    {{-- === ZONA DE ERRORES GLOBAL === --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 backdrop-blur-sm animate-pulse-fast">
                            <div class="flex items-center gap-2 mb-2 text-red-400 font-bold text-sm">
                                <span class="material-symbols-outlined text-lg">error</span>
                                <span>Por favor corrige los siguientes errores:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1 text-xs text-red-300 ml-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="space-y-5" id="register-form" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Nombre Completo --}}
                        <div class="group/field">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 ml-1 transition-colors group-focus-within/field:text-primary">Nombre Completo</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within/field:text-primary transition-colors material-symbols-outlined text-[20px]">person</span>
                                <input 
                                    type="text" 
                                    name="nombre" 
                                    value="{{ old('nombre') }}" 
                                    class="w-full pl-12 pr-4 py-3 bg-slate-900/50 border @error('nombre') border-red-500/50 @else border-white/10 @enderror rounded-2xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 outline-none transition-all hover:border-white/20"
                                    placeholder="Ej. Juan Pérez" 
                                    required
                                />
                            </div>
                            @error('nombre') 
                                <p class="text-red-400 text-xs mt-1 ml-2 font-medium flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">cancel</span> {{ $message }}
                                </p> 
                            @enderror
                        </div>

                        {{-- Grid Username & Email --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Username --}}
                            <div class="group/field">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 ml-1 transition-colors group-focus-within/field:text-primary">Usuario</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within/field:text-primary transition-colors material-symbols-outlined text-[20px]">badge</span>
                                    <input 
                                        type="text" 
                                        name="username" 
                                        value="{{ old('username') }}" 
                                        class="w-full pl-12 pr-4 py-3 bg-slate-900/50 border @error('username') border-red-500/50 @else border-white/10 @enderror rounded-2xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 outline-none transition-all hover:border-white/20"
                                        placeholder="Kirito" 
                                        required
                                    />
                                </div>
                                @error('username') <p class="text-red-400 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="group/field">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 ml-1 transition-colors group-focus-within/field:text-primary">Email</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within/field:text-primary transition-colors material-symbols-outlined text-[20px]">mail</span>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        class="w-full pl-12 pr-4 py-3 bg-slate-900/50 border @error('email') border-red-500/50 @else border-white/10 @enderror rounded-2xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 outline-none transition-all hover:border-white/20"
                                        placeholder="correo@ejemplo.com" 
                                        required
                                    />
                                </div>
                                @error('email') <p class="text-red-400 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Avatar --}}
                        <div class="group/field">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 ml-1">Avatar (Opcional)</label>
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed @error('avatar') border-red-500/30 bg-red-500/5 @else border-white/10 bg-slate-900/30 @enderror rounded-2xl cursor-pointer hover:bg-slate-800 transition-all group/upload relative overflow-hidden">
                                <div class="flex flex-col items-center justify-center pt-2 pb-3 z-10">
                                    <span class="material-symbols-outlined text-slate-500 group-hover/upload:text-primary transition-colors mb-1">cloud_upload</span>
                                    <p class="text-[10px] text-slate-400">Click para subir (JPG, PNG, WEBP - Max 8MB)</p>
                                </div>
                                <input type="file" name="avatar" class="hidden" accept="image/*" onchange="document.getElementById('avatar-preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('avatar-preview').classList.remove('hidden')" />
                                {{-- Previsualización --}}
                                <img id="avatar-preview" class="absolute inset-0 w-full h-full object-cover opacity-50 hidden" />
                            </label>
                            @error('avatar') <p class="text-red-400 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Passwords --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="group/field">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 ml-1 transition-colors group-focus-within/field:text-primary">Contraseña</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within/field:text-primary transition-colors material-symbols-outlined text-[20px]">lock</span>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        class="w-full pl-12 pr-4 py-3 bg-slate-900/50 border @error('password') border-red-500/50 @else border-white/10 @enderror rounded-2xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 outline-none transition-all hover:border-white/20"
                                        placeholder="••••••••" 
                                        required
                                    />
                                </div>
                                @error('password') <p class="text-red-400 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="group/field">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 ml-1 transition-colors group-focus-within/field:text-primary">Confirmar</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within/field:text-primary transition-colors material-symbols-outlined text-[20px]">lock_reset</span>
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        class="w-full pl-12 pr-4 py-3 bg-slate-900/50 border border-white/10 rounded-2xl text-white placeholder:text-slate-600 focus:ring-2 focus:ring-primary/50 focus:border-primary/50 outline-none transition-all hover:border-white/20"
                                        placeholder="••••••••" 
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- Términos --}}
                        <div class="group/terms">
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="terms" required class="mt-1 size-5 rounded border-white/20 bg-slate-900/50 text-primary focus:ring-2 focus:ring-primary/50 focus:ring-offset-0 transition-all checked:bg-primary checked:border-primary">
                                <span class="text-xs text-slate-400 leading-snug">
                                    Acepto los <a href="#" class="text-white font-bold hover:text-primary hover:underline transition-colors">Términos de Servicio</a> y la <a href="#" class="text-white font-bold hover:text-primary hover:underline transition-colors">Política de Privacidad</a> de NovelAsia.
                                </span>
                            </label>
                            @error('terms') <p class="text-red-400 text-[10px] font-bold mt-1 ml-8">{{ $message }}</p> @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-4 rounded-2xl shadow-lg transition-all transform active:scale-[0.98] sakura-glow flex items-center justify-center gap-2 mt-2">
                            <span class="material-symbols-outlined">person_add</span>
                            Crear Cuenta
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-white/5 text-center">
                        <p class="text-sm text-slate-400">
                            ¿Ya eres parte de NovelAsia? 
                            <a href="{{ route('login') }}" class="text-primary font-bold hover:text-white hover:underline ml-1 transition-colors">Inicia Sesión</a>
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