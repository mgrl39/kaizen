<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | Kaizen</title>
    
    <!-- CSS Resources -->
    @include('components.styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Background Elements -->
    @include('components.background')
    
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
    
    <!-- JS Resources -->
    @include('components.scripts')
</body>
</html> 