<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Route;

class RouteExplorer extends Component
{
    public $routes = [];
    public $search = '';
    public $selectedMethods = ['GET' => true, 'POST' => true, 'PUT' => true, 'PATCH' => true, 'DELETE' => true];

    public function mount()
    {
        $this->loadRoutes();
    }

    public function loadRoutes()
    {
        $this->routes = collect(Route::getRoutes())
            ->map(
                function ($route) {
                    return [
                        'method' => implode('|', array_diff($route->methods(), ['HEAD'])),
                        'uri' => $route->uri(),
                        'name' => $route->getName(),
                        'action' => $route->getActionName()
                    ];
                }
            )
            ->filter(
                function ($route) {
                    return $route['method'] !== '';
                }
            )
            ->values()
            ->all();
    }

    public function toggleMethod($method)
    {
        $this->selectedMethods[$method] = !$this->selectedMethods[$method];
    }

    public function getFilteredRoutesProperty()
    {
        return collect($this->routes)
            ->filter(
                function ($route) {
                    if ($this->search && !str_contains(strtolower($route['uri']), strtolower($this->search))) {
                        return false;
                    }

                    return collect($this->selectedMethods)
                        ->filter()
                        ->keys()
                        ->contains(
                            function ($method) use ($route) {
                                return str_contains($route['method'], $method);
                            }
                        );
                }
            )
            ->values()
            ->all();
    }

    public function render()
    {
        return view(
            'livewire.route-explorer',
            [
            'filteredRoutes' => $this->getFilteredRoutesProperty()
        ]
        );
    }
}
