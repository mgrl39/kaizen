@extends('layouts.app')

@section('title', 'API Endpoints')

@section('content')
<div class="space-y-4">
    <h1 class="text-xl font-semibold">API Endpoints</h1>

    <!-- Lista de rutas -->
    <div class="border rounded">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
            @foreach(Route::getRoutes() as $route)
                @php
                    $methods = array_diff($route->methods(), ['HEAD']);
                    if (empty($methods)) continue;
                @endphp
                
                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col space-y-2">
                        <div class="flex items-center">
                            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                                {{ implode('|', $methods) }}
                            </code>
                            <code class="ml-3 text-sm text-gray-600 dark:text-gray-400 truncate">
                                {{ $route->uri() }}
                            </code>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            @if($route->getName())
                                <span class="text-gray-500 truncate max-w-[150px]">
                                    {{ $route->getName() }}
                                </span>
                            @endif
                            <code class="text-gray-400 font-mono truncate">
                                {{ class_basename($route->getActionName()) }}
                            </code>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="text-center">
        <a href="/" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            ← Volver
        </a>
    </div>
</div>
@endsection