<script>
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  import CategoryCard from './CategoryCard.svelte';
  
  export let categories = [];
  export let title = $t('exploreCategories', 'Explorar categorías');
  export let showViewAll = true;
  export let viewAllUrl = '/categories';
</script>

<section class="py-5 bg-body-tertiary" data-bs-theme={$theme}>
  <div class="container">
    <!-- Encabezado de sección -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">{title}</h2>
      
      {#if showViewAll && categories.length > 0}
        <a href={viewAllUrl} class="btn btn-outline-primary btn-sm">
          {$t('viewAll', 'Ver todas')}
          <i class="bi bi-arrow-right ms-1"></i>
        </a>
      {/if}
    </div>
    
    <!-- Rejilla de categorías -->
    {#if categories.length > 0}
      <div class="row g-4">
        {#each categories as category}
          <div class="col-6 col-md-4 col-lg-3">
            <CategoryCard {category} />
          </div>
        {/each}
      </div>
    {:else}
      <!-- Estado vacío -->
      <div class="text-center py-5">
        <i class="bi bi-grid-3x3-gap display-1 text-muted"></i>
        <p class="mt-3">{$t('noCategories', 'No hay categorías disponibles')}</p>
      </div>
    {/if}
  </div>
</section> 