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
    
    <!-- CSS Personalizado (mínimo) -->
    <style>
        /* Estilos para personalizar Bootstrap */
        :root {
            --primary-color: #6A11CB;
            --secondary-color: #2575FC;
            --dark-bg: #121212;
            --card-bg: rgba(33, 37, 41, 0.8);
        }
        
        body {
            padding-top: 56px;
            background-color: var(--dark-bg);
            background-image: linear-gradient(135deg, #121212 0%, #1e1e2f 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #5a0db0;
            border-color: #5a0db0;
        }
        
        .text-primary {
            color: var(--secondary-color) !important;
        }
        
        .card {
            background-color: var(--card-bg);
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .content-container {
            background-color: rgba(18, 18, 18, 0.7);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .footer {
            margin-top: auto;
        }
    </style>
    
    @yield('styles')
</head>
<body class="d-flex flex-column">
    <!-- Navbar con botón de Acceder alineado correctamente -->
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <div class="container">
            <a href="/" class="navbar-brand d-flex align-items-center">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Kaizen" height="30">
                <span class="ms-2">Kaizen</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a href="/cinemas" class="nav-link">
                            <i class="bi bi-building me-1"></i>Cines
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/movies" class="nav-link">
                            <i class="bi bi-film me-1"></i>Películas
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="/login" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Acceder
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Contenido principal -->
    <main class="container py-4 flex-grow-1" id="content">
        <div class="content-container">
            @yield('content')
        </div>
    </main>
    
    <!-- Footer simplificado -->
    <footer class="py-3 bg-dark text-white">
        <div class="container">
            <div class="row g-3 text-center text-md-start">
                <div class="col-md-6">
                    <a href="/" class="text-white text-decoration-none">
                        <i class="bi bi-film me-2"></i>Kaizen
                    </a>
                    <span class="ms-2 text-white-50">&copy; {{ date('Y') }} Kaizen Cinema</span>
                </div>

                <div class="col-md-6 text-md-end">
                    <div class="d-flex justify-content-center justify-content-md-end gap-3">
                        <a href="/" class="text-white-50 text-decoration-none">Inicio</a>
                        <a href="/cinemas" class="text-white-50 text-decoration-none">Cines</a>
                        <a href="/movies" class="text-white-50 text-decoration-none">Películas</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Solo Bootstrap JS Core -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>
