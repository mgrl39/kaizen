<footer class="bg-gray-100 dark:bg-black/50 backdrop-blur-sm pt-8 mt-16 border-t border-gray-200 dark:border-gray-800 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-8">
            <div class="col-span-1">
                <div class="flex items-center mb-4">
                    <span class="text-blue-600 dark:text-blue-500"><i class="fas fa-film text-2xl"></i></span>
                    <span class="ml-2 font-bold text-xl text-gray-900 dark:text-white">Kaizen</span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    Sistema de reservas inteligente para una experiencia cinematográfica mejorada.
                </p>
                <div class="flex space-x-4 mt-6">
                    <a href="https://github.com/mgrl39" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-800 hover:bg-blue-600 text-gray-700 dark:text-gray-300 hover:text-white transition-colors duration-200">
                        <i class="fab fa-github"></i>
                    </a>
                </div>
            </div>
            
            <div x-data="{ links: [
                {name: 'Inicio', url: '/'},
                {name: 'Cines', url: '/cinemas'},
                {name: 'Películas', url: '/movies'}
            ]}">
                <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Enlaces</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <template x-for="link in links" :key="link.url">
                        <li>
                            <a :href="link.url" 
                               x-text="link.name"
                               class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                            </a>
                        </li>
                    </template>
                </ul>
            </div>

            <div>
                <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Contacto</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li class="flex items-center">
                        <i class="fas fa-envelope mr-2 text-blue-600 dark:text-blue-500"></i>
                        <span>soporte@kaizen.com</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="py-4 border-t border-gray-200 dark:border-gray-800 text-center text-gray-600 dark:text-gray-500 text-sm">
            <p>Kaizen © {{ date('Y') }} | Desarrollado con <i class="fas fa-heart text-red-500"></i> por mgrl39</p>
        </div>
    </div>
</footer>