<?php
// app/Http/Middleware/SetLocale.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Detectar idioma desde cookie, sesión o header
        $locale = $request->cookie('locale', 
                  $request->session()->get('locale', 
                  $request->getPreferredLanguage(['es', 'en'])));
        
        App::setLocale($locale);
        
        return $next($request);
    }
}