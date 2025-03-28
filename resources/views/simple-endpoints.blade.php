<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>API Endpoints - {{ config('app.name', 'Sistema de Cines') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-gray-100 via-gray-200 to-white dark:from-gray-900 dark:via-gray-800 dark:to-black text-gray-900 dark:text-white font-sans min-h-screen transition-colors duration-300">
    <!-- Nav Bar -->
    @include('components.navbar')

    <div class="pt-24 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="mb-10">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 flex items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                        <i class="fas fa-code text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">API Endpoints</h1>
                        <p class="text-gray-600 dark:text-gray-400">Rutas disponibles en la aplicación</p>
                    </div>
                    <div class="ml-auto">
                        <!-- Dark mode toggle -->
                        <button @click="darkMode = !darkMode" class="p-3 rounded-full bg-gray-200 dark:bg-gray-700 focus:outline-none transition-colors duration-300" aria-label="Toggle dark mode">
                            <i class="fas fa-sun text-yellow-500 dark:hidden"></i>
                            <i class="fas fa-moon text-blue-300 hidden dark:block"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                Esta página muestra todas las rutas disponibles en la aplicación. Utiliza los filtros para buscar por método HTTP.
                            </p>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Route filters -->
            @include('components.route-filters')
            
            <!-- Route list -->
            <div class="space-y-2">
                @foreach($routes as $route)
                    <div class="route-item 
                        @if(strpos($route['method'], 'GET') !== false) method-get
                        @elseif(strpos($route['method'], 'POST') !== false) method-post
                        @elseif(strpos($route['method'], 'PUT') !== false) method-put
                        @elseif(strpos($route['method'], 'PATCH') !== false) method-patch
                        @elseif(strpos($route['method'], 'DELETE') !== false) method-delete
                        @endif">
                        @include('components.route-item', ['route' => $route])
                    </div>
                @endforeach
            </div>
            
            <div class="mt-10 flex justify-center">
                <a href="/" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300 text-gray-700 dark:text-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i> Volver al inicio
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('components.footer')
</body>
</html> 