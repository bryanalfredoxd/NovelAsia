<?php
// app/Http/Controllers/NovelaController.php

namespace App\Http\Controllers;

use App\Models\Novela;
use Illuminate\Http\Request;

class NovelaController extends Controller
{
    /**
     * Mostrar detalles de una novela
     */
    public function show($id_novela)
    {
        $novela = Novela::with(['capitulos', 'traducciones' => function($query) {
            $query->where('idioma', app()->getLocale())
                  ->where('estado_traduccion', 'completado');
        }])->findOrFail($id_novela);
        
        return view('books.book-info', compact('novela'));
    }
}