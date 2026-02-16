<?php
// app/Http/Controllers/LecturaController.php

namespace App\Http\Controllers;

use App\Models\Capitulo;
use App\Models\Novela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class LecturaController extends Controller
{
    /**
     * Mostrar capítulo por ID
     */
    public function show($id_capitulo)
    {
        // Forzar idioma español para las traducciones
        $idioma = 'es'; // Forzamos español
        
        // Cargar el capítulo con sus relaciones
        $capitulo = Capitulo::with(['novela', 'traducciones' => function($query) use ($idioma) {
            $query->where('idioma', $idioma)
                  ->where('estado_traduccion', 'completado')
                  ->latest('version_traduccion');
        }])->findOrFail($id_capitulo);
        
        // Verificar que el capítulo esté disponible
        if ($capitulo->estado_capitulo !== 'disponible') {
            abort(404, 'Capítulo no disponible');
        }
        
        // Incrementar vistas
        $this->incrementarVistas($capitulo);
        
        // Obtener la traducción (si existe)
        $traduccion = $capitulo->traducciones->first();
        
        // Debug temporal (puedes borrarlo después)
        if (!$traduccion) {
            Log::info('No hay traducción al español para el capítulo ' . $id_capitulo);
        } else {
            Log::info('Traducción encontrada: ' . $traduccion->titulo_traducido);
        }
        
        // Obtener capítulos anterior y siguiente
        $anterior = $capitulo->anterior();
        $siguiente = $capitulo->siguiente();
        
        // Obtener preferencias del usuario
        $preferencias = $this->obtenerPreferencias();
        
        return view('books.read-chapter', compact(
            'capitulo',
            'traduccion',
            'anterior',
            'siguiente',
            'preferencias'
        ));
    }
    
    /**
     * Mostrar capítulo por novela y número
     */
    public function showByNumber($id_novela, $numero_capitulo)
    {
        $capitulo = Capitulo::where('id_novela', $id_novela)
            ->where('numero_capitulo', $numero_capitulo)
            ->where('estado_capitulo', 'disponible')
            ->firstOrFail();
        
        return redirect()->route('lectura.show', $capitulo->id_capitulo);
    }
    
    
    /**
     * Cambiar configuración de lectura
     */
    public function cambiarConfiguracion(Request $request)
    {
        $request->validate([
            'tamano_letra' => 'sometimes|in:pequeno,normal,grande,muy_grande',
            'tipo_letra' => 'sometimes|in:serif,sans-serif',
            'espaciado' => 'sometimes|in:normal,amplio,muy_amplio',
            'tema' => 'sometimes|in:claro,oscuro,sepia'
        ]);
        
        foreach ($request->all() as $key => $value) {
            Cookie::queue('config_' . $key, $value, 60 * 24 * 365); // 1 año
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Incrementar contador de vistas
     */
    private function incrementarVistas(Capitulo $capitulo)
    {
        // Incrementar en tabla novelas
        $capitulo->novela()->increment('total_vistas');
    }
    
    /**
     * Obtener preferencias del usuario desde cookies
     */
    private function obtenerPreferencias()
    {
        $defaults = [
            'tamano_letra' => 'normal',
            'tipo_letra' => 'serif',
            'espaciado' => 'normal',
            'tema' => 'claro'
        ];
        
        $preferencias = [];
        foreach ($defaults as $key => $default) {
            $preferencias[$key] = Cookie::get('config_' . $key, $default);
        }
        
        return $preferencias;
    }
}