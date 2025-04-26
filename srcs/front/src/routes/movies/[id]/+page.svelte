<script lang="ts">
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import type { Movie, ApiResponse } from '$lib/types';
  
  // Estado para película
  let movie: Movie | null = null;
  let loading: boolean = true;
  let error: string | null = null;
  
  // Obtener ID de la película de la URL
  const movieId = $page.params.id;
  
  onMount(async () => {
    try {
      // Fetch data from the API endpoint with the specific movie ID
      const response = await fetch(`http://localhost:8000/api/v1/movies/${movieId}`);
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data: ApiResponse<Movie> = await response.json();
      
      // Check if the response has the expected structure
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
  
  // Función para formatear fecha
  function formatDate(dateString: string): string {
    if (!dateString) return 'Fecha no disponible';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  }
  
  // Función para convertir minutos a formato horas y minutos
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
              <i class="bi bi-calendar3 me-2"></i>Estreno: {formatDate(movie.release_date || '')}
            </span>
            <span class="meta-item ms-3">
              <i class="bi bi-clock me-2"></i>Duración: {formatDuration(movie.duration || 0)}
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
    
    <!-- Sección de información adicional -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="content-wrapper p-4">
          <h3 class="section-subtitle mb-4">Detalles</h3>
          
          <div class="row">
            <div class="col-md-6">
              <div class="detail-item">
                <span class="detail-label">Clasificación:</span>
                <span class="detail-value">{movie.rating || 'No disponible'}</span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Género:</span>
                <span class="detail-value">{movie.genre || 'No disponible'}</span>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="detail-item">
                <span class="detail-label">Director:</span>
                <span class="detail-value">{movie.director || 'No disponible'}</span>
              </div>
            </div>
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
    overflow: hidden;
    border-radius: 0.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  }
  
  .movie-poster {
    width: 100%;
    transition: transform 0.3s ease;
  }
  
  .movie-poster-container:hover .movie-poster {
    transform: scale(1.03);
  }
  
  .movie-rating {
    position: absolute;
    top: 10px;
    right: 10px;
  }
  
  .movie-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  
  .meta-item {
    display: inline-flex;
    align-items: center;
    color: var(--text-muted);
    font-size: 1rem;
  }
  
  .section-subtitle {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--text-light);
    position: relative;
    display: inline-block;
  }
  
  .section-subtitle::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 40%;
    height: 3px;
    background: linear-gradient(to right, var(--primary-color), transparent);
    border-radius: 2px;
  }
  
  .detail-item {
    margin-bottom: 1rem;
  }
  
  .detail-label {
    font-weight: 600;
    color: var(--text-light);
    margin-right: 0.5rem;
  }
  
  .detail-value {
    color: var(--text-muted);
  }
  
  .movie-synopsis p {
    color: var(--text-muted);
    line-height: 1.7;
    font-size: 1.05rem;
  }
</style> 