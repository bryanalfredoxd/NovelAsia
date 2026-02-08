<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- PÁGINA DE INICIO ---
Route::get('/', function () {
    return view('home');
})->name('home');

// --- DASHBOARD (Solo accesible si estás logueado - opcional middleware) ---
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard'); // Importante: darle nombre para el redirect del controlador

// --- RUTAS DE REGISTRO (CONECTADAS AL CONTROLADOR) ---
// 1. Mostrar formulario (GET)
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

// 2. Procesar datos (POST) - Aquí es donde guarda en la DB
Route::post('/register', [RegisterController::class, 'register']);

// Rutas de Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// --- UTILIDAD: CERRAR SESIÓN (Para probar registros nuevos) ---
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Ruta GET temporal para cerrar sesión rápido escribiendo la URL
Route::get('/logout-force', function () {
    Auth::logout();
    return redirect('/register');
});
