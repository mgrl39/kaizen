@extends('layouts.app')

@section('title', 'Kaizen Cinema')

@section('content')
<!-- Hero Banner -->
<div class="p-4 p-md-5 mb-4 rounded text-white" 
     style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
            url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') center/cover;">
    <div class="row align-items-center">
        <div class="col-md-6 px-0">
            <h1 class="display-4 fw-bold">Kaizen Cinema</h1>
            <p class="lead my-3">La mejor experiencia cinematográfica con las últimas películas y los cines más confortables.</p>
            <p class="d-flex gap-2">
                <a href="/movies" class="btn btn-primary">Ver películas</a>
                <a href="/cinemas" class="btn btn-outline-light">Explorar cines</a>
            </p>
        </div>
    </div>
</div>

<!-- Películas destacadas -->
<h2 class="mb-3"><i class="bi bi-stars me-2 text-warning"></i>Películas destacadas</h2>

<div class="row g-4 mb-5">
    @php
    $movies = [
        ['id' => 1, 'title' => 'Dune', 'image' => 'https://image.tmdb.org/t/p/w500/jYEW5xZkZk2WTrdbMGAPFuBqbDc.jpg', 'rating' => 8.1],
        ['id' => 2, 'title' => 'Oppenheimer', 'image' => 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg', 'rating' => 8.4],
        ['id' => 3, 'title' => 'Barbie', 'image' => 'https://image.tmdb.org/t/p/w500/iuFNMS8U5cb6xfzi8Qzsk3e3hkS.jpg', 'rating' => 7.2],
    ];
    @endphp
    
    @foreach($movies as $movie)
        <div class="col-md-4">
            <div class="card h-100">
                <img src="{{ $movie['image'] }}" class="card-img-top" alt="{{ $movie['title'] }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $movie['title'] }}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary">
                            <i class="bi bi-star-fill me-1"></i>{{ $movie['rating'] }}
                        </span>
                        <a href="/movies/{{ $movie['id'] }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-ticket me-1"></i>Reservar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Categorías -->
<h2 class="mb-3"><i class="bi bi-grid me-2 text-primary"></i>Categorías</h2>

<div class="row g-4">
    @foreach([
        ['name' => 'Acción', 'icon' => 'bi-lightning', 'color' => 'danger'],
        ['name' => 'Comedia', 'icon' => 'bi-emoji-laughing', 'color' => 'warning'],
        ['name' => 'Drama', 'icon' => 'bi-mask', 'color' => 'info']
    ] as $category)
        <div class="col-md-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi {{ $category['icon'] }} fs-1 text-{{ $category['color'] }}"></i>
                    <h5 class="mt-3">{{ $category['name'] }}</h5>
                    <a href="/genre/{{ strtolower($category['name']) }}" class="btn btn-sm btn-{{ $category['color'] }} mt-2">
                        Ver películas
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicialización de sliders si existen
        if (document.querySelector('.swiper-container')) {
            new Swiper('.swiper-container', {
                slidesPerView: 'auto',
                spaceBetween: 20,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: { slidesPerView: 2 },
                    768: { slidesPerView: 3 },
                    1024: { slidesPerView: 4 }
                }
            });
        }
    });
</script>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" rel="stylesheet">
@endsection
