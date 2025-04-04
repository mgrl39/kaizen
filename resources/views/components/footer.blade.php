<footer class="bg-white dark:bg-gray-900 shadow-sm border-t border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <!-- Logo y nombre -->
            <div class="flex items-center space-x-2">
                <span class="text-blue-600 dark:text-blue-500">
                    <i class="fas fa-film"></i>
                </span>
                <span class="font-semibold text-gray-900 dark:text-white">
                    {{ __('Kaizen') }}
                </span>
            </div>

            <!-- Enlaces principales -->
            <nav class="flex space-x-6">
                <a href="/" class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500 transition-colors duration-200">
                    {{ __('Inicio') }}
                </a>
                <a href="/cinemas" class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500 transition-colors duration-200">
                    {{ __('Cines') }}
                </a>
                <a href="/movies" class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500 transition-colors duration-200">
                    {{ __('Películas') }}
                </a>
            </nav>

            <!-- Redes sociales y copyright -->
            <div class="flex items-center space-x-4">
                <a href="https://github.com/mgrl39" target="_blank" class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500 transition-colors duration-200">
                    <i class="fab fa-github"></i>
                </a>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    © {{ date('Y') }} {{ __('Kaizen') }}
                </span>
            </div>
        </div>
    </div>
</footer>