<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';

  let movies = [];
  let loading = true;
  let error = null;
  let searchQuery = '';
  let filters = {
    genre: '',
    year: '',
    rating: ''
  };

  // Para los filtros
  let genres = [];
  let years = [];

  async function fetchMovies() {
    loading = true;
    try {
      // Construir URL con parámetros de búsqueda y filtros
      let url = `${API_URL}/movies`;
      let params = new URLSearchParams();
      
      if (searchQuery) {
        params.append('search', searchQuery);
      }
      
      if (filters.genre) {
        params.append('genre', filters.genre);
      }
      
      if (filters.year) {
        params.append('year', filters.year);
      }
      
      if (filters.rating) {
        params.append('rating', filters.rating);
      }
      
      // Añadir parámetros a la URL si hay alguno
      if (params.toString()) {
        url += `?${params.toString()}`;
      }
      
      const response = await fetch(url, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (response.ok) {
        movies = data.data || data;
      } else {
        error = data.message || 'Error al cargar las películas';
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
    } finally {
      loading = false;
    }
  }

  async function fetchFilters() {
    try {
      // En una implementación real, obtendrías esta información del servidor
      genres = ['Acción', 'Aventura', 'Comedia', 'Drama', 'Ciencia Ficción', 'Terror'];
      years = ['2023', '2022', '2021', '2020', '2019'];
    } catch (e) {
      console.error('Error al cargar filtros:', e);
    }
  }

  function handleSearch() {
    fetchMovies();
  }

  function resetFilters() {
    filters = {
      genre: '',
      year: '',
      rating: ''
    };
    searchQuery = '';
    fetchMovies();
  }

  onMount(() => {
    fetchMovies();
    fetchFilters();
  });
</script>

<div class="container py-4">
  <h1 class="section-title mb-4">Películas</h1>
  
  <!-- Buscador y Filtros -->
  <div class="filters-container mb-4">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="input-group">
          <input 
            type="text" 
            class="form-control" 
            placeholder="Buscar películas..." 
            bind:value={searchQuery}
            on:keyup={(e) => e.key === 'Enter' && handleSearch()}
          >
          <button class="btn btn-primary" on:click={handleSearch}>
            <i class="bi bi-search me-1"></i>Buscar
          </button>
        </div>
      </div>
      
      <div class="col-md-2">
        <select class="form-select" bind:value={filters.genre} on:change={handleSearch}>
          <option value="">Género</option>
          {#each genres as genre}
            <option value={genre}>{genre}</option>
          {/each}
        </select>
      </div>
      
      <div class="col-md-2">
        <select class="form-select" bind:value={filters.year} on:change={handleSearch}>
          <option value="">Año</option>
          {#each years as year}
            <option value={year}>{year}</option>
          {/each}
        </select>
      </div>
      
      <div class="col-md-2">
        <select class="form-select" bind:value={filters.rating} on:change={handleSearch}>
          <option value="">Calificación</option>
          <option value="5">5 estrellas</option>
          <option value="4">4+ estrellas</option>
          <option value="3">3+ estrellas</option>
        </select>
      </div>
    </div>
    
    <div class="mt-2">
      <button class="btn btn-sm btn-outline-secondary" on:click={resetFilters}>
        <i class="bi bi-x-circle me-1"></i>Limpiar filtros
      </button>
    </div>
  </div>
  
  {#if loading}
    <div class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
      <p class="mt-2">Cargando películas...</p>
    </div>
  {:else if error}
    <div class="alert alert-danger">{error}</div>
  {:else if movies.length === 0}
    <div class="alert alert-info">No se encontraron películas con los criterios seleccionados.</div>
  {:else}
    <div class="row g-4">
      {#each movies as movie}
        <div class="col-md-6 col-lg-4">
          <div class="card movie-card h-100">
            <img src={movie.photo_url || '/img/default-movie.jpg'} 
                class="card-img-top" 
                alt={movie.title}
                loading="lazy">
            <div class="card-body">
              <h5 class="card-title">{movie.title}</h5>
              <p class="card-text text-muted">
                {movie.year || 'Sin año'} | {movie.genre || 'Sin género'}
              </p>
              <p class="card-text description">{movie.description || 'Sin descripción disponible'}</p>
            </div>
            <div class="card-footer bg-transparent border-top-0">
              <a href={`/movies/${movie.id}`} class="btn btn-primary btn-sm">
                Ver detalles
              </a>
              {#if movie.rating}
                <span class="float-end badge bg-warning text-dark">
                  <i class="bi bi-star-fill me-1"></i>{movie.rating}
                </span>
              {/if}
            </div>
          </div>
        </div>
      {/each}
    </div>
  {/if}
</div>
