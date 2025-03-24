<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ __('Error 404') }}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center">
            <div class="text-center mb-4">
                <p class="lead text-muted animate__animated animate__fadeIn">
                    {{ __('Welcome to our application') }}
                </p>
            </div>
            
            <div class="text-center animate__animated animate__fadeIn" data-aos="zoom-in">
                <h1 class="display-1 animate__animated animate__bounce">{{ __('Error 404') }}</h1>
                <p class="lead animate__animated animate__fadeIn animate__delay-1s">
                    {{ __('The page you are looking for does not exist.') }}
                </p>
                <a href="/" class="btn btn-primary animate__animated animate__fadeIn animate__delay-2s">
                    {{ __('Back to home') }}
                </a>
                
                <div class="mt-5">
                    <p class="text-muted">{{ __('Current language') }}: 
                        <strong>
                            @if(app()->getLocale() == 'es')
                                Español
                            @elseif(app()->getLocale() == 'ca')
                                Català
                            @else
                                English
                            @endif
                        </strong>
                    </p>
                    
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('language', 'es') }}" class="btn btn-sm {{ app()->getLocale() == 'es' ? 'btn-secondary' : 'btn-outline-secondary' }} mx-1">
                            Español
                        </a>
                        <a href="{{ route('language', 'ca') }}" class="btn btn-sm {{ app()->getLocale() == 'ca' ? 'btn-secondary' : 'btn-outline-secondary' }} mx-1">
                            Català
                        </a>
                        <a href="{{ route('language', 'en') }}" class="btn btn-sm {{ app()->getLocale() == 'en' ? 'btn-secondary' : 'btn-outline-secondary' }} mx-1">
                            English
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init();
        </script>
    </body>
</html>
