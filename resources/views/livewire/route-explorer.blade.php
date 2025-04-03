<div>
    <!-- Filtros de rutas -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 space-y-4">
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar ruta</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    wire:model.debounce.300ms="search" 
                    type="text" 
                    id="search" 
                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-12 py-2 sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white rounded-md"
                    placeholder="Buscar por URI...">
            </div>
        </div>
        
        <div>
            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filtrar por método HTTP</span>
            <div class="flex flex-wrap gap-2">
                @php
                $methodColors = [
                    'GET' => ['bg' => 'green', 'text' => 'green'],
                    'POST' => ['bg' => 'blue', 'text' => 'blue'],
                    'PUT' => ['bg' => 'yellow', 'text' => 'yellow'],
                    'PATCH' => ['bg' => 'purple', 'text' => 'purple'],
                    'DELETE' => ['bg' => 'red', 'text' => 'red']
                ];
                @endphp

                @foreach($methodColors as $method => $colors)
                    <button 
                        wire:click="toggleMethod('{{ $method }}')"
                        class="px-3 py-1 rounded-full text-xs font-medium {{ $selectedMethods[$method] ? 'bg-'.$colors['bg'].'-100 text-'.$colors['text'].'-800 dark:bg-'.$colors['bg'].'-900 dark:text-'.$colors['text'].'-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                        {{ $method }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Lista de rutas -->
    <div class="space-y-2">
        @forelse($filteredRoutes as $route)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="p-4">
                    <div class="flex items-center">
                        @foreach($methodColors as $method => $colors)
                            @if(strpos($route['method'], $method) !== false)
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $colors['bg'] }}-100 text-{{ $colors['text'] }}-800 dark:bg-{{ $colors['bg'] }}-900 dark:text-{{ $colors['text'] }}-200">
                                    {{ $method }}
                                </span>
                                @break
                            @endif
                        @endforeach
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $route['uri'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                @if($route['name'])
                                    Nombre: {{ $route['name'] }}
                                @else
                                    <span class="italic">Sin nombre</span>
                                @endif
                            </div>
                        </div>
                        <div class="ml-auto text-xs text-gray-500 dark:text-gray-400">
                            {{ $route['action'] }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
                <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">No se encontraron rutas con los filtros aplicados.</p>
            </div>
        @endforelse
    </div>
</div>