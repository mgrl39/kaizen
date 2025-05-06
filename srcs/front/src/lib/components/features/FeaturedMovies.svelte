<script lang="ts">
  import type { Movie } from '$lib/types';
  import { t } from '$lib/i18n';
  import MovieCard from '../MovieCard.svelte';
  
  export let loading: boolean;
  export let error: string | null;
  export let featuredMovies: Movie[] = [];
</script>

<section class="mb-12">
  <h2 class="section-title text-2xl font-semibold mb-6 relative inline-block">
    <i class="bi bi-stars mr-2"></i>{$t('featuredMoviesTitle')}
  </h2>
  
  {#if loading}
    <div class="flex justify-center items-center my-10">
      <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      <span class="sr-only">{$t('loading')}</span>
    </div>
  {:else if error}
    <div class="bg-blue-900/20 border border-blue-800 text-blue-100 px-4 py-3 rounded-md">
      <i class="bi bi-info-circle mr-2"></i>{error}
    </div>
  {:else if featuredMovies.length == 0}
    <div class="bg-blue-900/20 border border-blue-800 text-blue-100 px-4 py-3 rounded-md">
      <i class="bi bi-info-circle mr-2"></i>{$t('noFeaturedMovies')}
    </div>
  {:else}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {#each featuredMovies as movie}
        <div>
          <MovieCard {movie} />
        </div>
      {/each}
    </div>
  {/if}
</section>