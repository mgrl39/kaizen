<script lang="ts">
  import type { Movie } from '$lib/types';
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  import MovieCard from '../MovieCard.svelte';
  
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
</script>

<section class="featured-movies py-5" data-bs-theme={$theme}>
  <div class="container">
    <!-- Encabezado de sección -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="section-title">
        <i class="bi bi-star-fill me-2 text-warning"></i>
        {title}
      </h2>
      
      {#if showViewAll && movies.length > 0}
        <a href={viewAllUrl} class="btn btn-outline-primary">
          {$t('showFilms')}
          <i class="bi bi-arrow-right ms-1"></i>
        </a>
      {/if}
    </div>
    
    <!-- Estado de carga -->
    {#if loading}
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">{$t('loading')}</span>
        </div>
        <p class="mt-3">{$t('loading')}</p>
      </div>
    
    <!-- Estado de error -->
    {:else if error}
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {error}
      </div>
    
    <!-- Películas -->
    {:else if movies.length > 0}
      <div class="row g-4">
        {#each movies as movie}
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <MovieCard {movie} />
          </div>
        {/each}
      </div>
    
    <!-- Estado vacío -->
    {:else}
      <div class="text-center py-5">
        <div class="empty-state">
          <i class="bi bi-film display-1 text-muted mb-3"></i>
          <p class="lead">{$t('noFeaturedMovies')}</p>
          <a href="/movies" class="btn btn-primary mt-3">
            {$t('showFilms')}
          </a>
        </div>
      </div>
    {/if}
  </div>
</section>

<style>
  .featured-movies .section-title {
    position: relative;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
  }
  
  .featured-movies .section-title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 60px;
    height: 3px;
    background: var(--bs-primary);
    border-radius: 3px;
  }
  
  .empty-state {
    padding: 2rem;
    border-radius: 0.5rem;
    background-color: rgba(var(--bs-primary-rgb), 0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
</style>

<section class="mb-12">
  <h2 class="section-title text-2xl font-semibold mb-6 relative inline-block">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
    </svg>{$t('featuredMoviesTitle')}
  </h2>
  
  {#if loading}
    <div class="flex justify-center items-center my-10">
      <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      <span class="sr-only">{$t('loading')}</span>
    </div>
  {:else if error}
    <div class="bg-blue-900/20 border border-blue-800 text-blue-100 px-4 py-3 rounded-md">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>{error}
    </div>
  {:else if movies.length == 0}
    <div class="bg-blue-900/20 border border-blue-800 text-blue-100 px-4 py-3 rounded-md">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>{$t('noFeaturedMovies')}
    </div>
  {:else}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {#each movies as movie}
        <div>
          <MovieCard {movie} />
        </div>
      {/each}
    </div>
  {/if}
</section>