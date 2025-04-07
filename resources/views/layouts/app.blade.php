<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Kaizen'))</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Generic CSS (Tema principal) -->
    <link href="{{ asset('css/generic.css') }}" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .main-content {
            flex: 1;
            padding-top: 5rem;
            padding-bottom: 3rem;
        }
        
        /* Clases para tema claro (sobreescribiendo generic.css cuando está en modo claro) */
        body.light-theme {
            --primary-bg: #ffffff;
            --secondary-bg: #f8f9fa;
            --tertiary-bg: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #495057;
            --text-muted: #6c757d;
            --card-bg: #ffffff;
            --border-color: #dee2e6;
            --input-bg: #ffffff;
            --input-color: #212529;
            --input-border: #ced4da;
            background-color: var(--primary-bg);
            color: var(--text-primary);
        }
    </style>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex flex-column min-vh-100">
        <!-- Navbar -->
        @include('components.navbar')

        <!-- Contenido principal -->
        <main class="main-content">
            <div class="container">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        @include('components.footer')
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check for dark mode preference
        const darkMode = localStorage.getItem('darkMode') === 'true';
        
        // Apply theme
        if (!darkMode) {
            document.body.classList.add('light-theme');
        }
        
        // Listen for dark mode changes
        window.addEventListener('storage', function(e) {
            if (e.key === 'darkMode') {
                const isDark = e.newValue === 'true';
                if (!isDark) {
                    document.body.classList.add('light-theme');
                } else {
                    document.body.classList.remove('light-theme');
                }
            }
        });
    });
    </script>

    @yield('scripts')
</body>
</html>
