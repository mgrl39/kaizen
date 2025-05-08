<script lang="ts">
  import { t } from '$lib/i18n';
  
  // Opcionales: props para sobreescribir las traducciones si es necesario
  export let title = undefined;
  export let subtitle = undefined;
</script>

<div class="w-full mx-auto mb-8 overflow-hidden rounded-2xl relative bg-no-repeat bg-center bg-cover py-12 sm:py-16 md:py-24" 
  style="background-image: linear-gradient(rgba(0,0,0,0.75), rgba(0,0,0,0.75)), url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');"
>
  <div class="container mx-auto px-4 md:px-8 relative z-10">
    <div class="max-w-3xl mx-auto md:mx-0">
      <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 text-shadow-lg">
        {title || $t('heroBannerTitle')}
      </h1>
      <p class="text-lg sm:text-xl mb-6 text-gray-200 max-w-2xl">
        {subtitle || $t('heroBannerSubtitle')}
      </p>
      <div class="flex flex-wrap gap-3 sm:gap-4">
        <a href="/movies" class="px-4 py-2.5 bg-primary hover:bg-primary-hover text-white rounded-md shadow-lg hover:shadow-xl transition-all flex items-center justify-center sm:justify-start">
          <i class="bi bi-film mr-2"></i>
          <span>{$t('viewMovies')}</span>
        </a>
        <a href="/cinemas" class="px-4 py-2.5 border border-white text-white hover:bg-white/10 rounded-md transition-all flex items-center justify-center sm:justify-start">
          <i class="bi bi-building mr-2"></i>
          <span>{$t('exploreCinemas')}</span>
        </a>
      </div>
    </div>
  </div>
  
  <!-- Overlay de gradiente para mejorar contraste -->
  <div class="absolute inset-0 bg-gradient-to-r from-purple-900/10 to-transparent pointer-events-none"></div>
</div>

<style>
  .text-shadow-lg {
    text-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
  }
</style>