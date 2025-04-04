<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Registro') }}</title>
    <!-- TODO: En el tailwind meterlo directamente escriot -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex min-h-screen">
        <!-- Formulario -->
        <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-sm space-y-6">
                <!-- Logo -->
                <div class="flex justify-center">
                    <i class="fas fa-film text-blue-600 text-3xl"></i>
                </div>

                <!-- Título -->
                <h2 class="text-center text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ __('Crear cuenta') }}
                </h2>

                <!-- Formulario -->
                <form method="POST" action="#" class="space-y-4">
                    @csrf

                    <input type="text"
                           name="name"
                           placeholder="{{ __('Nombre') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white">

                    <input type="email"
                           name="email"
                           placeholder="{{ __('Email') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white">

                    <input type="password"
                           name="password"
                           placeholder="{{ __('Contraseña') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white">

                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Registrarse') }}
                    </button>
                </form>

                <!-- Enlace a login -->
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('¿Ya tienes cuenta?') }}
                    <a href="/login" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
                        {{ __('Iniciar sesión') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
