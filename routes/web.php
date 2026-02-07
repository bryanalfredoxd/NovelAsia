<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';


// Conexion con el servidor

Route::post('/deploy-webhook-secret-123', function () {
    // Esto ejecuta el pull desde PHP de forma interna
    $output = shell_exec('git pull origin main 2>&1');
    
    // Opcional: Limpiar caché después del pull
    Artisan::call('optimize:clear');
    
    return response()->json(['output' => $output]);
});