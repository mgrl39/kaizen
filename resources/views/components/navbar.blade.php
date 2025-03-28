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