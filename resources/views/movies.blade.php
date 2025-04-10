@extends('layouts.app')

@section('title', 'Películas')

@section('styles')R
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
.movie-card {
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.05) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}
.movie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}
.movie-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.movie-card:hover .movie-overlay { opacity: 1; }
</style>
@endsection

@section('content')
<div class="container py-5" x-data="movies">
    <h1 class="text-center mb-5 text-white" data-aos="fade-down">
        <i class="fa-solid fa-film me-2 text-primary"></i>
        Lista de Películas
    </h1>

    <!-- Grid de películas -->
    <div class="row g-4" id="movies">
        <template x-for="movie in moviesList" :key="movie.id">
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="card h-100 movie-card" @click="selectMovie(movie)">
                    <div class="position-relative">
                        <img :src="movie.photo_url" :alt="movie.title" 
                             class="card-img-top" style="height: 300px; object-fit: cover;">
                        <div class="movie-overlay">
                            <button class="btn btn-primary rounded-pill px-4">
                                <i class="fa-solid fa-info-circle me-2"></i>Ver Detalles
                            </button>
                        </div>
                    </div>
                    <div class="card-body text-white">
                        <h5 class="card-title" x-text="movie.title"></h5>
                        <p class="card-text text-white-50" x-text="truncate(movie.synopsis, 100)"></p>
                        <div class="d-flex justify-content-between align-items-center text-white-50">
                            <small><i class="fa-solid fa-clock me-1"></i><span x-text="movie.duration + ' min'"></span></small>
                            <small><i class="fa-solid fa-calendar me-1"></i><span x-text="formatDate(movie.release_date)"></span></small>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading -->
    <div class="text-center py-5" x-show="loading">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="text-white mt-3">Cargando películas...</p>
    </div>

    <!-- Mensaje vacío -->
    <div class="text-center py-5 text-white" x-show="!loading && moviesList.length === 0">
        <i class="fa-solid fa-film-slash fa-3x mb-3"></i>
        <p class="h4">No hay películas disponibles en este momento.</p>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="movieModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content glass-card" x-show="selectedMovie">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" x-text="selectedMovie?.title"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img :src="selectedMovie?.photo_url" :alt="selectedMovie?.title" 
                                 class="img-fluid rounded" style="width: 100%; height: 400px; object-fit: cover;">
                        </div>
                        <div class="col-md-8 text-white">
                            <p x-text="selectedMovie?.synopsis"></p>
                            <div class="movie-info">
                                <div class="mb-2">
                                    <i class="fa-solid fa-clock me-2 text-primary"></i>
                                    <span x-text="selectedMovie?.duration + ' minutos'"></span>
                                </div>
                                <div class="mb-3">
                                    <i class="fa-solid fa-calendar me-2 text-primary"></i>
                                    <span x-text="formatDate(selectedMovie?.release_date)"></span>
                                </div>
                                <button class="btn btn-primary rounded-pill px-4" @click="reserveMovie">
                                    <i class="fa-solid fa-ticket me-2"></i>Reservar Entrada
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('movies', () => ({
        moviesList: [],
        loading: true,
        selectedMovie: null,
        modal: null,

        async init() {
            AOS.init({ duration: 800, once: true });
            this.modal = new bootstrap.Modal(document.getElementById('movieModal'));
            await this.loadMovies();
        },

        async loadMovies() {
            try {
                const response = await fetch('/api/movies');
                const data = await response.json();
                console.log('Datos recibidos:', data); // Debug
                this.moviesList = data.success ? data.data : [];
            } catch (error) {
                console.error('Error al cargar películas:', error);
                this.moviesList = [];
            } finally {
                this.loading = false;
            }
        },

        selectMovie(movie) {
            this.selectedMovie = movie;
            this.modal.show();
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString();
        },

        truncate(text, length) {
            return text?.length > length ? text.substring(0, length) + '...' : text;
        },

        reserveMovie() {
            alert('Función de reserva en desarrollo');
        }
    }));
});
</script>
@endsection

