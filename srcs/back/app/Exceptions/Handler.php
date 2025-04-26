<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    // Handlers de errors, es decir, si se entra a una ruta que no existe, se muestra el error 404
    // Si se produce un error 500, se muestra el error 500
    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception)) {
            // Establecer el idioma antes de renderizar la vista
            if (Session::has('locale')) {
                App::setLocale(Session::get('locale'));
            } elseif ($request->has('lang')) {
                $locale = $request->query('lang');
                if (in_array($locale, config('app.available_locales', ['es', 'ca', 'en']))) {
                    App::setLocale($locale);
                }
            }

            if ($exception->getStatusCode() == 404) {
                return response()->view('errors.404');
            }
            
            if ($exception->getStatusCode() == 500) {
                return response()->view('errors.500');
            }
        }
        
        return parent::render($request, $exception);
    }
}
