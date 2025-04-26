<script lang="ts">
  import { onMount } from 'svelte';
  import type { Movie } from '$lib/types';
  
  // Estado para películas
  let movies: Movie[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  onMount(async () => {
    try {
      // Fetch data from the actual API endpoint
      const response = await fetch('http://localhost:8000/api/v1/movies');
      
      if (!response.ok) {
        throw new Error(`API responded with status: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Check if the response has the expected structure
      if (data && Array.isArray(data)) {
        movies = data;
      } else if (data && data.data && Array.isArray(data.data)) {
        // Handle case where data is wrapped in a 'data' property
        movies = data.data;
      } else {
        throw new Error('Unexpected API response format');
      }
      
      loading = false;
    } catch (err) {
      console.error('Error cargando películas:', err);
      error = "No se pudieron cargar las películas: " + (err instanceof Error ? err.message : String(err));
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
</script>

<div class="container mt-4">
  <div class="row">
    <div class="col-12">
      <h1 class="section-title mb-4">Peliculas</h1>
    </div>
  </div>
  
  {#if loading}
    <div class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  {:else if error}
    <div class="col-12 text-center">
      <div class="alert alert-danger">
        {error}
      </div>
    </div>
  {:else if movies.length === 0}
    <div class="col-12 text-center">
      <p>No hay películas disponibles</p>
    </div>
  {:else}
    <div class="row g-4">
      {#each movies as movie}
        <div class="col-md-6 col-lg-4">
          <div class="card movie-card h-100">
            <img src={movie.photo_url || '/img/default-movie.jpg'} 
                 class="card-img-top" alt={movie.title}>
            <div class="card-body">
              <h5 class="card-title">{movie.title}</h5>
              <p class="card-text small">
                Lanzamiento: {formatDate(movie.release_date || '')}
              </p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-primary">
                  <i class="bi bi-star-fill me-1"></i>{movie.rating || '?'}
                </span>
                <a href={`/movies/${movie.id}`} class="btn btn-primary btn-sm">
                  <i class="bi bi-ticket me-1"></i>Reservar
                </a>
              </div>
            </div>
          </div>
        </div>
      {/each}
    </div>
  {/if}
</div>

<style>
  .movie-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .movie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  }
  
  .card-img-top {
    height: 350px;
    object-fit: cover;
  }
</style> 