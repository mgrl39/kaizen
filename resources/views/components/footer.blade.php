<footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 mt-auto">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <div class="flex items-center space-x-2">
                <i class="fas fa-film text-blue-600 dark:text-blue-500"></i>
                <span class="font-semibold text-gray-900 dark:text-white">{{ __('Kaizen') }}</span>
            </div>

            <nav class="flex space-x-6">
                @foreach(['/' => 'Inicio', '/cinemas' => 'Cines', '/movies' => 'Películas'] as $url => $label)
                    <a href="{{ $url }}" class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500">{{ __($label) }}</a>
                @endforeach
            </nav>

            <div class="flex items-center space-x-4">
                <a href="https://github.com/mgrl39" target="_blank" class="text-gray-600 hover:text-blue-600 dark:text-gray-400"><i class="fab fa-github"></i></a>
                <a href="https://opensource.org/licenses/MIT" target="_blank" class="text-sm text-gray-500 hover:text-blue-600">{{ __('MIT License') }}</a>
            </div>
        </div>
    </div>
</footer>