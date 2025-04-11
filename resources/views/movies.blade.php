@extends('layouts.app')

@section('title', 'Películas')

@section('styles')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
.movie-card {
transition: all 0.3s ease;
background: rgba(255, 255, 255, 0.05) !important;
backdrop-filter: blur(10px);
border: 1px solid rgba(255, 255, 255, 0.1) !important;
cursor: pointer;
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
    <h1 class="text-center mb-5 text-white">
        <i class="fa-solid fa-film me-2 text-primary"></i>
        Lista de Películas
    </h1>

    <!-- Grid de películas -->
    <div class="row g-4" x-show="!loading && !error && moviesList.length > 0">
        <template x-for="movie in moviesList" :key="movie.id">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 movie-card" @click="window.open(`/movies/${movie.id}`, '_blank')">
                    <img :src="movie.photo_url" :alt="movie.title"
                        class="card-img-top" style="height: 300px; object-fit: cover;">
                    <div class="card-body text-white">
                        <h5 class="card-title" x-text="movie.title"></h5>
                        <p class="card-text text-white-50" x-text="movie.synopsis"></p>
                        <div class="d-flex justify-content-between">
                            <small><i class="fas fa-clock me-1"></i><span x-text="movie.duration + ' min'"></span></small>
                            <small><i class="fas fa-calendar me-1"></i><span x-text="formatDate(movie.release_date)"></span></small>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading -->
    <div class="text-center py-5" x-show="loading">
        <div class="spinner-border text-primary"></div>
        <p class="text-white mt-2">Cargando películas...</p>
    </div>

    <!-- Error message -->
    <div class="alert alert-danger" x-show="error" x-text="error" role="alert"></div>

    <!-- Empty state -->
    <div class="text-center py-5" x-show="!loading && !error && moviesList.length === 0">
        <i class="fas fa-film-slash fa-3x mb-3 text-white-50"></i>
        <p class="h4 text-white">No hay películas disponibles</p>
    </div>
</div>
@endsection

@section('scripts')
<!-- <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>-->
<!-- TODO: npm install -->
<script>
<!-- TODO: alpine -->
    document.addEventListener('alpine:init', () => {
        Alpine.data('movies', () => ({
            moviesList: [],
            loading: true,
            error: null,

            async init() {
                try {
                    await this.loadMovies();
                    AOS.init({ duration: 800, once: true });
                } catch (error) {
                    this.error = 'Error al inicializar la página';
                }
            },

            async loadMovies() {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const response = await fetch('/api/movies', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error(`Error al cargar las películas (${response.status})`);
                    }

                    const data = await response.json();

                    if (data.success && Array.isArray(data.data)) this.moviesList = data.data;
                    else if (Array.isArray(data)) this.moviesList = data;
                    else throw new Error('El formato de los datos recibidos no es válido');
                    if (this.moviesList.length === 0) this.error = 'No hay películas disponibles';

                } catch (error) {
                    this.error = 'No se pudieron cargar las películas. Por favor, intenta de nuevo más tarde.';
                    this.moviesList = [];
                } finally {
                    this.loading = false;
                }
            },

            formatDate(date) {
                if (!date) return '';
                return new Date(date).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        }));
    });
</script>
@endsection

