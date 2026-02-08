<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     * AGREGAMOS: 'nombre' y 'avatar_url'
     */
    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'nombre',       // <--- NUEVO
        'avatar_url',   // <--- NUEVO
        'rol',
        'idioma_preferido',
        'dispositivo_registro',
        'fecha_registro'
    ];

    protected $hidden = [
        'password_hash',
        'token_notificaciones',
    ];

    protected $casts = [
        'esta_activo' => 'boolean',
        'configuracion_app' => 'array',
        'ultimo_login' => 'datetime',
        'fecha_registro' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}