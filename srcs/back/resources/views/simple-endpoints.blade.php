@extends('layouts.app')

@section('title', 'API Endpoints')

@section('content')
<div class="mb-4">
    <h1 class="fs-4 fw-semibold mb-4">API Endpoints</h1>

    <div class="border rounded mb-4">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 p-4">
            @foreach($routes as $route)
                @php
                    $methods = array_diff($route['methods'], ['HEAD']);
                    if (empty($methods)) continue;
                @endphp
                <div class="col">
                    <div class="card h-100 border">
                        <div class="card-body p-3">
                            <div class="mb-2">
                                <span class="badge bg-light text-dark me-2 small rounded">
                                    {{ implode('|', $methods) }}
                                </span>
                                <code class="small text-secondary text-truncate">
                                    {{ $route->uri() }}
                                </code>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small">
                                @if($route->getName())
                                    <span class="text-muted text-truncate">
                                        {{ $route->getName() }}
                                    </span>
                                @endif
                                <code class="text-muted small text-truncate">
                                    {{ class_basename($route->getActionName()) }}
                                </code>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="text-center">
        <a href="/" class="text-decoration-none small text-muted">
            ← Volver
        </a>
    </div>
</div>
@endsection
