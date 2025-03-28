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