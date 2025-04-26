<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kaizen')</title>

    <!-- Bootstrap Core -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Estilos de la aplicación -->
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
    <link href="{{ asset('css/effects.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body class="d-flex flex-column">
    <!-- Elementos decorativos tipo Stripe -->
    <div class="stripe-blob blob-1"></div>
    <div class="stripe-blob blob-2"></div>
    
    <!-- Incluir el componente de navbar -->
    @include('components.navbar')
    
    <!-- Contenido principal - sin animación -->
    <main class="container py-4 flex-grow-1" id="content">
        <div class="content-container @if(request()->is('login') || request()->is('register')) auth-page @endif">
            @yield('content')
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="py-4 footer">
        <div class="container">
            <div class="row g-3 text-center text-md-start">
                <div class="col-md-6" data-aos="fade-right" data-aos-duration="800">
                    <a href="/" class="text-decoration-none text-light">
                        <i class="bi bi-film me-2"></i>Kaizen
                    </a>
                    <span class="ms-2 text-muted">&copy; {{ date('Y') }} Kaizen Cinema</span>
                </div>

                <div class="col-md-6 text-md-end" data-aos="fade-left" data-aos-duration="800">
                    <div class="d-flex justify-content-center justify-content-md-end gap-3">
                        <a href="/" class="text-decoration-none text-muted">Inicio</a>
                        <a href="/cinemas" class="text-decoration-none text-muted">Cines</a>
                        <a href="/movies" class="text-decoration-none text-muted">Películas</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JS Core -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Inicializar AOS para animaciones
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                once: true,
                offset: 50
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>
