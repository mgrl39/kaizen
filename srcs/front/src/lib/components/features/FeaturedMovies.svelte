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
  {:else if featuredMovies.length == 0}
    <div class="bg-blue-900/20 border border-blue-800 text-blue-100 px-4 py-3 rounded-md">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>{$t('noFeaturedMovies')}
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