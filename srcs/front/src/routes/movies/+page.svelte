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
  
  // Imagen por defecto en base64 (un placeholder gris simple)
  const DEFAULT_IMAGE_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
  
  // Función para obtener la URL correcta de la imagen
  function getImageUrl(photoUrl) {
    if (!photoUrl) return DEFAULT_IMAGE_BASE64;
    
    // Si ya es una URL completa
    if (photoUrl.startsWith('http')) {
      return photoUrl;
    }
    
    // Si es solo el nombre del archivo
    if (!photoUrl.includes('/')) {
      return `${API_URL}/images/${photoUrl}`;
    }
    
    // Si ya tiene una ruta parcial
    return `${API_URL}/images/${photoUrl}`;
  }
  
  // Función para manejar errores de carga de imagen
  function handleImageError(event) {
    if (event.target) {
      event.target.src = DEFAULT_IMAGE_BASE64;
      event.target.onerror = null; // Prevenir bucle infinito
    }
  }
  
  onMount(async () => {
    try {
      // Puedes especificar la página y elementos por página
      const response = await fetch(`${API_URL}/movies?page=1&per_page=12`);
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Ahora los datos están en data.data
      if (data && data.success && Array.isArray(data.data)) {
        movies = data.data;
        
        // Guardar información de paginación
        pagination = data.pagination;
        
        // Mostrar información para depuración
        console.log('Películas cargadas:', movies);
        console.log('Primera película:', movies[0]);
        if (movies[0]) {
          console.log('URL de imagen generada:', getImageUrl(movies[0].photo_url));
        }
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
      const searchLower = searchQuery.toLowerCase();
      
      // Filtrar por búsqueda (título o sinopsis)
      if (searchQuery && !movie.title.toLowerCase().includes(searchLower) && 
          !movie.synopsis?.toLowerCase().includes(searchLower)) {
        return false;
      }
      
      // Filtrar por género
      if (selectedGenre && !movie.genre?.includes(selectedGenre)) {
        return false;
      }
      
      return true;
    })
    .sort((a, b) => {
      // Ordenar según criterio
      if (sortBy === 'rating') {
        return (b.rating || 0) - (a.rating || 0);
      } else if (sortBy === 'title') {
        return a.title.localeCompare(b.title);
      } else if (sortBy === 'release_date') {
        return new Date(b.release_date || 0).getTime() - new Date(a.release_date || 0).getTime();
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

  // Añadir variables para la paginación
  let pagination = {
    current_page: 1,
    total: 0,
    per_page: 12,
    last_page: 0
  };

  // Función para cambiar de página
  async function goToPage(page) {
    if (page < 1 || page > pagination.last_page) return;
    
    loading = true;
    try {
      const response = await fetch(`${API_URL}/movies?page=${page}&per_page=${pagination.per_page}`);
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data && data.success && Array.isArray(data.data)) {
        movies = data.data;
        pagination = data.pagination;
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
    } catch (err) {
      console.error('Error cambiando de página:', err);
      error = `Error: ` + (err instanceof Error ? err.message : String(err));
    } finally {
      loading = false;
    }
  }
</script>

<!-- Hero Banner con imagen de fondo para la cartelera -->
<HeroBanner 
  title="Cartelera"
  subtitle="Descubre todas las películas disponibles en nuestro cine"
  imageUrl="https://source.unsplash.com/random/1920x1080/?movies,cinema,popcorn"
  on:error={(e) => {
    if (e.target) {
      e.target.src = DEFAULT_IMAGE_BASE64;
      e.target.onerror = null;
    }
  }}
/>

<!-- Contenido principal con estilo Bootstrap -->
<div class="container py-5">
  <!-- Sección de filtros -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-gradient">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white">
              <i class="bi bi-funnel me-2"></i>
              Filtros
            </h5>
            <button class="btn btn-sm btn-light" on:click={resetFilters}>
              <i class="bi bi-arrow-counterclockwise me-1"></i>
              Resetear
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="searchQuery" class="form-label">Buscar por título o sinopsis</label>
              <div class="input-group">
                <span class="input-group-text border-primary bg-white">
                  <i class="bi bi-search text-primary"></i>
                </span>
                <input 
                  type="text" 
                  id="searchQuery" 
                  bind:value={searchQuery} 
                  placeholder="Escribe para buscar..." 
                  class="form-control border-primary"
                />
                {#if searchQuery}
                  <button 
                    class="btn btn-outline-primary" 
                    type="button"
                    on:click={() => searchQuery = ''}
                  >
                    <i class="bi bi-x"></i>
                  </button>
                {/if}
              </div>
            </div>
            
            <div class="col-md-3">
              <label for="genreSelect" class="form-label">Género</label>
              <select 
                id="genreSelect" 
                bind:value={selectedGenre} 
                class="form-select border-primary"
              >
                <option value="">Todos los géneros</option>
                {#each genres as genre}
                  <option value={genre}>{genre}</option>
                {/each}
              </select>
            </div>
            
            <div class="col-md-3">
              <label for="sortSelect" class="form-label">Ordenar por</label>
              <select 
                id="sortSelect" 
                bind:value={sortBy} 
                class="form-select border-primary"
              >
                <option value="rating">Mejor valoradas</option>
                <option value="title">Título</option>
                <option value="release_date">Más recientes</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Resultados -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 text-gradient">
      <i class="bi bi-film me-2"></i>
      {searchQuery ? 'Resultados' : 'Todas las películas'}
      {#if filteredMovies.length > 0}
        <span class="badge bg-gradient ms-2">{filteredMovies.length}</span>
      {/if}
    </h2>
    {#if searchQuery && filteredMovies.length === 0}
      <button class="btn btn-link text-primary text-decoration-none" on:click={() => searchQuery = ''}>
        <i class="bi bi-arrow-left me-1"></i>
        Ver todas
      </button>
    {/if}
  </div>
  
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
  {:else if filteredMovies.length === 0}
    <div class="alert alert-info" role="alert">
      <i class="bi bi-info-circle-fill me-2"></i>
      No se encontraron películas con los filtros actuales.
    </div>
  {:else}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      {#each filteredMovies as movie}
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
                  <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                    <i class="bi bi-star-fill me-1"></i>{movie.rating}
                  </span>
                {/if}
              </div>
              <div class="card-body">
                <h5 class="card-title text-truncate">{movie.title}</h5>
                <div class="d-flex align-items-center mb-2 text-muted">
                  <small>
                    {movie.release_year || (movie.release_date ? new Date(movie.release_date).getFullYear() : 'N/A')}
                    {#if movie.duration}
                      <span class="mx-1">•</span>
                      <i class="bi bi-clock me-1"></i>{movie.duration} min
                    {/if}
                  </small>
                </div>
                <div class="d-flex flex-wrap gap-1">
                  {#if movie.genres && movie.genres.length > 0}
                    {#each movie.genres.slice(0, 3) as genre, i}
                      <span class="badge bg-secondary">{genre}</span>
                    {/each}
                    {#if movie.genres.length > 3}
                      <span class="badge bg-secondary">+{movie.genres.length - 3}</span>
                    {/if}
                  {:else if movie.categories && movie.categories.length > 0}
                    {#each movie.categories.slice(0, 3) as category, i}
                      <span class="badge bg-secondary">{category}</span>
                    {/each}
                    {#if movie.categories.length > 3}
                      <span class="badge bg-secondary">+{movie.categories.length - 3}</span>
                    {/if}
                  {/if}
                </div>
              </div>
              <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                  <small class="text-muted">
                    {#if movie.release_date}
                      <i class="bi bi-calendar3 me-1"></i>
                      {new Date(movie.release_date).toLocaleDateString()}
                    {/if}
                  </small>
                  <button class="btn btn-sm btn-outline-primary">
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
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
  }
  
  /* Estilos para el tema morado */
  .bg-gradient {
    background: linear-gradient(to right, var(--bs-primary), var(--bs-indigo));
  }

  .text-gradient {
    background: linear-gradient(to right, var(--bs-primary), var(--bs-indigo));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .badge.bg-gradient {
    background: linear-gradient(to right, var(--bs-primary), var(--bs-indigo));
    color: white;
  }

  /* Asegúrate de que los enlaces no tengan el subrayado predeterminado */
  a {
    text-decoration: none;
  }
  
  /* Cuando está en modo oscuro, ajustar colores para mantener contraste */
  :global([data-bs-theme="dark"]) .movie-card {
    background-color: var(--bs-dark);
    border-color: var(--bs-dark-border-subtle);
  }
  
  :global([data-bs-theme="dark"]) .movie-card a {
    color: var(--bs-body-color);
  }

  :global([data-bs-theme="dark"]) .card {
    background-color: var(--bs-dark);
  }

  :global([data-bs-theme="dark"]) .input-group-text {
    background-color: var(--bs-dark);
    border-color: var(--bs-primary);
  }

  :global([data-bs-theme="dark"]) .form-control,
  :global([data-bs-theme="dark"]) .form-select {
    background-color: var(--bs-dark);
    border-color: var(--bs-primary);
    color: var(--bs-body-color);
  }

  :global([data-bs-theme="dark"]) .form-control::placeholder {
    color: var(--bs-secondary);
  }
</style>
