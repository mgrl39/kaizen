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
  
  // Categorías para la sección de categorías (adaptadas a Tailwind)
  $: categories = [
    {name: $t('category_action'), icon: 'lightning', color: 'text-red-500', gradient: 'bg-gradient-to-r from-red-600 to-red-400'},
    {name: $t('category_comedy'), icon: 'emoji-laughing', color: 'text-yellow-500', gradient: 'bg-gradient-to-r from-yellow-600 to-yellow-400'},
    {name: $t('category_drama'), icon: 'mask', color: 'text-blue-500', gradient: 'bg-gradient-to-r from-blue-600 to-blue-400'}
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

<div class="w-full mt-15">
  <HeroBanner />
  <div class="container mx-auto px-4">
    <FeaturedMovies {loading} {error} {featuredMovies} />
    <CategoriesSection {categories} />
  </div>
</div>

<div class="fixed -z-10 top-1/3 left-1/4 w-96 h-96 rounded-full bg-purple-900/20 blur-3xl"></div>
<div class="fixed -z-10 bottom-1/4 right-1/4 w-96 h-96 rounded-full bg-purple-800/10 blur-3xl"></div> 