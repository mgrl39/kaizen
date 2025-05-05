<script lang="ts">
  import { onMount } from 'svelte';
  import type { Movie, Category } from '$lib/types';
  import { t } from '$lib/i18n';
  
  // Importar componentes - Rutas corregidas
  import HeroBanner from '$lib/components/HeroBanner.svelte';
  import FeaturedMovies from '$lib/components/features/FeaturedMovies.svelte';
  import CategoriesSection from '$lib/components/features/CategoriesSection.svelte';
  
  // Estado para las películas destacadas
  let featuredMovies: Movie[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  // Categorías para la sección de categorías
  $: categories = [
    {name: $t('category_action'), icon: 'lightning', color: 'danger', gradient: 'danger'},
    {name: $t('category_comedy'), icon: 'emoji-laughing', color: 'warning', gradient: 'warning'},
    {name: $t('category_drama'), icon: 'mask', color: 'info', gradient: 'info'}
  ];
  
  // Función para obtener películas aleatorias de un array
  function getRandomMovies(movies: Movie[], count: number): Movie[] {
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
      let allMovies: Movie[] = [];
      
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

<div class="home-page">
  <HeroBanner />
  <FeaturedMovies {loading} {error} {featuredMovies} />
  <CategoriesSection {categories} />
</div>

<div class="decorative-blob blob-1"></div>
<div class="decorative-blob blob-2"></div> 