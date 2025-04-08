@extends('layouts.app')

@section('title', 'Películas')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5 animate__animated animate__fadeInDown" style="color: var(--text-primary);">
        <i class="fa-solid fa-film me-2" style="color: var(--primary-color);"></i>
        Lista de Películas
    </h1>

    <div id="movies" class="row g-4">
        <!-- Las películas se cargarán aquí dinámicamente -->
    </div>

    <div id="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <div id="empty-message" class="text-center py-5 d-none" style="color: var(--text-secondary);">
        <i class="fa-solid fa-film-slash fa-3x mb-3"></i>
        <p class="h4">No hay películas disponibles en este momento.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const moviesContainer = document.getElementById('movies');
    const loadingElement = document.getElementById('loading');
    const emptyMessage = document.getElementById('empty-message');

    async function loadMovies() {
        try {
            const response = await fetch('/api/movies');
            const data = await response.json();

            loadingElement.classList.add('d-none');

            if (!data.success || data.count === 0) {
                emptyMessage.classList.remove('d-none');
                return;
            }

            data.data.forEach(movie => {
                const movieCard = document.createElement('div');
                movieCard.className = 'col-md-6 col-lg-4 animate__animated animate__fadeInUp';
                movieCard.innerHTML = `
                    <div class="card h-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                        <img src="${movie.photo_url}" class="card-img-top" alt="${movie.title}" style="height: 300px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: var(--text-primary);">${movie.title}</h5>
                            <p class="card-text" style="color: var(--text-secondary);">${movie.synopsis}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fa-solid fa-clock me-1"></i>
                                    ${movie.duration} min
                                </small>
                                <small class="text-muted">
                                    <i class="fa-solid fa-calendar me-1"></i>
                                    ${new Date(movie.release_date).toLocaleDateString()}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
                moviesContainer.appendChild(movieCard);
            });
        } catch (error) {
            console.error('Error al cargar películas:', error);
            loadingElement.classList.add('d-none');
            emptyMessage.classList.remove('d-none');
            emptyMessage.innerHTML = `
                <i class="fa-solid fa-exclamation-triangle fa-3x mb-3" style="color: var(--danger-color);"></i>
                <p class="h4">Error al cargar las películas.</p>
                <p class="text-muted">Por favor, intenta nuevamente más tarde.</p>
            `;
        }
    }

    loadMovies();
});
</script>
@endsection

