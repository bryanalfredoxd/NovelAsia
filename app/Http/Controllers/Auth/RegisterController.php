<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password; // <--- IMPORTANTE: Para reglas de contraseña
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // ==========================================
        // 1. VALIDACIÓN ESTRICTA (LÓGICA BLINDADA)
        // ==========================================
        $request->validate([
            // NOMBRE REAL:
            // Regex permite letras (a-z), espacios (\s) y acentos/ñ.
            // Rechaza números y símbolos raros.
            'nombre' => [
                'required', 
                'string', 
                'min:3', 
                'max:100', 
                'regex:/^[a-zA-Z\s\á\é\í\ó\ú\ñ\Á\É\Í\Ó\Ú\Ñ]+$/'
            ],

            // USERNAME:
            // Regex permite solo letras, números y guion bajo (_).
            // ^ = inicio, $ = fin.
            // Esto es lo que bloquea el "@".
            'username' => [
                'required', 
                'string', 
                'min:3', 
                'max:20', 
                'unique:usuarios', 
                'regex:/^[a-zA-Z0-9_]+$/' 
            ],

            // EMAIL:
            // 'email:dns' verifica que el dominio (ej: gmail.com) tenga registros MX reales.
            'email' => [
                'required', 
                'string', 
                'email:dns', 
                'max:100', 
                'unique:usuarios'
            ],

            // PASSWORD:
            // Usamos el objeto Password de Laravel para mayor seguridad.
            'password' => [
                'required', 
                'confirmed', 
                Password::min(8)
                    ->letters()    // Debe tener letras
                    ->mixedCase()  // Debe tener Mayúsculas y Minúsculas
                    ->numbers()    // Debe tener números
                    // ->symbols() // Opcional: Si quieres obligar símbolos
            ],

            'terms' => ['required'],
            
            // AVATAR:
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:8192'],

        ], [
            // ==========================================
            // MENSAJES DE ERROR PERSONALIZADOS (Feedback UX)
            // ==========================================
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'username.regex' => 'El usuario solo acepta letras, números y guión bajo (sin espacios ni @).',
            'username.unique' => 'Ese usuario ya está ocupado, intenta con otro.',
            'username.min' => 'El usuario debe tener al menos 3 caracteres.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            // Mensajes automáticos de la regla Password (Laravel los traduce si tienes el idioma es)
        ]);

        // ==========================================
        // 2. PROCESAMIENTO DE IMAGEN (Optimizado)
        // ==========================================
        $avatarPath = null;
        
        if ($request->hasFile('avatar')) {
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($request->file('avatar'));
                
                // Redimensionar a 400x400 y convertir a WebP
                $image->cover(400, 400);
                $encoded = $image->toWebp(80);
                
                $filename = 'avatars/' . md5(uniqid()) . '.webp';
                Storage::disk('public')->put($filename, (string) $encoded);
                
                $avatarPath = 'storage/' . $filename;
            } catch (\Exception $e) {
                // Loguear error silenciosamente si falla la imagen
                $avatarPath = null; 
            }
        }

        // ==========================================
        // 3. CREACIÓN DEL USUARIO
        // ==========================================
        $user = User::create([
            'nombre' => strip_tags($request->nombre), // Limpieza extra XSS
            'username' => strtolower($request->username), // Guardamos siempre en minúsculas
            'email' => strtolower($request->email),
            'password_hash' => Hash::make($request->password),
            'avatar_url' => $avatarPath, 
            'rol' => 'lector',
            'idioma_preferido' => 'es',
            'dispositivo_registro' => 'web',
        ]);

        // ==========================================
        // 4. LOGIN Y REDIRECCIÓN
        // ==========================================
        Auth::login($user);

        return redirect()->route('home')->with('success', '¡Cuenta creada exitosamente!');
    }
}