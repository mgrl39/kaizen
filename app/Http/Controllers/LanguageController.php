<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
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
