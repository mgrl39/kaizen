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
                <button 
                    wire:click="toggleMethod('GET')" 
                    class="px-3 py-1 rounded-full text-xs font-medium {{ $selectedMethods['GET'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                    GET
                </button>
                <button 
                    wire:click="toggleMethod('POST')" 
                    class="px-3 py-1 rounded-full text-xs font-medium {{ $selectedMethods['POST'] ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                    POST
                </button>
                <button 
                    wire:click="toggleMethod('PUT')" 
                    class="px-3 py-1 rounded-full text-xs font-medium {{ $selectedMethods['PUT'] ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                    PUT
                </button>
                <button 
                    wire:click="toggleMethod('PATCH')" 
                    class="px-3 py-1 rounded-full text-xs font-medium {{ $selectedMethods['PATCH'] ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                    PATCH
                </button>
                <button 
                    wire:click="toggleMethod('DELETE')" 
                    class="px-3 py-1 rounded-full text-xs font-medium {{ $selectedMethods['DELETE'] ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                    DELETE
                </button>
            </div>
        </div>
    </div>
    
    <!-- Lista de rutas -->
    <div class="space-y-2">
        @forelse($filteredRoutes as $route)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="p-4">
                    <div class="flex items-center">
                        @if(strpos($route['method'], 'GET') !== false)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                GET
                            </span>
                        @elseif(strpos($route['method'], 'POST') !== false)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                POST
                            </span>
                        @elseif(strpos($route['method'], 'PUT') !== false)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                PUT
                            </span>
                        @elseif(strpos($route['method'], 'PATCH') !== false)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                PATCH
                            </span>
                        @elseif(strpos($route['method'], 'DELETE') !== false)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                DELETE
                            </span>
                        @endif
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