<script lang="ts">
  import { onMount } from 'svelte';
  import type { Movie } from '$lib/types';
  import HeroBanner from '$lib/components/HeroBanner.svelte';
  import { API_URL } from '$lib/config';

  // Estado para las películas
  let movies: Movie[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  // Imagen por defecto en base64 (un placeholder gris simple)
  const DEFAULT_IMAGE_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
  
  // Función para obtener la URL correcta de la imagen
  function getImageUrl(photoUrl) {
    if (!photoUrl) return DEFAULT_IMAGE_BASE64;
    if (photoUrl.startsWith('http')) return photoUrl;
    return `${API_URL}/images/${photoUrl}`;
  }
  
  // Función para manejar errores de carga de imagen
  function handleImageError(event) {
    if (event.target) {
      event.target.src = DEFAULT_IMAGE_BASE64;
      event.target.onerror = null;
    }
  }
  
  // Añadir variables para la paginación
  let pagination = {
    current_page: 1,
    total: 0,
    per_page: 12,
    last_page: 0
  };

  onMount(async () => {
    try {
      const response = await fetch(`${API_URL}/movies?page=1&per_page=${pagination.per_page}`);
      if (!response.ok) throw new Error(`API respondió con estado: ${response.status}`);
      
      const data = await response.json();
      if (data && data.success && Array.isArray(data.data)) {
        movies = data.data;
        pagination = data.pagination;
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
      loading = false;
    } catch (err) {
      error = `Error cargando películas: ` + (err instanceof Error ? err.message : String(err));
      loading = false;
    }
  });

  // Función para cambiar de página
  async function goToPage(page) {
    if (page < 1 || page > pagination.last_page) return;
    
    loading = true;
    try {
      const response = await fetch(`${API_URL}/movies?page=${page}&per_page=${pagination.per_page}`);
      if (!response.ok) throw new Error(`API respondió con estado: ${response.status}`);
      
      const data = await response.json();
      if (data && data.success && Array.isArray(data.data)) {
        movies = data.data;
        pagination = data.pagination;
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
    } catch (err) {
      error = `Error: ` + (err instanceof Error ? err.message : String(err));
    } finally {
      loading = false;
    }
  }
</script>

<!-- Hero Banner con imagen de fondo para la cartelera -->
<HeroBanner 
  title="Cartelera"
  subtitle="Disfruta de nuestra colección de {pagination.total} películas"
  imageUrl="https://source.unsplash.com/random/1920x1080/?movies,cinema,popcorn"
  on:error={handleImageError}
/>

<!-- Contenido principal con estilo Bootstrap -->
<div class="container py-5">
  {#if loading}
    <div class="d-flex justify-content-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  {:else if error}
    <div class="alert alert-danger" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      {error}
    </div>
  {:else}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      {#each movies as movie}
        <div class="col">
          <div class="card h-100 movie-card hover-card">
            <a href={`/movies/${movie.slug || movie.id}`} class="text-decoration-none">
              <div class="position-relative">
                <img 
                  src={getImageUrl(movie.photo_url)} 
                  class="card-img-top movie-poster" 
                  alt={movie.title}
                  on:error={handleImageError}
                  loading="lazy"
                />
                {#if movie.rating}
                  <span class="position-absolute top-0 end-0 m-2 badge bg-warning">
                    <i class="bi bi-star-fill me-1"></i>{movie.rating}
                  </span>
                {/if}
              </div>
              <div class="card-body">
                <h5 class="card-title text-truncate text-primary">{movie.title}</h5>
                <div class="d-flex align-items-center mb-2 text-muted">
                  <small>
                    {movie.release_year || (movie.release_date ? new Date(movie.release_date).getFullYear() : 'N/A')}
                    {#if movie.duration}
                      <span class="mx-1">•</span>
                      <i class="bi bi-clock me-1"></i>{movie.duration} min
                    {/if}
                  </small>
                </div>
              </div>
            </a>
          </div>
        </div>
      {/each}
    </div>

    <!-- Controles de paginación -->
    {#if pagination.last_page > 1}
      <nav aria-label="Navegación de páginas" class="mt-4">
        <ul class="pagination justify-content-center">
          <li class="page-item {pagination.current_page === 1 ? 'disabled' : ''}">
            <button 
              class="page-link" 
              on:click={() => goToPage(pagination.current_page - 1)}
              aria-label="Anterior"
            >
              <span aria-hidden="true">&laquo;</span>
            </button>
          </li>
          
          {#each Array(pagination.last_page) as _, i}
            <li class="page-item {pagination.current_page === i + 1 ? 'active' : ''}">
              <button class="page-link" on:click={() => goToPage(i + 1)}>
                {i + 1}
              </button>
            </li>
          {/each}
          
          <li class="page-item {pagination.current_page === pagination.last_page ? 'disabled' : ''}">
            <button 
              class="page-link" 
              on:click={() => goToPage(pagination.current_page + 1)}
              aria-label="Siguiente"
            >
              <span aria-hidden="true">&raquo;</span>
            </button>
          </li>
        </ul>
      </nav>
    {/if}
  {/if}
</div>

<style>
  /* Variables de color personalizadas */
  :root {
    --movie-primary: #9333ea;
    --movie-primary-hover: #7e22ce;
    --movie-primary-light: rgba(147, 51, 234, 0.1);
    --movie-gradient: linear-gradient(135deg, #9333ea, #7e22ce);
  }

  .movie-poster {
    height: 300px;
    object-fit: cover;
  }
  
  .hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(147, 51, 234, 0.2);
  }

  /* Estilos personalizados para elementos de UI */
  :global(.badge.bg-primary) {
    background: var(--movie-gradient) !important;
    border: none;
  }

  :global(.badge.bg-secondary) {
    background-color: var(--movie-primary-light) !important;
    color: var(--movie-primary);
  }

  :global(.badge.bg-warning) {
    background-color: var(--movie-primary-light) !important;
    color: var(--movie-primary);
    border: 1px solid var(--movie-primary);
  }

  :global(.text-primary) {
    color: var(--movie-primary) !important;
  }

  :global(.page-link) {
    color: var(--movie-primary);
  }

  :global(.page-link:hover) {
    color: var(--movie-primary-hover);
    background-color: var(--movie-primary-light);
  }

  :global(.page-item.active .page-link) {
    background: var(--movie-gradient);
    border-color: var(--movie-primary);
  }

  :global(.spinner-border) {
    color: var(--movie-primary);
  }

  /* Estilos para el tema oscuro */
  :global([data-bs-theme="dark"]) {
    --bg-card: rgba(17, 24, 39, 0.8);
    --bg-input: rgba(17, 24, 39, 0.8);
    --text-primary: #fff;
    --text-secondary: rgba(255, 255, 255, 0.7);
    --text-muted: rgba(255, 255, 255, 0.6);
    --border-color: rgba(255, 255, 255, 0.1);
    
    .card,
    .movie-card {
      background-color: var(--bg-card);
      border-color: var(--border-color);
    }

    .text-muted {
      color: var(--text-muted) !important;
    }

    .card-footer {
      border-top-color: var(--border-color);
    }
  }
</style>
