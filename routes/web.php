<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController; // <--- Importante importar esto
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Inicio
Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// --- Registro ---
Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

// --- Login / Logout ---
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout'); // <--- NUEVA RUTA

// --- Dashboard ---
Route::get('/dashboard', function () {
    return Inertia::render('dashboard'); // Renderiza resources/js/pages/dashboard.tsx
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';