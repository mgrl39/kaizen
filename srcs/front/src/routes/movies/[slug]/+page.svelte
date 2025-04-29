<script lang="ts">
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { API_URL } from '$lib/config';
  import type { Movie } from '$lib/types';
  
  let movie: Movie | null = null;
  let loading: boolean = true;
  let error: string | null = null;
  
  const slug = $page.params.slug;
  
  onMount(async () => {
    try {
      const response = await fetch(`${API_URL}/movies/${slug}`);
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data && data.success && data.data) {
        movie = data.data;
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
      
      loading = false;
    } catch (err) {
      console.error('Error cargando película:', err);
      error = "No se pudo cargar la información de la película: " + (err instanceof Error ? err.message : String(err));
      loading = false;
    }
  });
  
  function formatDate(dateString: string): string {
    if (!dateString) return 'Fecha no disponible';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  }
  
  function formatDuration(minutes: number): string {
    if (!minutes) return 'Duración no disponible';
    
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    
    return `${hours}h ${mins}m`;
  }
</script>

<div class="container mt-4">
  {#if loading}
    <div class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  {:else if error}
    <div class="alert alert-danger">
      {error}
    </div>
  {:else if movie}
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="movie-poster-container">
          <img src={movie.photo_url || '/img/default-movie.jpg'} 
               class="img-fluid rounded movie-poster" 
               alt={movie.title}>
          <div class="movie-rating">
            <span class="rating-badge">
              <i class="bi bi-star-fill me-1"></i>{movie.rating || 'NR'}
            </span>
          </div>
        </div>
      </div>
      
      <div class="col-md-8">
        <div class="content-wrapper p-4">
          <h1 class="movie-title mb-3">{movie.title}</h1>
          
          <div class="movie-meta mb-4">
            <span class="meta-item">
              <i class="bi bi-calendar3 me-2"></i>Estreno: {formatDate(movie.release_date)}
            </span>
            <span class="meta-item ms-3">
              <i class="bi bi-clock me-2"></i>Duración: {formatDuration(movie.duration)}
            </span>
          </div>
          
          <div class="movie-synopsis mb-4">
            <h3 class="section-subtitle">Sinopsis</h3>
            <p>{movie.synopsis || 'No hay sinopsis disponible para esta película.'}</p>
          </div>
          
          <div class="movie-actions mt-4">
            <button class="btn btn-primary btn-lg">
              <i class="bi bi-ticket-perforated me-2"></i>Reservar Entradas
            </button>
            <a href="/movies" class="btn btn-outline-light ms-3">
              <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
          </div>
        </div>
      </div>
    </div>
  {:else}
    <div class="alert alert-info">
      No se encontró información para esta película
    </div>
  {/if}
</div>

<style>
  .movie-poster-container {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  }

  .movie-poster {
    width: 100%;
    height: auto;
    display: block;
  }

  .movie-rating {
    position: absolute;
    top: 10px;
    right: 10px;
  }

  .rating-badge {
    background: rgba(var(--bs-primary-rgb), 0.9);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: bold;
  }

  .movie-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--bs-primary);
  }

  .meta-item {
    color: var(--bs-gray-600);
    font-size: 1.1rem;
  }

  .section-subtitle {
    color: var(--bs-primary);
    font-weight: 600;
    margin-bottom: 1rem;
  }

  .content-wrapper {
    background: var(--bs-light);
    border-radius: 15px;
    height: 100%;
  }
</style> 