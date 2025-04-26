<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | Kaizen</title>
    
    <!-- Bootstrap Core -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Estilos de la aplicación -->
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
    <link href="{{ asset('css/effects.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Elementos decorativos tipo Stripe -->
    <div class="stripe-blob blob-1"></div>
    <div class="stripe-blob blob-2"></div>
    
    <div class="error-container text-center">
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="error-number">404</div>
        <div class="error-message">Página no encontrada</div>
        <p class="text-muted mb-4">Lo sentimos, la página que estás buscando no existe o ha sido movida.</p>
        <a href="/" class="btn btn-primary">
            <i class="bi bi-house-door me-2"></i>Volver al inicio
        </a>
    </div>
</body>
</html>