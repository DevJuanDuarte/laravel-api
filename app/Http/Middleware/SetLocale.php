<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     * 
     * Permite cambiar el idioma de la aplicación según el header 'Accept-Language'
     * o el parámetro 'locale' en la URL.
     * 
     * Idiomas soportados: es (español), en (inglés)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Idiomas disponibles
        $availableLocales = ['es', 'en'];
        
        // Intentar obtener el idioma del parámetro de la URL
        $locale = $request->input('locale');
        
        // Si no está en la URL, intentar obtenerlo del header Accept-Language
        if (!$locale) {
            $locale = $request->header('Accept-Language');
        }
        
        // Si se encontró un idioma y está disponible, usarlo
        if ($locale && in_array($locale, $availableLocales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
