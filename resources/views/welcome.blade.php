<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistema de Cines') }}</title>

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
                        },
                        animation: {
                            'bounce-slow': 'bounce 3s infinite',
                            'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        }
                    }
                }
            }
        </script>
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="bg-gradient-to-br from-gray-100 via-gray-200 to-white dark:from-gray-900 dark:via-gray-800 dark:to-black text-gray-900 dark:text-white font-sans min-h-screen transition-colors duration-300">
        <!-- Nav Bar - With dark mode toggle -->
        <nav class="bg-white/80 dark:bg-black/40 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800 fixed w-full z-50 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="flex items-center">
                            <span class="text-blue-500"><i class="fas fa-film text-2xl"></i></span>
                            <span class="ml-2 font-bold text-xl tracking-tight">CineSystem</span>
                        </a>
                    </div>
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="/cinemas" class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/50 rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-building mr-2"></i>Cines
                        </a>
                        <a href="/movies" class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/50 rounded-md transition duration-150 ease-in-out">
                            <i class="fas fa-video mr-2"></i>Películas 
                        </a>
                        <a href="/simple-endpoints" class="ml-4 px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-md hover:shadow-lg transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-code mr-2"></i>API
                        </a>
                        <!-- Dark mode toggle -->
                        <button @click="darkMode = !darkMode" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 focus:outline-none transition-colors duration-300" aria-label="Toggle dark mode">
                            <i class="fas fa-sun text-yellow-500 dark:hidden"></i>
                            <i class="fas fa-moon text-blue-300 hidden dark:block"></i>
                        </button>
                    </div>
                    <!-- Mobile menu button -->
                    <div class="flex items-center md:hidden">
                        <!-- Dark mode toggle mobile -->
                        <button @click="darkMode = !darkMode" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 mr-2 focus:outline-none transition-colors duration-300" aria-label="Toggle dark mode">
                            <i class="fas fa-sun text-yellow-500 dark:hidden text-sm"></i>
                            <i class="fas fa-moon text-blue-300 hidden dark:block text-sm"></i>
                        </button>
                        <button type="button" class="text-gray-700 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none" aria-label="Toggle menu" x-data="{open: false}" @click="open = !open">
                            <i x-show="!open" class="fas fa-bars text-xl"></i>
                            <i x-show="open" class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80');">
            <div class="absolute inset-0 bg-white/80 dark:bg-black/70 backdrop-blur-sm transition-colors duration-300"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="space-y-8 text-left" data-aos="fade-up">
                        <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight">
                            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-500">
                                Sistema de Gestión
                            </span>
                            <span class="block mt-1">para Cines</span>
                        </h1>
                        <p class="text-xl text-gray-700 dark:text-gray-300 max-w-2xl">
                            Administra tus cines, películas y servicios con una plataforma moderna, eficiente y potente.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <a href="/cinemas" class="px-8 py-4 text-lg font-medium rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-blue-600/30 transition-all duration-200 text-center text-white">
                                <i class="fas fa-ticket-alt mr-2"></i>Explorar Sistema
                            </a>
                            <a href="/movies" class="px-8 py-4 text-lg font-medium rounded-lg bg-gray-200 dark:bg-gray-800/80 hover:bg-gray-300 dark:hover:bg-gray-700/80 border border-gray-300 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-600 transition-all duration-200 text-center">
                                <i class="fas fa-video mr-2"></i>Ver Películas
                            </a>
                        </div>
                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-4 pt-6">
                            <div class="text-center p-3 bg-white/70 dark:bg-gray-800/50 backdrop-blur-sm rounded-lg border border-gray-300 dark:border-gray-700 transition-colors duration-300">
                                <div class="font-bold text-2xl text-blue-600 dark:text-blue-400">500+</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Cines</div>
                            </div>
                            <div class="text-center p-3 bg-white/70 dark:bg-gray-800/50 backdrop-blur-sm rounded-lg border border-gray-300 dark:border-gray-700 transition-colors duration-300">
                                <div class="font-bold text-2xl text-blue-600 dark:text-blue-400">10k+</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Películas</div>
                            </div>
                            <div class="text-center p-3 bg-white/70 dark:bg-gray-800/50 backdrop-blur-sm rounded-lg border border-gray-300 dark:border-gray-700 transition-colors duration-300">
                                <div class="font-bold text-2xl text-blue-600 dark:text-blue-400">1M+</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Usuarios</div>
                            </div>
                        </div>
                    </div>
                    <div class="relative hidden lg:block">
                        <div class="absolute top-0 right-0 -mt-10 -mr-20 w-40 h-40 bg-blue-500 rounded-full opacity-50 filter blur-3xl animate-pulse-slow"></div>
                        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-indigo-600 rounded-full opacity-40 filter blur-3xl animate-pulse-slow"></div>
                        <div class="relative z-10 p-6 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border border-gray-300 dark:border-gray-700 rounded-2xl shadow-2xl transform -rotate-1 hover:rotate-0 transition-all duration-300">
                            <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Cinema Interface" class="rounded-lg shadow-lg">
                            <div class="absolute -top-3 -right-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">NUEVO</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Wave Divider -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" class="w-full h-auto fill-gray-100 dark:fill-gray-900 transition-colors duration-300">
                    <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
                </svg>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <!-- Section Title -->
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold">Nuestras <span class="text-blue-600 dark:text-blue-500">Características</span></h2>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-600 mx-auto mt-4 rounded-full"></div>
            </div>

            <!-- Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Cines Card -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl dark:shadow-2xl hover:shadow-blue-900/10 dark:hover:shadow-blue-900/20 hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-indigo-600/20 dark:from-blue-600/20 dark:to-indigo-600/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute top-0 right-0 p-4">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                    </div>
                    <div class="p-8 relative z-10">
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">Gestión de Cines</h3>
                        <p class="text-gray-700 dark:text-gray-300 mb-6">Administra todos tus cines, ubicaciones, salas y horarios desde una plataforma centralizada y fácil de usar.</p>
                        <ul class="space-y-2 mb-8 text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-600 dark:text-green-500 mt-1 mr-2"></i>
                                <span>Control de múltiples ubicaciones</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-600 dark:text-green-500 mt-1 mr-2"></i>
                                <span>Configuración de salas y asientos</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-600 dark:text-green-500 mt-1 mr-2"></i>
                                <span>Estadísticas de ventas por cine</span>
                            </li>
                        </ul>
                        <a href="/cinemas" class="inline-flex items-center justify-center w-full py-3 px-5 bg-gray-200 dark:bg-gray-700 hover:bg-blue-600 dark:hover:bg-blue-600 text-gray-800 hover:text-white dark:text-white font-medium rounded-lg transition-colors duration-300">
                            <i class="fas fa-arrow-right mr-2"></i> Explorar Cines
                        </a>
                    </div>
                </div>

                <!-- Películas Card -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl dark:shadow-2xl hover:shadow-indigo-900/10 dark:hover:shadow-indigo-900/20 hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/10 to-purple-600/20 dark:from-indigo-600/20 dark:to-purple-600/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute top-0 right-0 p-4">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-film text-2xl"></i>
                        </div>
                    </div>
                    <div class="p-8 relative z-10">
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">Catálogo de Películas</h3>
                        <p class="text-gray-700 dark:text-gray-300 mb-6">Administra todo el catálogo de películas, con detalles, clasificaciones y programación en todas tus salas.</p>
                        <ul class="space-y-2 mb-8 text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-600 dark:text-green-500 mt-1 mr-2"></i>
                                <span>Fichas técnicas completas</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-600 dark:text-green-500 mt-1 mr-2"></i>
                                <span>Integración con APIs de películas</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-600 dark:text-green-500 mt-1 mr-2"></i>
                                <span>Gestión de estrenos automática</span>
                            </li>
                        </ul>
                        <a href="/movies" class="inline-flex items-center justify-center w-full py-3 px-5 bg-gray-200 dark:bg-gray-700 hover:bg-indigo-600 dark:hover:bg-indigo-600 text-gray-800 hover:text-white dark:text-white font-medium rounded-lg transition-colors duration-300">
                            <i class="fas fa-arrow-right mr-2"></i> Ver Películas
                        </a>
                    </div>
                </div>

                <!-- Próximamente Card -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl dark:shadow-2xl hover:shadow-purple-900/10 dark:hover:shadow-purple-900/20 hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 left-0 p-2 z-20">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-purple-600 to-pink-600 text-white">
                            Próximamente
                        </span>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600/10 to-pink-600/20 dark:from-purple-600/20 dark:to-pink-600/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute top-0 right-0 p-4">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100 dark:bg-purple-600/20 text-purple-600 dark:text-purple-400 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-rocket text-2xl"></i>
                        </div>
                    </div>
                    <div class="p-8 relative z-10">
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">Próximas Funciones</h3>
                        <p class="text-gray-700 dark:text-gray-300 mb-6">Descubre las nuevas características que pronto estarán disponibles en nuestra plataforma.</p>
                        <ul class="space-y-2 mb-8 text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-hourglass text-amber-600 dark:text-amber-500 mt-1 mr-2"></i>
                                <span>Sistema avanzado de reservas</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-hourglass text-amber-600 dark:text-amber-500 mt-1 mr-2"></i>
                                <span>Pagos integrados multi-plataforma</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-hourglass text-amber-600 dark:text-amber-500 mt-1 mr-2"></i>
                                <span>Análisis de datos y predicciones</span>
                            </li>
                        </ul>
                        <button disabled class="inline-flex items-center justify-center w-full py-3 px-5 bg-gray-200 dark:bg-gray-700 text-gray-400 font-medium rounded-lg cursor-not-allowed opacity-75">
                            <i class="fas fa-clock mr-2"></i> Disponible Pronto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Developer Info Section -->
        @if(config('app.debug'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 flex items-center justify-center rounded-md bg-blue-100 dark:bg-blue-600/20 text-blue-600 dark:text-blue-400 mr-3 transition-colors duration-300">
                            <i class="fas fa-code text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Información de Desarrollo</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-gray-700 dark:text-gray-300 mb-2">Estás ejecutando:</p>
                            <div class="space-y-2">
                                <div class="flex items-center bg-gray-100 dark:bg-gray-800/50 rounded-lg p-3 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                                    <span class="w-8 h-8 flex items-center justify-center rounded-md bg-red-100 dark:bg-red-600/10 text-red-600 dark:text-red-400 mr-3 transition-colors duration-300">
                                        <i class="fab fa-laravel"></i>
                                    </span>
                                    <div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Laravel</div>
                                        <div class="font-medium text-gray-900 dark:text-white">v{{ Illuminate\Foundation\Application::VERSION }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center bg-gray-100 dark:bg-gray-800/50 rounded-lg p-3 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                                    <span class="w-8 h-8 flex items-center justify-center rounded-md bg-indigo-100 dark:bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 mr-3 transition-colors duration-300">
                                        <i class="fab fa-php"></i>
                                    </span>
                                    <div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">PHP</div>
                                        <div class="font-medium text-gray-900 dark:text-white">v{{ PHP_VERSION }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-gray-700 dark:text-gray-300 mb-2">Herramientas para desarrolladores:</p>
                            <div class="space-y-2 flex-grow">
                                <a href="/simple-endpoints" class="flex items-center bg-gray-100 dark:bg-gray-800/50 hover:bg-gray-200 dark:hover:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                                    <span class="w-8 h-8 flex items-center justify-center rounded-md bg-green-100 dark:bg-green-600/10 text-green-600 dark:text-green-400 mr-3 transition-colors duration-300">
                                        <i class="fas fa-list"></i>
                                    </span>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Ver Endpoints</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Explorar rutas API disponibles</div>
                                    </div>
                                    <i class="fas fa-chevron-right ml-auto text-gray-500 dark:text-gray-500"></i>
                                </a>
                                <a href="https://laravel.com/docs" target="_blank" class="flex items-center bg-gray-100 dark:bg-gray-800/50 hover:bg-gray-200 dark:hover:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                                    <span class="w-8 h-8 flex items-center justify-center rounded-md bg-blue-100 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 mr-3 transition-colors duration-300">
                                        <i class="fas fa-book"></i>
                                    </span>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Documentación</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Laravel & API docs</div>
                                    </div>
                                    <i class="fas fa-external-link-alt ml-auto text-gray-500 dark:text-gray-500"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <footer class="bg-gray-100 dark:bg-black/50 backdrop-blur-sm pt-12 mt-16 border-t border-gray-200 dark:border-gray-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center mb-4">
                            <span class="text-blue-600 dark:text-blue-500"><i class="fas fa-film text-2xl"></i></span>
                            <span class="ml-2 font-bold text-xl text-gray-900 dark:text-white">CineSystem</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 max-w-md">
                            Sistema profesional para la gestión integral de cines, películas, salas y más.
                            Diseñado para optimizar la experiencia tanto de administradores como de clientes.
                        </p>
                        <div class="flex space-x-4 mt-6">
                            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-800 hover:bg-blue-600 text-gray-700 dark:text-gray-300 hover:text-white transition-colors duration-200">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-800 hover:bg-blue-600 text-gray-700 dark:text-gray-300 hover:text-white transition-colors duration-200">
                                <i class="fab fa-github"></i>
                            </a>
                            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-800 hover:bg-blue-600 text-gray-700 dark:text-gray-300 hover:text-white transition-colors duration-200">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Enlaces Rápidos</h4>
                        <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                            <li><a href="/" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Inicio</a></li>
                            <li><a href="/cinemas" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Cines</a></li>
                            <li><a href="/movies" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Películas</a></li>
                            <li><a href="/simple-endpoints" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">API</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Contacto</h4>
                        <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-map-marker-alt mt-1 mr-2 text-blue-600 dark:text-blue-500"></i>
                                <span>Madrid, España</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-envelope mt-1 mr-2 text-blue-600 dark:text-blue-500"></i>
                                <span>info@cinesystem.com</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-phone mt-1 mr-2 text-blue-600 dark:text-blue-500"></i>
                                <span>+34 612 345 678</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="py-6 border-t border-gray-200 dark:border-gray-800 text-center text-gray-600 dark:text-gray-500 text-sm">
                    <p>Sistema de Cines &copy; {{ date('Y') }}. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
