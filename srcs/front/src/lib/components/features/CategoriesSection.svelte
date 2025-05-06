<script lang="ts">
  import type { Category } from '$lib/types';
  import CategoryCard from './CategoryCard.svelte';
  
  export let categories: Category[] = [];
</script>

<section class="mb-12">
  <h2 class="section-title text-2xl font-semibold mb-6 relative inline-block">
    <i class="bi bi-grid mr-2"></i>Categorías
  </h2>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    {#each categories as category}
      <div>
        <CategoryCard {category} />
      </div>
    {/each}
  </div>
</section> 