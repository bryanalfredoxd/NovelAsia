<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Mostrar formulario
    public function showLoginForm()
    {
        // Si ya está logueado, lo mandamos a casa
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

// Procesar Login
    public function login(Request $request)
    {
        // 1. Validar inputs
        // Ya no validamos 'email', sino un campo genérico 'identity'
        $request->validate([
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'identity.required' => 'Debes ingresar tu usuario o correo.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // 2. Determinar si es Email o Username
        $input = $request->input('identity');
        
        // La función filter_var nos dice si el texto tiene formato de correo
        $loginType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Preparar credenciales
        // Laravel es listo: buscará en la columna $loginType el valor $input
        $credentials = [
            $loginType => $input,
            'password' => $request->input('password')
        ];

        // 4. Recordarme
        $remember = $request->filled('remember');

        // 5. Intentar Autenticación
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'))
                             ->with('success', '¡Bienvenido de vuelta, ' . Auth::user()->username . '!');
        }

        // 6. Si falla
        return back()->withErrors([
            'identity' => 'El usuario/correo o la contraseña son incorrectos.',
        ])->onlyInput('identity');
    }
}