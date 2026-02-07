<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/register', function () {
    return view('register');
});

use Illuminate\Http\Request;

Route::get('/login', function () {
    return view('login');
});

Route::post('/login', function (Request $request) {
    // Usuario estático para pruebas
    if ($request->email === 'admin@sakura.com' && $request->password === 'password') {
        return redirect('/dashboard');
    }
    return back();
});

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', function () {
    // Simular registro exitoso
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});
