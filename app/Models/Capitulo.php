<?php
// app/Models/Capitulo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Capitulo extends Model
{
    protected $table = 'capitulos';
    protected $primaryKey = 'id_capitulo';
    
    /**
     * Indicates if the model should be timestamped.
     * Usamos los campos fecha_creacion y fecha_actualizacion de la tabla
     */
    public $timestamps = false;
    
    protected $fillable = [
        'id_novela',
        'numero_capitulo',
        'titulo_original',
        'contenido_original',
        'fecha_publicacion_original',
        'fuente_url',
        'orden_lectura',
        'palabras_original',
        'estado_capitulo',
        'enviado_traduccion',
        'prioridad_traduccion',
        'hash_contenido',
        'scrapeado_en',
        'intentos_scraping',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    
    protected $casts = [
        'fecha_publicacion_original' => 'datetime',
        'scrapeado_en' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'enviado_traduccion' => 'boolean',
        'palabras_original' => 'integer',
        'prioridad_traduccion' => 'integer',
        'intentos_scraping' => 'integer'
    ];
    
    /**
     * Relación con la novela
     */
    public function novela(): BelongsTo
    {
        return $this->belongsTo(Novela::class, 'id_novela', 'id_novela');
    }
    
    /**
     * Relación con las traducciones del capítulo
     */
    public function traducciones(): HasMany
    {
        return $this->hasMany(TraduccionCapitulo::class, 'id_capitulo', 'id_capitulo');
    }
    
    /**
     * Obtener la traducción en un idioma específico
     */
    public function traduccionEnIdioma(string $idioma, int $version = null)
    {
        $query = $this->traducciones()
            ->where('idioma', $idioma)
            ->where('estado_traduccion', 'completado');
        
        if ($version) {
            $query->where('version_traduccion', $version);
        } else {
            $query->latest('version_traduccion');
        }
        
        return $query->first();
    }
    
    /**
     * Obtener capítulo anterior
     */
    public function anterior()
    {
        return self::where('id_novela', $this->id_novela)
            ->where('numero_capitulo', '<', $this->numero_capitulo)
            ->where('estado_capitulo', 'disponible')
            ->orderBy('numero_capitulo', 'desc')
            ->first();
    }
    
    /**
     * Obtener capítulo siguiente
     */
    public function siguiente()
    {
        return self::where('id_novela', $this->id_novela)
            ->where('numero_capitulo', '>', $this->numero_capitulo)
            ->where('estado_capitulo', 'disponible')
            ->orderBy('numero_capitulo', 'asc')
            ->first();
    }
    
    /**
     * Calcular tiempo estimado de lectura (en minutos)
     */
    public function tiempoLectura(): int
    {
        $palabras = $this->palabras_original ?? 0;
        // Promedio de 200 palabras por minuto
        return max(1, ceil($palabras / 200));
    }
}