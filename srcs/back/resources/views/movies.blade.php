@extends('layouts.app')

@section('title', 'Películas')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Películas</h1>
    
    <div class="row g-4">
        @if(isset($error))
            <div class="col-12 text-center">
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            </div>
        @elseif(empty($movies))
            <div class="col-12 text-center">
                <p>No hay películas disponibles</p>
            </div>
        @else
            @foreach($movies as $movie)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <img src="{{ $movie['photo_url'] ?? $movie['poster'] ?? '/img/default-movie.jpg' }}" 
                             class="card-img-top" alt="{{ $movie['title'] }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $movie['title'] }}</h5>
                            <p class="card-text small">
                                Lanzamiento: {{ isset($movie['release_date']) ? \Carbon\Carbon::parse($movie['release_date'])->format('d/m/Y') : 'Fecha no disponible' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">
                                    <i class="bi bi-star-fill me-1"></i>{{ $movie['rating'] ?? '?' }}
                                </span>
                                <a href="/movies/{{ $movie['id'] }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-ticket me-1"></i>Reservar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection