<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Primero intentamos obtener el idioma de la URL
        if ($request->has('lang')) {
            $locale = $request->query('lang');
            if (in_array($locale, config('app.available_locales', ['es', 'ca', 'en']))) {
                App::setLocale($locale);
                Session::put('locale', $locale);
            }
        } 
        // Si no hay en URL, intentamos de la sesión
        elseif (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        
        return $next($request);
    }
} 