<script lang="ts">
  import { onMount } from 'svelte';
  import type { Movie } from '$lib/types';
  import { t } from '$lib/i18n';
  import HeroBanner from '$lib/components/HeroBanner.svelte';

  // Géneros para filtrar
  const genres = [
    'Acción', 'Aventura', 'Comedia', 'Drama', 'Ciencia Ficción', 
    'Terror', 'Romance', 'Animación', 'Fantasía', 'Documental'
  ];

  // Estado para las películas
  let movies: Movie[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  // Datos para el hero banner
  const heroData = {
    title: "Cartelera",
    subtitle: "Descubre todas las películas disponibles en nuestro cine",
    imageUrl: "https://source.unsplash.com/1200x600/?movies,cinema",
    overlayOpacity: "50"
  };
  
  onMount(async () => {
    try {
      // Obtener películas desde la API
      const response = await fetch('http://localhost:8000/api/v1/movies');
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Procesar la respuesta según su estructura
      if (Array.isArray(data)) {
        movies = data;
      } else if (data && data.data && Array.isArray(data.data)) {
        // Si la respuesta está dentro de un objeto con propiedad 'data'
        movies = data.data;
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
      
      loading = false;
    } catch (err) {
      console.error('Error cargando películas:', err);
      error = `Error cargando películas: ` + (err instanceof Error ? err.message : String(err));
      loading = false;
    }
  });

  // Estado para filtros
  let searchQuery = '';
  let selectedGenre = '';
  let sortBy = 'rating'; // 'rating', 'title', 'release_date'
  
  // Películas filtradas
  $: filteredMovies = movies
    .filter(movie => {
      // Filtrar por búsqueda
      if (searchQuery && !movie.title.toLowerCase().includes(searchQuery.toLowerCase())) {
        return false;
      }
      
      // Filtrar por género
      if (selectedGenre && !movie.genres.includes(selectedGenre)) {
        return false;
      }
      
      return true;
    })
    .sort((a, b) => {
      // Ordenar según criterio
      if (sortBy === 'rating') {
        return b.rating - a.rating;
      } else if (sortBy === 'title') {
        return a.title.localeCompare(b.title);
      } else if (sortBy === 'release_date') {
        return new Date(b.release_date).getTime() - new Date(a.release_date).getTime();
      }
      return 0;
    });
  
  // Películas destacadas
  $: featuredMovies = movies.filter(movie => movie.is_featured);
  
  // Formatear fecha
  function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
  }
  
  // Resetear filtros
  function resetFilters() {
    searchQuery = '';
    selectedGenre = '';
    sortBy = 'rating';
  }
</script>

<!-- Hero Banner con imagen específica para la página de películas -->
<HeroBanner 
  title="Cartelera"
  subtitle="Descubre todas las películas disponibles en nuestro cine"
  imageUrl="https://source.unsplash.com/random/1920x1080/?movies,cinema,popcorn"
  overlayOpacity="60"
/>

<!-- Contenido específico de la página -->
<div class="container mx-auto px-4 py-8">
  <h2 class="text-2xl font-bold mb-6">Películas en cartelera</h2>
  
  {#if loading}
    <div class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
    </div>
  {:else if error}
    <div class="bg-red-900/20 border border-red-500/30 text-red-200 p-4 rounded-md">
      <p>{error}</p>
    </div>
  {:else if movies.length === 0}
    <div class="bg-blue-900/20 border border-blue-500/30 text-blue-200 p-4 rounded-md">
      <p>No hay películas disponibles en este momento.</p>
    </div>
  {:else}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      {#each movies as movie}
        <div class="bg-card border border-white/10 rounded-lg overflow-hidden transition-transform hover:scale-105">
          <a href={`/movies/${movie.id}`} class="block">
            <div class="h-64 overflow-hidden">
              <img 
                src={movie.poster_url || 'https://via.placeholder.com/300x450?text=No+Image'} 
                alt={movie.title} 
                class="w-full h-full object-cover"
              />
            </div>
            <div class="p-4">
              <h3 class="text-lg font-bold mb-1 line-clamp-1">{movie.title}</h3>
              <div class="flex items-center text-sm text-gray-400 mb-2">
                <span>{movie.release_year}</span>
                <span class="mx-2">•</span>
                <span>{movie.duration} min</span>
              </div>
              <div class="flex flex-wrap gap-2">
                {#each movie.categories || [] as category}
                  <span class="bg-white/10 text-xs py-1 px-2 rounded-md">
                    {category}
                  </span>
                {/each}
              </div>
            </div>
          </a>
        </div>
      {/each}
    </div>
  {/if}
</div>
