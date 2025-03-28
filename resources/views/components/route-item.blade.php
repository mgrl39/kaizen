@props(['route'])

<div class="group transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800/70 border-l-4 
    @if(strpos($route['method'], 'GET') !== false) border-green-500
    @elseif(strpos($route['method'], 'POST') !== false) border-blue-500
    @elseif(strpos($route['method'], 'PUT') !== false || strpos($route['method'], 'PATCH') !== false) border-amber-500
    @elseif(strpos($route['method'], 'DELETE') !== false) border-red-500
    @else border-gray-500
    @endif
    bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/30 mb-2 overflow-hidden">
    
    <div class="p-4 flex items-center gap-4">
        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full 
            @if(strpos($route['method'], 'GET') !== false) bg-green-100 dark:bg-green-900/20 text-green-600 dark:text-green-400
            @elseif(strpos($route['method'], 'POST') !== false) bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400
            @elseif(strpos($route['method'], 'PUT') !== false || strpos($route['method'], 'PATCH') !== false) bg-amber-100 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400
            @elseif(strpos($route['method'], 'DELETE') !== false) bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400
            @else bg-gray-100 dark:bg-gray-900/20 text-gray-600 dark:text-gray-400
            @endif">
            
            @if(strpos($route['method'], 'GET') !== false)
                <i class="fas fa-arrow-down text-lg"></i>
            @elseif(strpos($route['method'], 'POST') !== false)
                <i class="fas fa-plus text-lg"></i>
            @elseif(strpos($route['method'], 'PUT') !== false || strpos($route['method'], 'PATCH') !== false)
                <i class="fas fa-edit text-lg"></i>
            @elseif(strpos($route['method'], 'DELETE') !== false)
                <i class="fas fa-trash text-lg"></i>
            @else
                <i class="fas fa-code text-lg"></i>
            @endif
        </div>
        
        <div class="flex-grow">
            <div class="font-mono text-sm px-3 py-1 rounded inline-block 
                @if(strpos($route['method'], 'GET') !== false) bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                @elseif(strpos($route['method'], 'POST') !== false) bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                @elseif(strpos($route['method'], 'PUT') !== false || strpos($route['method'], 'PATCH') !== false) bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300
                @elseif(strpos($route['method'], 'DELETE') !== false) bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                @else bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300
                @endif">
                {{ $route['method'] }}
            </div>
            
            <div class="mt-2 font-mono text-gray-800 dark:text-gray-200 break-all">
                {{ $route['url'] }}
            </div>
        </div>
        
        <div class="flex-shrink-0 text-gray-400 dark:text-gray-500 opacity-0 group-hover:opacity-100 transition-opacity">
            <i class="fas fa-chevron-right"></i>
        </div>
    </div>
</div> 