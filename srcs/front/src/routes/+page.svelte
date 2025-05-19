<script>
  import { onMount } from 'svelte';
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
    {
      id: 1,
      name: $t('category_action', 'Acción'), 
      icon: 'lightning', 
      count: 24,
      image: "https://source.unsplash.com/random/500x300/?action,movie"
    },
    {
      id: 2,
      name: $t('category_comedy', 'Comedia'), 
      icon: 'emoji-laughing', 
      count: 18,
      image: "https://source.unsplash.com/random/500x300/?comedy,movie"
    },
    {
      id: 3,
      name: $t('category_drama', 'Drama'), 
      icon: 'mask', 
      count: 32,
      image: "https://source.unsplash.com/random/500x300/?drama,movie"
    }
  ];
  
  // Función para obtener películas aleatorias de un array
  function getRandomMovies(movies, count) {
    const shuffled = [...movies].sort(() => 0.5 - Math.random());
    return shuffled.slice(0, Math.min(count, movies.length));
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
      
      // Seleccionar películas aleatorias
      featuredMovies = getRandomMovies(allMovies, 4);
      
      loading = false;
    } catch (err) {
      console.error('Error cargando películas:', err);
      error = `${$t('featuredMoviesError', 'Error al cargar películas destacadas:')} ` + 
              (err instanceof Error ? err.message : String(err));
      loading = false;
      
      // Películas de respaldo en caso de error
      featuredMovies = [
        {
          id: 1,
          title: "Película de ejemplo 1",
          poster: "https://source.unsplash.com/random/300x450/?movie,poster,1",
          rating: 4.5,
          year: 2023,
          genres: ["Acción", "Aventura"],
          duration: 120
        },
        {
          id: 2,
          title: "Película de ejemplo 2",
          poster: "https://source.unsplash.com/random/300x450/?movie,poster,2",
          rating: 4.2,
          year: 2023,
          genres: ["Comedia", "Romance"],
          duration: 105
        }
      ];
    }
  });
</script>

<!-- Hero Banner con imagen específica para la página principal -->
<HeroBanner 
  title="Kaizen Cinema"
  subtitle="Tu destino para las mejores experiencias cinematográficas"
  imageUrl="https://source.unsplash.com/random/1920x1080/?cinema,movies,theater"
  buttonText="Ver películas"
  buttonUrl="/movies"
/>

<!-- Contenido específico de la página -->
<div class="container py-5">
  <FeaturedMovies movies={featuredMovies} loading={loading} />
  <CategoriesSection categories={categories} />
</div> 