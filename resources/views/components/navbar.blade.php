<nav class="bg-white/80 dark:bg-black/40 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800 fixed w-full z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <i class="fas fa-film text-2xl text-blue-500"></i>
                    <span class="ml-2 font-bold text-xl">Kaizen</span>
                </a>
            </div>
            <div class="hidden md:flex items-center space-x-6">
                @php
                $menuItems = [
                    ['url' => '/cinemas', 'icon' => 'fa-building', 'text' => 'Cines'],
                    ['url' => '/movies', 'icon' => 'fa-video', 'text' => 'Películas'], 
                    ['url' => '/login', 'icon' => 'fa-sign-in-alt', 'text' => 'Iniciar Sesión'],
                    ['url' => '/register', 'icon' => 'fa-user-plus', 'text' => 'Registrarse']
                ];
                @endphp

                @foreach($menuItems as $item)
                    <a href="{{ $item['url'] }}" class="flex items-center px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md">
                        <i class="fas {{ $item['icon'] }} mr-2"></i>{{ $item['text'] }}
                    </a>
                @endforeach

                <button @click="darkMode = !darkMode" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700">
                    <i class="fas fa-sun text-yellow-500 dark:hidden"></i>
                    <i class="fas fa-moon text-blue-300 hidden dark:block"></i>
                </button>
            </div>
            <div class="flex items-center md:hidden">
                <button @click="darkMode = !darkMode" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 mr-2">
                    <i class="fas fa-sun text-yellow-500 dark:hidden"></i>
                    <i class="fas fa-moon text-blue-300 hidden dark:block"></i>
                </button>
                <button type="button" x-data="{open: false}" @click="open = !open">
                    <i x-show="!open" class="fas fa-bars text-xl"></i>
                    <i x-show="open" class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</nav>