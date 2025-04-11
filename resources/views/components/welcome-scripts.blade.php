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