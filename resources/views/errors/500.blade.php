<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>{{ config('app.name') }} - Error del servidor</title>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 min-h-screen">
        <div x-data="{ show: false }" 
             x-init="setTimeout(() => show = true, 100)"
             class="container mx-auto min-h-screen flex flex-col items-center justify-center px-4">
            <div x-show="show" 
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="text-center">
                <h1 class="text-8xl font-bold text-gray-800 mb-4 hover:scale-105 transition duration-300">500</h1>
                <p class="text-2xl font-semibold text-gray-700 mb-2">Error del servidor</p>
                <p class="text-lg text-gray-600 mb-8">
                    Ha ocurrido un error en el servidor.
                </p>
                <a href="{{ url('/') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    Volver al inicio
                </a>
                
                @if(config('app.debug'))
                    <div class="mt-8 p-4 bg-gray-100 rounded-lg text-left text-sm text-gray-500 max-w-md">
                        <p class="font-semibold">URL: {{ request()->url() }}</p>
                        <p>Método: {{ request()->method() }}</p>
                    </div>
                @endif
            </div>
        </div>
    </body>
</html>
