<?php
// app/Models/Novela.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Novela extends Model
{
    protected $table = 'novelas';
    protected $primaryKey = 'id_novela';
    
    /**
     * Indicates if the model should be timestamped.
     * Usamos fecha_creacion en lugar de timestamps automáticos
     */
    public $timestamps = false;
    
    protected $fillable = [
        'titulo_original',
        'titulo_ingles',
        'autor_original',
        'descripcion_original',
        'url_original_qidian',
        'generos',
        'etiquetas',
        'estado_original',
        'fecha_publicacion_original',
        'portada_url',
        'fuente_scraping',
        'ultimo_scraping',
        'es_verificado',
        'promedio_calificacion',
        'total_vistas',
        'total_favoritos',
        'fecha_creacion'
    ];
    
    protected $casts = [
        'generos' => 'array',
        'etiquetas' => 'array',
        'fecha_publicacion_original' => 'date',
        'ultimo_scraping' => 'datetime',
        'fecha_creacion' => 'datetime',
        'es_verificado' => 'boolean',
        'promedio_calificacion' => 'float',
        'total_vistas' => 'integer',
        'total_favoritos' => 'integer'
    ];
    
    /**
     * Relación con capítulos
     */
    public function capitulos(): HasMany
    {
        return $this->hasMany(Capitulo::class, 'id_novela', 'id_novela')
            ->where('estado_capitulo', 'disponible')
            ->orderBy('numero_capitulo');
    }
    
    /**
     * Relación con traducciones de novela
     */
    public function traducciones(): HasMany
    {
        return $this->hasMany(TraduccionNovela::class, 'id_novela', 'id_novela');
    }
    
    /**
     * Obtener título en el idioma especificado
     */
    public function getTituloAttribute()
    {
        $idioma = app()->getLocale();
        $traduccion = $this->traducciones()
            ->where('idioma', $idioma)
            ->where('estado_traduccion', 'completado')
            ->first();
        
        return $traduccion->titulo_traducido ?? 
               $this->titulo_ingles ?? 
               $this->titulo_original;
    }
    
    /**
     * Obtener descripción en el idioma especificado
     */
    public function getDescripcionAttribute()
    {
        $idioma = app()->getLocale();
        $traduccion = $this->traducciones()
            ->where('idioma', $idioma)
            ->where('estado_traduccion', 'completado')
            ->first();
        
        return $traduccion->descripcion_traducida ?? $this->descripcion_original;
    }
    
    /**
     * Total de capítulos disponibles
     */
    public function getTotalCapitulosAttribute(): int
    {
        return $this->capitulos()->count();
    }
}