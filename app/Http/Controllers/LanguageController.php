<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        // Verificar si el idioma es válido
        if (in_array($lang, config('app.available_locales', ['es', 'ca', 'en']))) {
            Session::put('locale', $lang);
            App::setLocale($lang);
        }
        
        // Obtener la URL de referencia o ir a la página principal
        $previous = url()->previous();
        
        // Añadir el parámetro de idioma para asegurar que se aplica
        $redirectUrl = $previous . (parse_url($previous, PHP_URL_QUERY) ? '&' : '?') . 'lang=' . $lang;
        
        return redirect($redirectUrl);
    }
} 