<!DOCTYPE html>
<html>
    <head>
        <title>Error 404</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <p>{{ __('messages.welcome') }}</p>
        <div class="container min-vh-100 d-flex align-items-center justify-content-center">
            <div class="text-center animate__animated animate__fadeIn" data-aos="zoom-in">
                <h1 class="display-1 animate__animated animate__bounce">Error 404</h1>
                <p class="lead animate__animated animate__fadeIn animate__delay-1s">La página que estás buscando no existe.</p>
                <a href="/" class="btn btn-primary animate__animated animate__fadeIn animate__delay-2s">Volver al inicio</a>
            </div>
        </div>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init();
        </script>
    </body>
</html>
