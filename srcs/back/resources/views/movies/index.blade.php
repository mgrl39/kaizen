@extends('layouts.app')

@section('title', 'Películas Disponibles')

@section('content')
<div class="container">
    <h1 class="mb-4">Películas Disponibles</h1>
    
    <div class="row g-4">
        @if(empty($movies) || $movies->isEmpty())
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>No hay películas disponibles en este momento.
                </div>
            </div>
        @else
            @foreach($movies as $movie)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">{{ $movie->title }}</h5>
                                <span class="badge bg-primary rounded-pill">{{ $movie->duration }} min</span>
                            </div>
                            
                            <p class="text-muted small mb-3">
                                <i class="bi bi-tag me-1"></i>{{ $movie->genre }}
                            </p>
                            
                            @if(isset($movie->synopsis))
                                <p class="card-text mb-3 small">{{ Str::limit($movie->synopsis, 120) }}</p>
                            @endif
                            
                            <div class="text-end">
                                <a href="{{ url('/movies/'.$movie->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-info-circle me-1"></i>Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease;
        background-color: rgba(255, 255, 255, 0.9);
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }
    
    .badge {
        background: linear-gradient(135deg, #7B68EE, #4D9FE3);
        font-weight: 500;
    }
</style>
@endsection
