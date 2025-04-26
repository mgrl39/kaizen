<script lang="ts">
  import { onMount } from 'svelte';
  import type { Movie, Category } from '$lib/types';
  
  // Estado para las películas destacadas
  let featuredMovies: Movie[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  // Categorías para la sección de categorías
  const categories: Category[] = [
    {name: 'Acción', icon: 'lightning', color: 'danger', gradient: 'danger'},
    {name: 'Comedia', icon: 'emoji-laughing', color: 'warning', gradient: 'warning'},
    {name: 'Drama', icon: 'mask', color: 'info', gradient: 'info'}
  ];
  
  // Función para obtener películas aleatorias de un array
  function getRandomMovies(movies: Movie[], count: number): Movie[] {
    const shuffled = [...movies].sort(() => 0.5 - Math.random());
    return shuffled.slice(0, count);
  }
  
  onMount(async () => {
    try {
      // Obtener películas desde la API real
      const response = await fetch('http://localhost:8000/api/v1/movies');
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Procesar la respuesta según su estructura
      let allMovies: Movie[] = [];
      
      if (Array.isArray(data)) {
        allMovies = data;
      } else if (data && data.data && Array.isArray(data.data)) {
        // Si la respuesta está dentro de un objeto con propiedad 'data'
        allMovies = data.data;
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
      
      // Seleccionar 3 películas aleatorias
      featuredMovies = getRandomMovies(allMovies, 3);
      
      loading = false;
    } catch (err) {
      console.error('Error cargando películas:', err);
      error = "No se pudieron cargar las películas destacadas: " + (err instanceof Error ? err.message : String(err));
      loading = false;
    }
  });
  
  // Función para limitar texto
  function truncateText(text: string, limit: number = 100): string {
    if (!text) return 'Sin descripción disponible';
    return text.length > limit ? text.substring(0, limit) + '...' : text;
  }
</script>

<div class="home-page">
  <!-- Hero Banner -->
  <div class="hero-banner mb-5">
    <div class="hero-content">
      <div class="row align-items-center">
        <div class="col-md-6 px-0">
          <h1 class="display-4 fw-bold glow-text">Kaizen Cinema</h1>
          <p class="lead my-4">La mejor experiencia cinematográfica con las últimas películas y los cines más confortables.</p>
          <div class="d-flex gap-3">
            <a href="/movies" class="btn btn-primary glow-effect">
              <i class="bi bi-film me-2"></i>Ver películas
            </a>
            <a href="/cinemas" class="btn btn-outline-light hover-glow">
              <i class="bi bi-building me-2"></i>Explorar cines
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Películas Destacadas -->
  <section class="featured-movies mb-5">
    <h2 class="section-title mb-4">
      <i class="bi bi-stars me-2"></i>Películas Destacadas
    </h2>
    
    {#if loading}
      <div class="d-flex justify-content-center my-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>
    {:else if error}
      <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>{error}
      </div>
    {:else if featuredMovies.length === 0}
      <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No hay películas destacadas disponibles en este momento.
      </div>
    {:else}
      <div class="row g-4">
        {#each featuredMovies as movie}
          <div class="col-md-4">
            <div class="movie-card card h-100">
              <div class="card-image-wrapper">
                <img src={movie.photo_url} class="card-img-top" alt={movie.title}>
                <div class="card-overlay">
                  <a href={`/movies/${movie.id}`} class="btn btn-primary btn-sm">
                    <i class="bi bi-ticket me-1"></i>Ver detalles
                  </a>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h5 class="card-title mb-0">{movie.title}</h5>
                  <span class="rating-badge">
                    <i class="bi bi-star-fill me-1"></i>{movie.rating}
                  </span>
                </div>
                <p class="card-text text-muted">
                  {truncateText(movie.synopsis || '', 100)}
                </p>
              </div>
            </div>
          </div>
        {/each}
      </div>
    {/if}
  </section>

  <!-- Categorías -->
  <section class="categories">
    <h2 class="section-title mb-4">
      <i class="bi bi-grid me-2"></i>Categorías
    </h2>

    <div class="row g-4">
      {#each categories as category}
        <div class="col-md-4">
          <div class="category-card card h-100">
            <div class="card-body text-center">
              <div class="category-icon-wrapper mb-3">
                <i class={`bi bi-${category.icon} category-icon`}></i>
              </div>
              <h5 class="category-title">{category.name}</h5>
              <a href={`/genre/${category.name.toLowerCase()}`} 
                 class={`btn btn-sm category-btn gradient-${category.gradient}`}>
                Ver películas
              </a>
            </div>
          </div>
        </div>
      {/each}
    </div>
  </section>
</div>

<style>
  /* Estilos específicos de la página home */
  .hero-banner {
    position: relative;
    padding: 6rem 0;
    background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
    background-size: cover;
    background-position: center;
    border-radius: 16px;
    overflow: hidden;
  }

  .hero-content {
    position: relative;
    z-index: 2;
    padding: 0 2rem;
  }

  .glow-text {
    text-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
  }

  .movie-card {
    transition: transform 0.3s ease;
  }

  .card-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 16px 16px 0 0;
  }

  .card-image-wrapper img {
    height: 300px;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .movie-card:hover .card-overlay {
    opacity: 1;
  }

  .movie-card:hover img {
    transform: scale(1.05);
  }

  .rating-badge {
    background: linear-gradient(135deg, #6d28d9, #8b5cf6);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
  }

  .category-card {
    transition: transform 0.3s ease;
  }

  .category-icon-wrapper {
    width: 60px;
    height: 60px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
  }

  .category-icon {
    font-size: 1.75rem;
  }

  .category-btn {
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    color: white;
    transition: all 0.3s ease;
  }

  .gradient-danger {
    background: linear-gradient(135deg, #ff4e50, #f9d423);
  }

  .gradient-warning {
    background: linear-gradient(135deg, #f6d365, #fda085);
  }

  .gradient-info {
    background: linear-gradient(135deg, #0093E9, #80D0C7);
  }
</style>
