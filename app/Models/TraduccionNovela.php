<?php
// app/Models/TraduccionNovela.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraduccionNovela extends Model
{
    protected $table = 'traducciones_novela';
    protected $primaryKey = 'id_traduccion_novela';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_novela',
        'idioma',
        'titulo_traducido',
        'descripcion_traducida',
        'estado_traduccion',
        'fecha_inicio_traduccion',
        'fecha_fin_traduccion',
        'traductor_ia',
        'configuracion_ia',
        'prioridad_idioma',
        'es_principal'
    ];
    
    protected $casts = [
        'configuracion_ia' => 'array',
        'fecha_inicio_traduccion' => 'datetime',
        'fecha_fin_traduccion' => 'datetime',
        'es_principal' => 'boolean',
        'prioridad_idioma' => 'integer'
    ];
    
    /**
     * Relación con la novela
     */
    public function novela(): BelongsTo
    {
        return $this->belongsTo(Novela::class, 'id_novela', 'id_novela');
    }
}