<script lang="ts">
  import { onMount } from 'svelte';
  import type { Movie } from '$lib/types';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';
  
  // Estado para películas
  let movies: Movie[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  let searchTerm: string = '';
  let searchTimeout: ReturnType<typeof setTimeout>;
  let initialMovies: Movie[] = [];
  
  async function fetchMovies(name?: string) {
    loading = true;
    error = null;
    
    try {
      let endpoint = `${API_URL}/movies`;
      if (name?.trim()) {
        endpoint += `?name=${encodeURIComponent(name.trim())}`;
      }
      
      const response = await fetch(endpoint, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      if (!response.ok) {
        throw new Error(`API responded with status: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data && data.success && Array.isArray(data.data)) {
        movies = data.data;
        if (!name) {
          initialMovies = data.data;
        }
      } else {
        throw new Error('Unexpected API response format');
      }
    } catch (err) {
      console.error('Error cargando películas:', err);
      error = "No se pudieron cargar las películas: " + (err instanceof Error ? err.message : String(err));
    } finally {
      loading = false;
    }
  }
  
  // Función para manejar la búsqueda con debounce
  function handleSearch() {
    // Cancelar el timeout anterior si existe
    if (searchTimeout) clearTimeout(searchTimeout);
    
    // Si el término de búsqueda está vacío, restaurar las películas iniciales
    if (!searchTerm.trim()) {
      movies = initialMovies;
      loading = false;
      return;
    }
    
    // Crear un nuevo timeout
    searchTimeout = setTimeout(() => {
      fetchMovies(searchTerm);
    }, 300); // Esperar 300ms después de que el usuario deje de escribir
  }
  
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

  // Función para resaltar texto de búsqueda
  function highlightText(text: string, search: string): string {
    if (!search.trim()) return text;
    
    const searchWords = search.trim().toLowerCase().split(/\s+/);
    let result = text;
    
    searchWords.forEach(word => {
      if (word) {
        const regex = new RegExp(`(${word})`, 'gi');
        result = result.replace(regex, '<span class="highlight">$1</span>');
      }
    });
    
    return result;
  }
  
  onMount(() => {
    fetchMovies();
  });
</script>

<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-12">
      <h1 class="section-title">Películas</h1>
      <div class="input-group">
        <span class="input-group-text">
          <i class="bi bi-search"></i>
        </span>
        <input
          type="text"
          class="form-control"
          placeholder="Buscar por título o descripción..."
          bind:value={searchTerm}
          on:input={handleSearch}
        >
        {#if searchTerm}
          <button 
            class="btn btn-outline-secondary" 
            on:click={() => {
              searchTerm = '';
              movies = initialMovies;
            }}
          >
            <i class="bi bi-x-lg"></i>
          </button>
        {/if}
      </div>
      {#if searchTerm}
        <div class="text-center text-muted small mt-2">
          <i class="bi bi-info-circle me-1"></i>
          Buscando en títulos y sinopsis. La búsqueda no distingue mayúsculas/minúsculas.
        </div>
      {/if}
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
      <div class="alert alert-info">
        {searchTerm ? 'No se encontraron películas que coincidan con tu búsqueda' : 'No hay películas disponibles'}
      </div>
    </div>
  {:else}
    <div class="row g-4">
      {#each movies as movie}
        <div class="col-md-6 col-lg-4">
          <div class="card movie-card h-100">
            <img src={movie.photo_url || '/img/default-movie.jpg'} 
                 class="card-img-top" alt={movie.title}>
            <div class="card-body">
              <h5 class="card-title">
                {#if searchTerm}
                  {@html highlightText(movie.title, searchTerm)}
                {:else}
                  {movie.title}
                {/if}
              </h5>
              <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-primary">
                  <i class="bi bi-star-fill me-1"></i>{movie.rating || '?'}
                </span>
                <a href={`/movies/${movie.slug}`} class="btn btn-primary btn-sm">
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
  
  .input-group {
    max-width: 600px;
    margin: 1rem auto;
  }
  
  :global(.highlight) {
    background-color: rgba(var(--bs-warning-rgb), 0.3);
    padding: 0 2px;
    border-radius: 2px;
  }
</style>

<div class="decorative-blob blob-1"></div>
<div class="decorative-blob blob-2"></div> 