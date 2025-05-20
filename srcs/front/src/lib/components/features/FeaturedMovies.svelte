<script lang="ts">
  import type { Movie } from '$lib/types';
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  import MovieCard from '../MovieCard.svelte';
  import { onMount } from 'svelte';
  
  export let movies = [];
  export let title = $t('featuredMoviesTitle');
  export let loading = false;
  export let error = null;
  export let viewAllUrl = "/movies";
  export let showViewAll = true;
  
  // Número de películas a mostrar en diferentes tamaños de pantalla
  export let itemsToShow = {
    xs: 1,  // Extra small devices
    sm: 2,  // Small devices
    md: 3,  // Medium devices
    lg: 4   // Large devices and up
  };

  // Para implementar el carrusel (opcional)
  let currentSlide = 0;
  let totalSlides = 0;
  
  $: {
    // Calcular número total de slides cuando cambian las películas
    totalSlides = Math.ceil(movies.length / itemsToShow.lg);
  }
  
  function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
  }
  
  function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
  }
  
  onMount(() => {
    // Ajustar tamaños según dispositivo si es necesario
  });
</script>

<section class="featured-movies py-5" data-bs-theme={$theme}>
  <div class="container">
    <!-- Encabezado de sección con diseño mejorado -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div class="section-header mb-3 mb-md-0">
            <h2 class="section-title mb-0">
              <i class="bi bi-star-fill me-2 text-warning"></i>
              {title}
            </h2>
            <div class="section-subtitle text-muted">
              {$t('discoverMovies')}
            </div>
          </div>
          
          {#if showViewAll && movies.length > 0}
            <a href={viewAllUrl} class="btn btn-primary">
              {$t('showFilms')}
              <i class="bi bi-arrow-right ms-1"></i>
            </a>
          {/if}
        </div>
      </div>
    </div>
    
    <!-- Estados del componente -->
    {#if loading}
      <div class="card bg-transparent border-0 text-center py-5">
        <div class="card-body">
          <div class="spinner-grow text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">{$t('loading')}</span>
          </div>
          <p class="lead text-muted">{$t('loading')}</p>
        </div>
      </div>
    
    {:else if error}
      <div class="alert alert-danger shadow-sm">
        <div class="d-flex align-items-center">
          <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
          <div>
            <h5 class="alert-heading mb-1">{$t('errorOccurred')}</h5>
            <p class="mb-0">{error}</p>
          </div>
        </div>
      </div>
    
    {:else if movies.length > 0}
      <!-- Contenedor con padding para los botones de navegación -->
      <div class="position-relative px-4">
        <!-- Botones de navegación (solo visibles en pantallas más grandes o con hover) -->
        {#if movies.length > itemsToShow.md}
          <button 
            class="btn btn-light rounded-circle shadow-sm position-absolute start-0 top-50 translate-middle-y nav-btn prev-btn"
            on:click={prevSlide}
            aria-label={$t('previous')}
          >
            <i class="bi bi-chevron-left"></i>
          </button>
          <button 
            class="btn btn-light rounded-circle shadow-sm position-absolute end-0 top-50 translate-middle-y nav-btn next-btn"
            on:click={nextSlide}
            aria-label={$t('next')}
          >
            <i class="bi bi-chevron-right"></i>
          </button>
        {/if}
      
        <!-- Grid de películas -->
        <div class="row g-4 movies-grid">
          {#each movies as movie, i}
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 movie-item">
              <div class="card-wrapper h-100">
                <MovieCard {movie} />
              </div>
            </div>
          {/each}
        </div>
      </div>
      
      <!-- Indicadores (solo si hay múltiples slides) -->
      {#if totalSlides > 1}
        <div class="indicators d-flex justify-content-center mt-4">
          {#each Array(totalSlides) as _, i}
            <button 
              class="indicator-dot btn btn-sm rounded-circle mx-1 {i === currentSlide ? 'active' : ''}"
              on:click={() => currentSlide = i}
              aria-label={`Slide ${i+1}`}
            ></button>
          {/each}
        </div>
      {/if}
    
    {:else}
      <!-- Estado vacío mejorado -->
      <div class="card bg-transparent border-0 empty-state py-5">
        <div class="card-body text-center">
          <div class="empty-icon mb-4">
            <i class="bi bi-film fs-1 text-muted"></i>
          </div>
          <h3 class="h5 mb-3">{$t('noFeaturedMovies')}</h3>
          <p class="text-muted mb-4">{$t('checkLater')}</p>
          <a href={viewAllUrl} class="btn btn-primary">
            {$t('browseAllMovies')}
          </a>
        </div>
      </div>
    {/if}
  </div>
</section>

<style>
  /* Encabezado con línea decorativa */
  .section-title {
    position: relative;
    font-weight: 600;
    color: var(--bs-body-color);
  }
  
  .section-subtitle {
    font-size: 0.9rem;
    margin-top: 0.25rem;
  }
  
  /* Estilizado de las tarjetas */
  .movie-item {
    transition: transform 0.3s ease;
  }
  
  .movie-item:hover {
    transform: translateY(-5px);
  }
  
  .card-wrapper {
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    transition: box-shadow 0.3s ease;
  }
  
  .card-wrapper:hover {
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  }
  
  /* Botones de navegación */
  .nav-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    opacity: 0.85;
    transition: all 0.3s ease;
    padding: 0;
  }
  
  .nav-btn:hover {
    opacity: 1;
    transform: translateY(-50%) scale(1.1);
  }
  
  /* Indicadores */
  .indicator-dot {
    width: 12px;
    height: 12px;
    background-color: var(--bs-gray-300);
    transition: all 0.3s ease;
  }
  
  .indicator-dot.active {
    background-color: var(--bs-primary);
    width: 16px;
    height: 16px;
  }
  
  /* Estado vacío */
  .empty-state {
    border-radius: 1rem;
    background-color: rgba(var(--bs-primary-rgb), 0.05);
  }
  
  .empty-icon {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
  }
  
  /* Adaptaciones para modo oscuro */
  :global([data-bs-theme="dark"]) .card-wrapper {
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  }
  
  :global([data-bs-theme="dark"]) .card-wrapper:hover {
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
  }
  
  :global([data-bs-theme="dark"]) .empty-state {
    background-color: rgba(var(--bs-primary-rgb), 0.15);
  }
  
  :global([data-bs-theme="dark"]) .empty-icon {
    background-color: rgba(var(--bs-primary-rgb), 0.2);
  }
  
  :global([data-bs-theme="dark"]) .indicator-dot {
    background-color: var(--bs-gray-600);
  }
  
  /* Media queries para mejorar responsividad */
  @media (max-width: 767.98px) {
    .nav-btn {
      display: none;
    }
  }
</style>