<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. Definir tu tabla personalizada
    protected $table = 'usuarios';
    
    // 2. Definir tu llave primaria
    protected $primaryKey = 'id_usuario';

    // --- SOLUCIÓN AL ERROR DE FECHAS ---
    // Mapeamos 'created_at' a tu columna real 'fecha_registro'
    const CREATED_AT = 'fecha_registro';

    // Desactivamos 'updated_at' porque tu tabla no tiene esa columna
    const UPDATED_AT = null;
    // -----------------------------------

    // 3. Los campos que se pueden llenar masivamente
    protected $fillable = [
        'username',
        'nombre',
        'email',
        'pais',
        'password_hash', // Ojo aquí, usaremos tu columna
    ];

    // 4. Ocultar el hash al serializar
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    // 5. IMPORTANTE: Decirle a Laravel cuál es el campo de contraseña para el Login
    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // 'password_hash' => 'hashed', // Lo manejaremos manual en el controlador por seguridad inicial
        ];
    }
}