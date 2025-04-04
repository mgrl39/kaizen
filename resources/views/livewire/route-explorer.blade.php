<div class="space-y-4">
    <!-- Barra de búsqueda simple -->
    <div class="bg-white dark:bg-gray-900 p-4 rounded-lg">
        <input 
            wire:model.debounce.300ms="search" 
            type="text" 
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md"
            placeholder="Buscar ruta...">
            
        <!-- Filtros de método HTTP simplificados -->
        <div class="flex gap-2 mt-3">
            @foreach(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method)
                <button 
                    wire:click="toggleMethod('{{ $method }}')"
                    class="px-2 py-1 text-xs rounded {{ $selectedMethods[$method] 
                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' 
                        : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                    {{ $method }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Lista de rutas simplificada -->
    <div class="space-y-2">
        @forelse($filteredRoutes as $route)
            <div class="bg-white dark:bg-gray-900 p-3 rounded-lg border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-xs font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                            {{ $route['method'] }}
                        </span>
                        <span class="text-sm font-mono text-gray-600 dark:text-gray-400">
                            {{ $route['uri'] }}
                        </span>
                    </div>
                    @if($route['name'])
                        <span class="text-xs text-gray-500 dark:text-gray-500">
                            {{ $route['name'] }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                No se encontraron rutas
            </div>
        @endforelse
    </div>
</div>