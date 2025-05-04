<script lang="ts">
  import type { Movie } from '$lib/types';
  import MovieCard from '../MovieCard.svelte';
  
  export let loading: boolean;
  export let error: string | null;
  export let featuredMovies: Movie[] = [];
</script>

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
  {:else if featuredMovies.length == 0}
    <div class="alert alert-info">
      <i class="bi bi-info-circle me-2"></i>No hay películas destacadas disponibles en este momento.
    </div>
  {:else}
    <div class="row g-4">
      {#each featuredMovies as movie}
        <div class="col-md-4">
          <MovieCard {movie} />
        </div>
      {/each}
    </div>
  {/if}
</section>