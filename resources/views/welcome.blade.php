@extends('layouts.app')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
<style>
:root {
    --swiper-theme-color: #4433ff;
}
.hero-slide { height: 70vh; }
.card-hover { transition: transform 0.3s ease-in-out; }
.card-hover:hover { transform: translateY(-5px); }
.gradient-overlay {
    background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%);
}
</style>
@endpush

@section('content')
<!-- Hero Slider -->
<div class="swiper hero-swiper mb-5">
    <div class="swiper-wrapper" id="heroSlider"></div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
</div>

<!-- Promo Section -->
<div class="container mb-5">
    <div class="card bg-dark text-white shadow-lg border-0 overflow-hidden">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-md-6 p-5">
                    <div class="badge bg-danger mb-3">Oferta Especial</div>
                    <h2 class="display-6 fw-bold mb-3">2x1 en Entradas</h2>
                    <p class="lead mb-4">Todos los martes y miércoles, ¡no te lo pierdas!</p>
                    <a href="/movies" class="btn btn-light btn-lg rounded-pill shadow-sm">
                        Ver Películas <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="col-md-6 position-relative">
                    <div class="h-100 w-100" style="background: url('https://picsum.photos/800/400') center/cover;"></div>
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-dark opacity-50"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Movies -->
<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">
            <i class="fas fa-star text-warning me-2"></i>Top Películas
        </h2>
        <a href="/movies" class="btn btn-outline-light btn-floating">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    
    <div class="swiper top-movies-swiper">
        <div class="swiper-wrapper" id="topMoviesSlider"></div>
    </div>
</div>

<!-- Categories Section -->
<div class="container mb-5">
    <div class="row g-4">
        @foreach(['Acción', 'Comedia', 'Drama', 'Sci-Fi'] as $category)
        <div class="col-md-3">
            <div class="card bg-dark text-white card-hover h-100 border-0">
                <div class="card-body text-center">
                    <i class="fas fa-film fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">{{ $category }}</h5>
                    <p class="card-text text-muted">Explora películas de {{ $category }}</p>
                    <a href="/movies?category={{ $category }}" class="btn btn-outline-light btn-floating mt-2">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar Swipers
    const heroSwiper = new Swiper('.hero-swiper', {
        loop: true,
        effect: 'fade',
        autoplay: {
            delay: 5000,
            disableOnInteraction: false
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        }
    });

    const topMoviesSwiper = new Swiper('.top-movies-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        grabCursor: true,
        breakpoints: {
            640: { slidesPerView: 2 },
            768: { slidesPerView: 3 },
            1024: { slidesPerView: 4 }
        }
    });

    // Cargar datos
    const loadMovies = async () => {
        try {
            const response = await fetch('/api/movies');
            const { data } = await response.json();
            
            // Hero Slider
            const heroHTML = data.slice(0, 5).map(movie => `
                <div class="swiper-slide hero-slide">
                    <div class="position-relative h-100">
                        <img src="${movie.photo_url}" class="w-100 h-100 object-fit-cover">
                        <div class="position-absolute bottom-0 start-0 w-100 p-5 gradient-overlay">
                            <div class="container">
                                <h2 class="display-4 fw-bold mb-3">${movie.title}</h2>
                                <p class="lead mb-4">${movie.synopsis.substring(0, 150)}...</p>
                                <button class="btn btn-primary btn-lg rounded-pill" 
                                        onclick="location.href='/movies/${movie.id}'">
                                    <i class="fas fa-play me-2"></i>Ver Detalles
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Top Movies
            const topHTML = data.slice(0, 8).map(movie => `
                <div class="swiper-slide" style="width: 250px;">
                    <div class="card bg-dark text-white card-hover border-0">
                        <img src="${movie.photo_url}" class="card-img-top" style="height: 350px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title mb-2">${movie.title}</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="rating text-warning">
                                    ${'★'.repeat(Math.floor(Math.random() * 2 + 3))}
                                </div>
                                <small class="text-muted">${movie.duration}min</small>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            document.getElementById('heroSlider').innerHTML = heroHTML;
            document.getElementById('topMoviesSlider').innerHTML = topHTML;
            
            heroSwiper.update();
            topMoviesSwiper.update();
        } catch (error) {
            console.error('Error cargando películas:', error);
        }
    };

    loadMovies();
});
</script>
@endpush