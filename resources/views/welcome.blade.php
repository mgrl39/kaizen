<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="antialiased">
        @include('components.navbar')
        
        <div class="min-h-screen flex items-center justify-center">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">¡Hola Mundo!</h1>
        </div>
    </body>
</html>
