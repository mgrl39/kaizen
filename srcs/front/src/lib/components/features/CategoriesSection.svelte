<script lang="ts">
  import type { Category } from '$lib/types';
  import CategoryCard from './CategoryCard.svelte';
  
  export let categories: Category[] = [];
</script>

<section class="categories">
  <h2 class="section-title mb-4">
    <i class="bi bi-grid me-2"></i>Categorías
  </h2>

  <div class="row g-4">
    {#each categories as category}
      <div class="col-md-4">
        <CategoryCard {category} />
      </div>
    {/each}
  </div>
</section> 