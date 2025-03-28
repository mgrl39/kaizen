<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>{{ config('app.name') }} - Page Not Found</title>
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>

    </head>
    <body class="bg-gray-50 min-h-screen">
        <div class="container mx-auto min-h-screen flex flex-col items-center justify-center px-4">
            <div class="text-center opacity-0 animate-[fadeIn_1s_ease-in-out_forwards]">
                <h1 class="text-8xl font-bold text-gray-800 mb-4 transform transition-transform duration-700 hover:scale-105">404</h1>
                <p class="text-2xl font-semibold text-gray-700 mb-2 transition-opacity duration-1000 delay-300">Page Not Found</p>
                <p class="text-lg text-gray-600 mb-8 transition-all duration-1000 delay-500">
                    The page you are looking for does not exist.
                </p>
                <a href="{{ url('/') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg shadow-md transition-all duration-300 ease-in-out hover:bg-blue-700 hover:shadow-lg transform hover:-translate-y-1">
                    Back to Home
                </a>
                
                @if(config('app.debug'))
                    <div class="mt-8 p-4 bg-gray-100 rounded-lg text-left text-sm text-gray-500 max-w-md">
                        <p class="font-semibold">URL: {{ request()->url() }}</p>
                        <p>Method: {{ request()->method() }}</p>
                    </div>
                @endif
            </div>
        </div>

        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </body>
</html>
