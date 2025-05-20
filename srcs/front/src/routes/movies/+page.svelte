<script lang="ts">
  import { onMount } from 'svelte';
  import type { Movie } from '$lib/types';
  import { t } from '$lib/i18n';
  import HeroBanner from '$lib/components/HeroBanner.svelte';
  import { API_URL } from '$lib/config';

  // Géneros para filtrar
  const genres = [
    'Acción', 'Aventura', 'Comedia', 'Drama', 'Ciencia Ficción', 
    'Terror', 'Romance', 'Animación', 'Fantasía', 'Documental'
  ];

  // Estado para las películas
  let movies: Movie[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  // Datos para el hero banner
  const heroData = {
    title: "Cartelera",
    subtitle: "Descubre todas las películas disponibles en nuestro cine",
    imageUrl: "https://source.unsplash.com/1200x600/?movies,cinema",
    overlayOpacity: "50"
  };
  
  onMount(async () => {
    try {
      // Obtener películas desde la API
      const response = await fetch(`${API_URL}/movies`);
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Procesar la respuesta según su estructura
      if (Array.isArray(data)) {
        movies = data;
      } else if (data && data.data && Array.isArray(data.data)) {
        // Si la respuesta está dentro de un objeto con propiedad 'data'
        movies = data.data;
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
      
      loading = false;
    } catch (err) {
      console.error('Error cargando películas:', err);
      error = `Error cargando películas: ` + (err instanceof Error ? err.message : String(err));
      loading = false;
    }
  });

  // Estado para filtros
  let searchQuery = '';
  let selectedGenre = '';
  let sortBy = 'rating'; // 'rating', 'title', 'release_date'
  
  // Películas filtradas
  $: filteredMovies = movies
    .filter(movie => {
      // Filtrar por búsqueda
      if (searchQuery && !movie.title.toLowerCase().includes(searchQuery.toLowerCase())) {
        return false;
      }
      
      // Filtrar por género
      if (selectedGenre && !movie.genres?.includes(selectedGenre)) {
        return false;
      }
      
      return true;
    })
    .sort((a, b) => {
      // Ordenar según criterio
      if (sortBy === 'rating') {
        return b.rating - a.rating;
      } else if (sortBy === 'title') {
        return a.title.localeCompare(b.title);
      } else if (sortBy === 'release_date') {
        return new Date(b.release_date).getTime() - new Date(a.release_date).getTime();
      }
      return 0;
    });
  
  // Películas destacadas
  $: featuredMovies = movies.filter(movie => movie.is_featured);
  
  // Formatear fecha
  function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
  }
  
  // Resetear filtros
  function resetFilters() {
    searchQuery = '';
    selectedGenre = '';
    sortBy = 'rating';
  }
</script>

<!-- Hero Banner con imagen de fondo para la cartelera -->
<HeroBanner 
  title="Cartelera"
  subtitle="Descubre todas las películas disponibles en nuestro cine"
  imageUrl="https://source.unsplash.com/random/1920x1080/?movies,cinema,popcorn"
  overlayOpacity="60"
/>

<!-- Contenido principal con estilo Bootstrap -->
<div class="container py-5">
  <!-- Sección de filtros -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card bg-dark text-white border-secondary">
        <div class="card-header bg-dark d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Filtros</h5>
          <button class="btn btn-sm btn-outline-light" on:click={resetFilters}>
            <i class="bi bi-arrow-counterclockwise me-1"></i>Resetear
          </button>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label for="searchQuery" class="form-label">Buscar</label>
              <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-light">
                  <i class="bi bi-search"></i>
                </span>
                <input 
                  type="text" 
                  id="searchQuery" 
                  bind:value={searchQuery} 
                  placeholder="Buscar películas..." 
                  class="form-control bg-dark border-secondary text-light"
                />
              </div>
            </div>
            
            <div class="col-md-4">
              <label for="genreSelect" class="form-label">Género</label>
              <select 
                id="genreSelect" 
                bind:value={selectedGenre} 
                class="form-select bg-dark border-secondary text-light"
              >
                <option value="">Todos los géneros</option>
                {#each genres as genre}
                  <option value={genre}>{genre}</option>
                {/each}
              </select>
            </div>
            
            <div class="col-md-4">
              <label for="sortSelect" class="form-label">Ordenar por</label>
              <select 
                id="sortSelect" 
                bind:value={sortBy} 
                class="form-select bg-dark border-secondary text-light"
              >
                <option value="rating">Valoración</option>
                <option value="title">Título</option>
                <option value="release_date">Fecha de estreno</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Resultados -->
  <h2 class="mb-4 text-white">
    <i class="bi bi-film me-2"></i>Películas en cartelera
    {#if filteredMovies.length > 0}
      <span class="badge bg-secondary ms-2">{filteredMovies.length}</span>
    {/if}
  </h2>
  
  {#if loading}
    <div class="d-flex justify-content-center py-5">
      <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  {:else if error}
    <div class="alert alert-danger" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      {error}
    </div>
  {:else if filteredMovies.length === 0}
    <div class="alert alert-info" role="alert">
      <i class="bi bi-info-circle-fill me-2"></i>
      No se encontraron películas con los filtros actuales.
    </div>
  {:else}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      {#each filteredMovies as movie}
        <div class="col">
          <div class="card h-100 bg-dark text-white border-secondary hover-card">
            <a href={`/movies/${movie.id}`} class="text-decoration-none text-white">
              <div class="position-relative">
                <img 
                  src={movie.poster_url || 'https://via.placeholder.com/300x450?text=No+Image'} 
                  class="card-img-top movie-poster" 
                  alt={movie.title}
                />
                {#if movie.rating}
                  <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                    <i class="bi bi-star-fill me-1"></i>{movie.rating}
                  </span>
                {/if}
              </div>
              <div class="card-body">
                <h5 class="card-title text-truncate">{movie.title}</h5>
                <div class="d-flex align-items-center mb-2 text-muted">
                  <small>
                    {movie.release_year || 'N/A'}
                    {#if movie.duration}
                      <span class="mx-1">•</span>
                      <i class="bi bi-clock me-1"></i>{movie.duration} min
                    {/if}
                  </small>
                </div>
                <div class="d-flex flex-wrap gap-1">
                  {#each movie.categories || [] as category, i}
                    {#if i < 3}
                      <span class="badge bg-secondary">{category}</span>
                    {/if}
                  {/each}
                  {#if (movie.categories || []).length > 3}
                    <span class="badge bg-secondary">+{movie.categories.length - 3}</span>
                  {/if}
                </div>
              </div>
              <div class="card-footer bg-dark border-secondary">
                <div class="d-flex justify-content-between align-items-center">
                  <small class="text-muted">
                    {#if movie.release_date}
                      <i class="bi bi-calendar3 me-1"></i>
                      {new Date(movie.release_date).toLocaleDateString()}
                    {/if}
                  </small>
                  <button class="btn btn-sm btn-outline-light">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>
        </div>
      {/each}
    </div>
  {/if}
</div>

<style>
  .movie-poster {
    height: 300px;
    object-fit: cover;
  }
  
  .hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
  }
  
  /* Asegúrate de que los enlaces no tengan el subrayado predeterminado */
  a {
    text-decoration: none;
  }
</style>
