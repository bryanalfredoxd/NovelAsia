<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Mostrar la vista de registro.
     */
    public function create(): Response
    {
        // Esto le dice a Inertia que busque el archivo en resources/js/pages/Auth/register.tsx
        return Inertia::render('Auth/register'); 
    }

    /**
     * Guardar el usuario nuevo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:usuarios',
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|lowercase|email|max:100|unique:usuarios',
            'pais' => 'required|string|max:50',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::create([
            'username' => $request->username,
            'nombre' => $request->nombre,
            'email' => $request->email,
            'pais' => $request->pais,
            'password_hash' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}