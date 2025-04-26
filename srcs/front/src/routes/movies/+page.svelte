<script lang="ts">
  import { onMount } from 'svelte';
  import type { Movie } from '$lib/types';
  
  // Estado para películas
  let movies: Movie[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  onMount(async () => {
    try {
      // Simulamos una llamada a la API
      await new Promise(resolve => setTimeout(resolve, 500));
      
      // Datos de ejemplo
      movies = [
        { 
          id: 1, 
          title: 'Inception', 
          photo_url: 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_.jpg',
          release_date: '2010-07-16',
          rating: 8.8
        },
        { 
          id: 2, 
          title: 'The Dark Knight', 
          photo_url: 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_.jpg',
          release_date: '2008-07-18',
          rating: 9.0
        },
        { 
          id: 3, 
          title: 'Interstellar', 
          photo_url: 'https://m.media-amazon.com/images/M/MV5BZjdkOTU3MDktN2IxOS00OGEyLWFmMjktY2FiMmZkNWIyODZiXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg',
          release_date: '2014-11-07',
          rating: 8.6
        },
        { 
          id: 4, 
          title: 'Pulp Fiction', 
          photo_url: 'https://m.media-amazon.com/images/M/MV5BNGNhMDIzZTUtNTBlZi00MTRlLWFjM2ItYzViMjE3YzI5MjljXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg',
          release_date: '1994-10-14',
          rating: 8.9
        },
        { 
          id: 5, 
          title: 'The Godfather', 
          photo_url: 'https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg',
          release_date: '1972-03-24',
          rating: 9.2
        },
        { 
          id: 6, 
          title: 'The Matrix', 
          photo_url: 'https://m.media-amazon.com/images/M/MV5BNzQzOTk3OTAtNDQ0Zi00ZTVkLWI0MTEtMDllZjNkYzNjNTc4L2ltYWdlXkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_.jpg',
          release_date: '1999-03-31',
          rating: 8.7
        }
      ];
      
      loading = false;
    } catch (err) {
      console.error('Error cargando películas:', err);
      error = "No se pudieron cargar las películas.";
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
  <h1 class="mb-4">Películas</h1>
  
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