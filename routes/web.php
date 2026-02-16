<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LecturaController;
use App\Http\Controllers\NovelaController;

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


Route::get('/book', function () {
    return view('books.book-info');
})->name('book.info');

Route::get('/author', function () {
    return view('books.author.author-info');
})->name('author.info');

Route::get('/library', function () {
    return view('user.library');
})->name('library');

Route::get('/search', function () {
    return view('books.search');
})->name('search');

Route::get('/chapter/{id_capitulo}', [LecturaController::class, 'show'])->name('chapter.read');

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

// Rutas de lectura
Route::prefix('lectura')->name('lectura.')->group(function () {
    Route::get('/capitulo/{id_capitulo}', [LecturaController::class, 'show'])->name('show');
    Route::get('/novela/{id_novela}/capitulo/{numero_capitulo}', [LecturaController::class, 'showByNumber'])->name('showByNumber');
    
    // Rutas AJAX
    Route::post('/capitulo/{id_capitulo}/progreso', [LecturaController::class, 'guardarProgreso'])->name('guardarProgreso');
    Route::post('/configuracion', [LecturaController::class, 'cambiarConfiguracion'])->name('configuracion');
});

// Añade esto después de las rutas de lectura
Route::get('/novela/{id_novela}', [App\Http\Controllers\NovelaController::class, 'show'])->name('novelas.show');