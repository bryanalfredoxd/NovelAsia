<?php
// app/Models/TraduccionCapitulo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraduccionCapitulo extends Model
{
    protected $table = 'traducciones_capitulo';
    protected $primaryKey = 'id_traduccion_capitulo';
    
    /**
     * Indicates if the model should be timestamped.
     * Usamos fecha_traduccion en lugar de timestamps automáticos
     */
    public $timestamps = false;
    
    protected $fillable = [
        'id_capitulo',
        'idioma',
        'titulo_traducido',
        'contenido_traducido',
        'estado_traduccion',
        'fecha_traduccion',
        'traductor_ia',
        'version_traduccion',
        'calidad_estimada',
        'palabras_traducidas',
        'contenido_comprimido',
        'hash_traduccion'
    ];
    
    protected $casts = [
        'fecha_traduccion' => 'datetime',
        'version_traduccion' => 'integer',
        'palabras_traducidas' => 'integer'
    ];
    
    /**
     * Relación con el capítulo
     */
    public function capitulo(): BelongsTo
    {
        return $this->belongsTo(Capitulo::class, 'id_capitulo', 'id_capitulo');
    }
    
    /**
     * Obtener título para mostrar (prioriza traducción)
     */
    public function getTituloVisualAttribute(): string
    {
        return $this->titulo_traducido ?? $this->capitulo->titulo_original ?? 'Capítulo ' . $this->capitulo->numero_capitulo;
    }
    
    /**
     * Obtener contenido para mostrar (prioriza traducción)
     */
    public function getContenidoVisualAttribute(): string
    {
        return $this->contenido_traducido ?? $this->capitulo->contenido_original ?? '';
    }
}