<script lang="ts">
  import { onMount } from 'svelte';
  import type { Movie } from '$lib/types';
  import HeroBanner from '$lib/components/HeroBanner.svelte';
  import { API_URL } from '$lib/config';
  import { t } from '$lib/i18n';

  // Estado para las películas
  let movies: Movie[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  // Imagen por defecto en base64 (un placeholder gris simple)
  const DEFAULT_IMAGE_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
  
  // Función para obtener la URL correcta de la imagen
  function getImageUrl(photoUrl) {
    if (!photoUrl) return DEFAULT_IMAGE_BASE64;
    return `/images/movies/${photoUrl}`;
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
    per_page: 24,
    last_page: 0
  };

  // Función para formatear la duración
  function formatDuration(minutes: number): string {
    if (!minutes) return '';
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours === 0) return `${mins}min`;
    if (mins === 0) return `${hours}h`;
    return `${hours}h ${mins}min`;
  }

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
  title={$t('bannerMoviesTitle')}
  subtitle={$t('bannerMoviesSubtitle', { total: pagination.total })}
  imageUrl="/images/banners/h1lzyx2t.png"
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
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-4 justify-content-center">
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
              </div>
              <div class="card-body">
                <h5 class="card-title text-truncate text-primary">{movie.title}</h5>
                <div class="d-flex align-items-center text-muted">
                  <small>
                    {movie.release_year || (movie.release_date ? new Date(movie.release_date).getFullYear() : 'N/A')}
                    {#if movie.duration}
                      <span class="mx-1">•</span>
                      <i class="bi bi-clock me-1"></i>{formatDuration(movie.duration)}
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
    width: 160px;
    height: 240px;
    object-fit: cover;
    margin: 0 auto;
    display: block;
    border-radius: 4px;
  }
  
  .movie-card {
    width: fit-content;
    margin: 0 auto;
    background: transparent;
    border: none;
  }
  
  .hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(147, 51, 234, 0.2);
  }

  /* Ajustar el ancho de las columnas para las tarjetas */
  :global(.row) {
    margin: 0 -0.5rem;
  }

  :global(.col) {
    padding: 0.5rem;
  }

  :global(.card-body) {
    width: 160px;
    padding: 0.75rem 0.5rem;
  }

  :global(.card-title) {
    font-size: 1rem;
    margin-bottom: 0.25rem;
    font-weight: 500;
  }

  :global(.text-muted small) {
    font-size: 0.85rem;
  }

  /* Ajustar el contenedor para centrar el último row */
  :global(.container) {
    max-width: 1400px;
    padding: 0 1rem;
  }

  @media (min-width: 992px) {
    :global(.container) {
      padding: 0 2rem;
    }
  }

  /* Estilos personalizados para elementos de UI */
  :global(.badge.bg-primary),
  :global(.badge.bg-secondary),
  :global(.badge.bg-warning) {
    display: none;
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
