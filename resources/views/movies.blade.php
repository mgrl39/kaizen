@extends('layouts.app')

@section('title', 'Películas')

@section('content')
<h1 class="mb-4"><i class="bi bi-film me-2 text-primary"></i>Películas</h1>

<div class="row g-4">
    @php
    $movies = [
        ['id' => 1, 'title' => 'Dune', 'image' => 'https://image.tmdb.org/t/p/w500/jYEW5xZkZk2WTrdbMGAPFuBqbDc.jpg', 'rating' => 8.1, 'date' => '2021-10-01'],
        ['id' => 2, 'title' => 'Oppenheimer', 'image' => 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg', 'rating' => 8.4, 'date' => '2023-07-21'],
        ['id' => 3, 'title' => 'Barbie', 'image' => 'https://image.tmdb.org/t/p/w500/iuFNMS8U5cb6xfzi8Qzsk3e3hkS.jpg', 'rating' => 7.2, 'date' => '2023-07-21'],
        ['id' => 4, 'title' => 'Aquaman', 'image' => 'https://image.tmdb.org/t/p/w500/xLPffWMhMj1l50ND3KchMjYoKmE.jpg', 'rating' => 6.9, 'date' => '2023-12-20'],
        ['id' => 5, 'title' => 'The Marvels', 'image' => 'https://image.tmdb.org/t/p/w500/Ag3D9qXjhJ2FUkrlJ0Cv1pgxqYQ.jpg', 'rating' => 6.3, 'date' => '2023-11-10'],
        ['id' => 6, 'title' => 'Napoleon', 'image' => 'https://image.tmdb.org/t/p/w500/jE5o7y9K6pZtWNNMEw3IdpHuncR.jpg', 'rating' => 7.5, 'date' => '2023-11-22'],
    ];
    @endphp
    
    @foreach($movies as $movie)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <img src="{{ $movie['image'] }}" class="card-img-top" alt="{{ $movie['title'] }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $movie['title'] }}</h5>
                    <p class="card-text small">Lanzamiento: {{ \Carbon\Carbon::parse($movie['date'])->format('d/m/Y') }}</p>
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
@endsection

