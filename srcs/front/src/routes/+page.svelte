<script lang="ts">
  import { onMount } from 'svelte';
  import type { Movie, Category } from '$lib/types';
  import { t } from '$lib/i18n';
  
  // Importar componentes
  import HeroBanner from '$lib/components/HeroBanner.svelte';
  import FeaturedMovies from '$lib/components/features/FeaturedMovies.svelte';
  import CategoriesSection from '$lib/components/features/CategoriesSection.svelte';
  
  // Estado para las películas destacadas
  let featuredMovies = [];
  let loading = true;
  let error = null;
  
  // Categorías para la sección de categorías
  $: categories = [
    {name: $t('category_action'), icon: 'lightning', color: 'text-danger', gradient: 'bg-danger bg-gradient'},
    {name: $t('category_comedy'), icon: 'emoji-laughing', color: 'text-warning', gradient: 'bg-warning bg-gradient'},
    {name: $t('category_drama'), icon: 'mask', color: 'text-info', gradient: 'bg-info bg-gradient'}
  ];
  
  // Función para obtener películas aleatorias de un array
  function getRandomMovies(movies, count) {
    const shuffled = [...movies].sort(() => 0.5 - Math.random());
    return shuffled.slice(0, count);
  }
  
  onMount(async () => {
    try {
      // Obtener películas desde la API real
      const response = await fetch('http://localhost:8000/api/v1/movies');
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Procesar la respuesta según su estructura
      let allMovies = [];
      
      if (Array.isArray(data)) {
        allMovies = data;
      } else if (data && data.data && Array.isArray(data.data)) {
        // Si la respuesta está dentro de un objeto con propiedad 'data'
        allMovies = data.data;
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
      
      // Seleccionar 3 películas aleatorias
      featuredMovies = getRandomMovies(allMovies, 3);
      
      loading = false;
    } catch (err) {
      console.error('Error cargando películas:', err);
      error = `${$t('featuredMoviesError')} ` + (err instanceof Error ? err.message : String(err));
      loading = false;
    }
  });
</script>

<!-- Hero Banner con imagen específica para la página principal -->
<HeroBanner 
  title="Kaizen Cinema"
  subtitle="Tu destino para las mejores experiencias cinematográficas"
  imageUrl="https://source.unsplash.com/random/1920x1080/?cinema,movies,theater"
  overlayOpacity="60"
/>

<!-- Contenido específico de la página -->
<div class="container py-5">
  <FeaturedMovies {loading} {error} {featuredMovies} />
  <CategoriesSection {categories} />
</div>

<style>
  /* Estilos para los gradientes personalizados */
  :global(.bg-gradient-danger) {
    background: linear-gradient(to right, var(--bs-danger), rgba(var(--bs-danger-rgb), 0.7));
  }
  
  :global(.bg-gradient-warning) {
    background: linear-gradient(to right, var(--bs-warning), rgba(var(--bs-warning-rgb), 0.7));
  }
  
  :global(.bg-gradient-info) {
    background: linear-gradient(to right, var(--bs-info), rgba(var(--bs-info-rgb), 0.7));
  }
</style> 