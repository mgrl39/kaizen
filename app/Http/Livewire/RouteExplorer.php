<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Route;

class RouteExplorer extends Component
{
    public $routes = [];
    public $search = '';
    public $selectedMethods = [
        'GET' => true,
        'POST' => true,
        'PUT' => true,
        'PATCH' => true,
        'DELETE' => true
    ];

    public function mount()
    {
        $this->loadRoutes();
    }

    public function loadRoutes()
    {
        $routeCollection = Route::getRoutes();
        $this->routes = [];

        foreach ($routeCollection as $route) {
            $methods = implode('|', $route->methods());
            if ($methods == 'HEAD') continue;
            
            $this->routes[] = [
                'method' => $methods,
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName()
            ];
        }
    }

    public function toggleMethod($method)
    {
        $this->selectedMethods[$method] = !$this->selectedMethods[$method];
    }

    public function getFilteredRoutesProperty()
    {
        return collect($this->routes)
            ->filter(function ($route) {
                // Filtrar por búsqueda
                if ($this->search && !str_contains(strtolower($route['uri']), strtolower($this->search))) {
                    return false;
                }
                
                // Filtrar por método HTTP seleccionado
                foreach ($this->selectedMethods as $method => $selected) {
                    if ($selected && str_contains($route['method'], $method)) {
                        return true;
                    }
                }
                return false;
            })
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.route-explorer', [
            'filteredRoutes' => $this->getFilteredRoutesProperty()
        ]);
    }
} 