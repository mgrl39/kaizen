<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

/**
 * @class LanguageController
 * @brief Controlador para manejar el cambio de idioma en la aplicación
 * 
 * Este controlador permite cambiar el idioma de la aplicación y mantener
 * la preferencia en la sesión del usuario.
 */
class LanguageController extends Controller
{
    /**
     * @brief Cambia el idioma de la aplicación
     * @param string $lang Código del idioma a cambiar (ej: 'es', 'en', 'ca')
     * @return \Illuminate\Http\RedirectResponse Redirección a la página anterior con el parámetro de idioma
     * 
     * Este método verifica si el idioma solicitado está disponible en la configuración,
     * lo establece en la sesión y en la aplicación, luego redirecciona a la página
     * anterior añadiendo el parámetro de idioma.
     */
    public function switchLang($lang)
    {
        // TODO QUITAR LOS IDIOMAS DE AQUI
        if (in_array($lang, config('app.available_locales', ['es', 'ca', 'en']))) {
            Session::put('locale', $lang);
            App::setLocale($lang);
        }
        $previous = url()->previous();
        // CAMBIAR EL SISTEMA DE LENGUA QUE ASI ES CUTRISIMO
        $redirectUrl = $previous . (parse_url($previous, PHP_URL_QUERY) ? '&' : '?') . 'lang=' . $lang;
        return redirect($redirectUrl);
    }
}
