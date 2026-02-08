{{-- 
    1. Extendemos del Layout Principal 
    Esto carga el <head>, Tailwind, Navbar y Footer automáticamente.
--}}
@extends('layouts.app')

{{-- 
    2. Definimos el Título de la Pestaña 
--}}
@section('title', 'Inicio - NovelAsia')

{{-- 
    3. Definimos el Contenido Principal
    Aquí usamos @include para cargar el archivo 'home.blade.php'
    que contiene solo el HTML específico de la portada.
--}}
@section('content')
    
    {{-- Verificamos si existe la vista para evitar errores si aún no la creas --}}
    @if(view()->exists('home'))
        @include('home')
    @else
        {{-- Fallback temporal por si no has creado home.blade.php --}}
        <div class="min-h-screen flex items-center justify-center text-center">
            <div class="glass p-10 rounded-3xl max-w-lg mx-4">
                <h1 class="text-4xl font-display font-bold text-primary mb-4">NovelAsia</h1>
                <p class="text-slate-400 mb-6">El archivo <code>home.blade.php</code> aún no existe o está vacío.</p>
                <div class="p-4 bg-slate-950/50 rounded-xl border border-white/10 text-sm font-mono text-left">
                    <span class="text-green-400">➜</span> Crea: resources/views/home.blade.php
                </div>
            </div>
        </div>
    @endif

@endsection