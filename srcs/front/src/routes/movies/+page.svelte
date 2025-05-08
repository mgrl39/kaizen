<script lang="ts">
  import { t } from '$lib/i18n';

  // Géneros para filtrar
  const genres = [
    'Acción', 'Aventura', 'Comedia', 'Drama', 'Ciencia Ficción', 
    'Terror', 'Romance', 'Animación', 'Fantasía', 'Documental'
  ];

  // Datos hardcodeados de películas
  const movies = [
    {
      id: 1,
      title: 'Dune: Parte Dos',
      slug: 'dune-parte-dos',
      director: 'Denis Villeneuve',
      duration: 166,
      release_date: '2024-03-01',
      genres: ['Ciencia Ficción', 'Aventura', 'Drama'],
      rating: 8.7,
      synopsis: 'Paul Atreides se une a los Fremen y comienza un viaje espiritual y político para vengar a su familia mientras trata de salvar el futuro de Arrakis y de toda la galaxia.',
      image_url: 'https://source.unsplash.com/800x1200/?dune,movie',
      trailer_url: 'https://www.youtube.com/watch?v=Way9Dexny3w',
      is_featured: true,
      is_now_playing: true
    },
    {
      id: 2,
      title: 'Oppenheimer',
      slug: 'oppenheimer',
      director: 'Christopher Nolan',
      duration: 180,
      release_date: '2023-07-21',
      genres: ['Drama', 'Historia', 'Biografía'],
      rating: 8.5,
      synopsis: 'La historia del científico J. Robert Oppenheimer y su papel en el desarrollo de la bomba atómica durante la Segunda Guerra Mundial.',
      image_url: 'https://source.unsplash.com/800x1200/?nuclear,explosion',
      trailer_url: 'https://www.youtube.com/watch?v=uYPbbksJxIg',
      is_featured: true,
      is_now_playing: true
    },
    {
      id: 3,
      title: 'Barbie',
      slug: 'barbie',
      director: 'Greta Gerwig',
      duration: 114,
      release_date: '2023-07-21',
      genres: ['Comedia', 'Aventura', 'Fantasía'],
      rating: 7.3,
      synopsis: 'Barbie y Ken disfrutan de una vida perfecta en el colorido y aparentemente perfecto mundo de Barbie Land. Sin embargo, cuando tienen la oportunidad de ir al mundo real, pronto descubren las alegrías y los peligros de vivir entre los humanos.',
      image_url: 'https://source.unsplash.com/800x1200/?pink,doll',
      trailer_url: 'https://www.youtube.com/watch?v=pBk4NYhWNMM',
      is_featured: true,
      is_now_playing: true
    },
    {
      id: 4,
      title: 'Pobres Criaturas',
      slug: 'pobres-criaturas',
      director: 'Yorgos Lanthimos',
      duration: 141,
      release_date: '2024-01-26',
      genres: ['Comedia', 'Drama', 'Ciencia Ficción'],
      rating: 8.1,
      synopsis: 'La increíble historia de Bella Baxter, una joven que ha sido devuelta a la vida por el brillante y poco ortodoxo científico Dr. Godwin Baxter.',
      image_url: 'https://source.unsplash.com/800x1200/?vintage,laboratory',
      trailer_url: 'https://www.youtube.com/watch?v=RlbR5N6veqw',
      is_featured: false,
      is_now_playing: true
    },
    {
      id: 5,
      title: 'Los Asesinos de la Luna',
      slug: 'los-asesinos-de-la-luna',
      director: 'Martin Scorsese',
      duration: 206,
      release_date: '2023-10-20',
      genres: ['Crimen', 'Drama', 'Historia'],
      rating: 7.8,
      synopsis: 'Miembros de la tribu Osage en Oklahoma son asesinados bajo misteriosas circunstancias en la década de 1920, desencadenando una importante investigación del FBI.',
      image_url: 'https://source.unsplash.com/800x1200/?moon,western',
      trailer_url: 'https://www.youtube.com/watch?v=EP34Yoxs3FQ',
      is_featured: false,
      is_now_playing: true
    },
    {
      id: 6,
      title: 'Napoleón',
      slug: 'napoleon',
      director: 'Ridley Scott',
      duration: 158,
      release_date: '2023-11-24',
      genres: ['Acción', 'Drama', 'Historia'],
      rating: 6.5,
      synopsis: 'Una mirada personal a los orígenes de Napoleón Bonaparte y su rápido y despiadado ascenso a emperador.',
      image_url: 'https://source.unsplash.com/800x1200/?napoleon,france',
      trailer_url: 'https://www.youtube.com/watch?v=OAZWXUkrjPc',
      is_featured: false,
      is_now_playing: true
    }
  ];

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

<div class="max-w-7xl mx-auto px-4 py-8">
  <h2 class="text-3xl font-bold mb-8 section-title">Cartelera</h2>
  
  <!-- Películas destacadas - Carrusel -->
  {#if featuredMovies.length > 0}
    <div class="mb-12">
      <h3 class="text-2xl font-bold mb-6">Películas destacadas</h3>
      
      <div class="relative rounded-xl overflow-hidden">
        <!-- Simplificado: mostramos solo la primera película destacada -->
        {#if featuredMovies[0]}
          <div class="relative h-96 md:h-[500px]">
            <img 
              src={featuredMovies[0].image_url} 
              alt={featuredMovies[0].title} 
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent"></div>
            <div class="absolute bottom-0 left-0 p-6 md:p-10 w-full md:w-2/3">
              <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">{featuredMovies[0].title}</h1>
              <div class="flex items-center text-white/80 mb-4">
                <span class="bg-purple-800 text-white text-sm px-2 py-1 rounded mr-3">
                  {featuredMovies[0].rating.toFixed(1)}
                </span>
                <span class="mr-4">{featuredMovies[0].duration} min</span>
                <span>{formatDate(featuredMovies[0].release_date)}</span>
              </div>
              <p class="text-white/80 mb-6 line-clamp-2 md:line-clamp-3">
                {featuredMovies[0].synopsis}
              </p>
              <div class="flex flex-wrap gap-2 mb-6">
                {#each featuredMovies[0].genres as genre}
                  <span class="bg-white/10 text-white text-xs py-1 px-2 rounded-md">
                    {genre}
                  </span>
                {/each}
              </div>
              <div class="flex flex-col sm:flex-row gap-3">
                <a 
                  href={`/movies/${featuredMovies[0].slug}`} 
                  class="bg-purple-800 hover:bg-purple-700 text-white py-2 px-4 rounded-md flex items-center justify-center transition-colors"
                >
                  <i class="bi bi-info-circle mr-2"></i>
                  Ver detalles
                </a>
                <a 
                  href={featuredMovies[0].trailer_url} 
                  target="_blank" 
                  rel="noopener noreferrer"
                  class="bg-transparent border border-white/20 text-white py-2 px-4 rounded-md flex items-center justify-center hover:bg-white/10 transition-colors"
                >
                  <i class="bi bi-play-circle mr-2"></i>
                  Ver trailer
                </a>
              </div>
            </div>
          </div>
        {/if}
      </div>
    </div>
  {/if}
  
  <!-- Buscador y Filtros -->
  <div class="bg-card border border-white/10 rounded-lg p-4 mb-8">
    <div class="flex flex-col md:flex-row gap-4">
      <div class="flex-grow">
        <div class="relative">
          <input 
            type="text" 
            class="w-full bg-dark border border-white/10 rounded-md py-2 px-4 pr-10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
            placeholder="Buscar películas..." 
            bind:value={searchQuery}
          >
          <i class="bi bi-search absolute right-3 top-1/2 -translate-y-1/2 text-white/60"></i>
        </div>
      </div>
      
      <div class="md:w-48">
          <select 
          class="w-full bg-dark border border-white/10 rounded-md py-2 px-4 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 appearance-none"
          bind:value={selectedGenre}
          >
            <option value="">Todos los géneros</option>
            {#each genres as genre}
              <option value={genre}>{genre}</option>
            {/each}
          </select>
      </div>
      
      <div class="md:w-48">
          <select 
          class="w-full bg-dark border border-white/10 rounded-md py-2 px-4 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 appearance-none"
          bind:value={sortBy}
        >
          <option value="rating">Mejor valoradas</option>
          <option value="release_date">Más recientes</option>
          <option value="title">Alfabético</option>
          </select>
      </div>
      
      <div>
        <button 
          class="w-full md:w-auto bg-transparent border border-white/20 text-white py-2 px-4 rounded-md hover:bg-white/10 transition-colors"
          on:click={resetFilters}
        >
          <i class="bi bi-x-circle mr-2"></i>Limpiar
        </button>
      </div>
    </div>
  </div>
  
  <!-- Lista de películas -->
  {#if filteredMovies.length === 0}
    <div class="bg-blue-900/20 border border-blue-500/50 text-blue-200 rounded-md p-4 mb-6">
      <div class="flex items-center">
        <i class="bi bi-info-circle mr-3 text-xl"></i>
        <p>No hay películas disponibles con los filtros seleccionados.</p>
    </div>
    </div>
  {:else}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      {#each filteredMovies as movie}
        <div class="bg-card border border-white/10 rounded-lg overflow-hidden hover:shadow-lg transition-all hover:-translate-y-1">
          <a href={`/movies/${movie.slug}`} class="block relative group">
            <div class="relative aspect-[2/3] overflow-hidden">
              <img 
                src={movie.image_url} 
              alt={movie.title}
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <span class="bg-purple-800/90 text-white py-2 px-4 rounded-md transform translate-y-4 group-hover:translate-y-0 transition-transform">
                  Ver detalles
                </span>
              </div>
              <div class="absolute top-2 right-2 bg-purple-800 text-white text-sm px-2 py-1 rounded">
                {movie.rating.toFixed(1)}
              </div>
          </div>
          </a>
          
          <div class="p-4">
            <h3 class="text-lg font-bold text-white mb-1 line-clamp-1">{movie.title}</h3>
            
            <div class="flex items-center text-gray-300 text-sm mb-2">
              <span class="mr-2">{movie.duration} min</span>
              <span class="text-white/30">•</span>
              <span class="ml-2">{formatDate(movie.release_date).split('de')[2]}</span>
            </div>
            
            <div class="flex flex-wrap gap-1 mb-3">
              {#each movie.genres.slice(0, 2) as genre}
                <span class="bg-white/10 text-white/80 text-xs py-0.5 px-1.5 rounded">
                  {genre}
                </span>
              {/each}
              {#if movie.genres.length > 2}
                <span class="bg-white/10 text-white/80 text-xs py-0.5 px-1.5 rounded">
                  +{movie.genres.length - 2}
                </span>
              {/if}
            </div>
            
            <div class="flex justify-between items-center">
              <a 
                href={`/movies/${movie.slug}`} 
                class="text-purple-400 hover:text-purple-300 text-sm flex items-center"
              >
                <i class="bi bi-info-circle mr-1"></i>
                Detalles
              </a>
              
              <a 
                href="/bookings/new?movie={movie.id}" 
                class="bg-purple-800 hover:bg-purple-700 text-white text-sm py-1 px-3 rounded transition-colors"
              >
                <i class="bi bi-ticket-perforated mr-1"></i>
                Comprar
              </a>
          </div>
          </div>
        </div>
      {/each}
    </div>
  {/if}
</div>
