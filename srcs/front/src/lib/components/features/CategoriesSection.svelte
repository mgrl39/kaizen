<script>
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  import CategoryCard from './CategoryCard.svelte';
  
  export let categories = [];
</script>

<section class="py-5 bg-body-tertiary" data-bs-theme={$theme}>
  <div class="container">
    <h2 class="mb-4 text-center">{$t('exploreCategories')}</h2>
    
    <div class="row g-4">
      {#each categories as category}
        <div class="col-6 col-md-4 col-lg-3">
          <CategoryCard {category} />
        </div>
      {/each}
    </div>
  </div>
</section> 