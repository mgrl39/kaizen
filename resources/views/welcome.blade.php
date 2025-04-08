@extends('layouts.app')

@section('content')
<!-- Slider Principal -->
<div class="swiper main-slider mb-5">
    <div class="swiper-wrapper">
        <!-- Las películas se cargarán aquí dinámicamente -->
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>

<!-- Banner Promocional -->
<div class="promo-banner mb-5 animate__animated animate__fadeIn">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="display-6 fw-bold mb-3" style="color: var(--text-primary);">
                    <i class="fa-solid fa-ticket me-2" style="color: var(--primary-color);"></i>
                    ¡Oferta Especial!
                </h2>
                <p class="lead mb-4" style="color: var(--text-secondary);">
                    Disfruta de un 20% de descuento en todas las entradas los martes y miércoles.
                </p>
                <a href="/movies" class="btn btn-primary btn-lg">
                    Ver Películas
                    <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-md-4 d-none d-md-block">
                <img src="https://via.placeholder.com/400x200" alt="Oferta Especial" class="img-fluid rounded-3" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            </div>
        </div>
    </div>
</div>

<!-- Top Películas -->
<div class="container mb-5">
    <h2 class="mb-4 animate__animated animate__fadeIn" style="color: var(--text-primary);">
        <i class="fa-solid fa-star me-2" style="color: var(--primary-color);"></i>
        Top Películas
    </h2>
    <div class="swiper top-movies-slider">
        <div class="swiper-wrapper">
            <!-- Las películas se cargarán aquí dinámicamente -->
        </div>
    </div>
</div>

<style>
.main-slider {
    height: 500px;
    background-color: var(--secondary-bg);
}

.main-slider .swiper-slide {
    position: relative;
    overflow: hidden;
}

.main-slider .swiper-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.main-slider .swiper-slide-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 2rem;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    color: white;
}

.top-movies-slider {
    padding: 1rem 0;
}

.top-movies-slider .swiper-slide {
    width: 200px;
    transition: transform 0.3s ease;
}

.top-movies-slider .swiper-slide:hover {
    transform: scale(1.05);
}

.promo-banner {
    background-color: var(--card-bg);
    padding: 3rem 0;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.swiper-button-next,
.swiper-button-prev {
    color: var(--primary-color) !important;
}

.swiper-pagination-bullet-active {
    background: var(--primary-color) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Swiper para el slider principal
    const mainSwiper = new Swiper('.main-slider', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });

    // Inicializar Swiper para el slider de Top Películas
    const topMoviesSwiper = new Swiper('.top-movies-slider', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        freeMode: true,
        mousewheel: true,
    });

    // Cargar películas para el slider principal
    async function loadMainSliderMovies() {
        try {
            const response = await fetch('/api/movies?limit=5');
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                const sliderWrapper = document.querySelector('.main-slider .swiper-wrapper');
                sliderWrapper.innerHTML = '';
                
                data.data.forEach(movie => {
                    const slide = document.createElement('div');
                    slide.className = 'swiper-slide';
                    slide.innerHTML = `
                        <img src="${movie.photo_url}" alt="${movie.title}">
                        <div class="swiper-slide-content">
                            <h3 class="h2 mb-2">${movie.title}</h3>
                            <p class="mb-0">${movie.synopsis.substring(0, 150)}...</p>
                        </div>
                    `;
                    sliderWrapper.appendChild(slide);
                });
                
                mainSwiper.update();
            }
        } catch (error) {
            console.error('Error al cargar películas para el slider:', error);
        }
    }

    // Cargar películas para el Top Movies
    async function loadTopMovies() {
        try {
            const response = await fetch('/api/movies?limit=10');
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                const sliderWrapper = document.querySelector('.top-movies-slider .swiper-wrapper');
                sliderWrapper.innerHTML = '';
                
                data.data.forEach(movie => {
                    const slide = document.createElement('div');
                    slide.className = 'swiper-slide';
                    slide.innerHTML = `
                        <div class="card h-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                            <img src="${movie.photo_url}" class="card-img-top" alt="${movie.title}" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title" style="color: var(--text-primary);">${movie.title}</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fa-solid fa-star" style="color: var(--primary-color);"></i>
                                        ${(Math.random() * 2 + 3).toFixed(1)}
                                    </small>
                                </div>
                            </div>
                        </div>
                    `;
                    sliderWrapper.appendChild(slide);
                });
                
                topMoviesSwiper.update();
            }
        } catch (error) {
            console.error('Error al cargar películas top:', error);
        }
    }

    // Cargar los datos
    loadMainSliderMovies();
    loadTopMovies();
});
</script>
@endsection