<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';

  let movies = [];
  let loading = true;
  let error = null;
  let searchQuery = '';
  let filters = {
    genre: '',
    year: '',
    rating: ''
  };

  // Paginación
  let currentPage = 1;
  let totalPages = 1;
  let itemsPerPage = 9;
  let totalItems = 0;

  // Para los filtros
  let genres = [];
  let years = [];

  async function fetchMovies() {
    loading = true;
    try {
      // Construir URL con parámetros de búsqueda, filtros y paginación
      let url = `${API_URL}/movies`;
      let params = new URLSearchParams();
      
      if (searchQuery) {
        params.append('search', searchQuery);
      }
      
      if (filters.genre) {
        params.append('genre', filters.genre);
      }
      
      if (filters.year) {
        params.append('year', filters.year);
      }
      
      if (filters.rating) {
        params.append('rating', filters.rating);
      }
      
      // Añadir parámetros de paginación
      params.append('page', currentPage.toString());
      params.append('limit', itemsPerPage.toString());
      
      // Añadir parámetros a la URL
      url += `?${params.toString()}`;
      
      const response = await fetch(url, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (response.ok) {
        movies = data.data || data;
        // Asumiendo que el API devuelve metadata de paginación
        totalItems = data.total || movies.length;
        totalPages = data.pages || Math.ceil(totalItems / itemsPerPage);
      } else {
        error = data.message || 'Error al cargar las películas';
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
    } finally {
      loading = false;
    }
  }

  async function fetchFilters() {
    try {
      // En una implementación real, obtendrías esta información del servidor
      genres = ['Acción', 'Aventura', 'Comedia', 'Drama', 'Ciencia Ficción', 'Terror'];
      years = ['2023', '2022', '2021', '2020', '2019'];
    } catch (e) {
      console.error('Error al cargar filtros:', e);
    }
  }

  function handleSearch() {
    fetchMovies();
  }

  function handlePageChange(page) {
    if (page >= 1 && page <= totalPages) {
      currentPage = page;
      fetchMovies();
      window.scrollTo(0, 0);
    }
  }

  function resetFilters() {
    filters = {
      genre: '',
      year: '',
      rating: ''
    };
    searchQuery = '';
    currentPage = 1;
    fetchMovies();
  }

  onMount(() => {
    fetchMovies();
    fetchFilters();
  });
</script>

<div class="container mx-auto py-8 px-4 max-w-7xl">
  <h1 class="text-3xl font-bold mb-6 text-gray-800">Catálogo de Películas</h1>
  
  <!-- Buscador y Filtros -->
  <div class="mb-8 bg-white p-4 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
      <div class="md:col-span-6">
        <div class="relative">
          <input 
            type="text" 
            class="w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors" 
            placeholder="Buscar películas..." 
            bind:value={searchQuery}
            on:keyup={(e) => e.key === 'Enter' && handleSearch()}
          >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <button 
            class="absolute right-1 top-1 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-md transition duration-150 ease-in-out"
            on:click={handleSearch}
          >
            Buscar
          </button>
        </div>
      </div>
      
      <div class="md:col-span-2">
        <div class="relative">
          <select 
            class="w-full appearance-none pl-4 pr-10 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors bg-white" 
            bind:value={filters.genre} 
            on:change={handleSearch}
          >
            <option value="">Todos los géneros</option>
            {#each genres as genre}
              <option value={genre}>{genre}</option>
            {/each}
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>
      
      <div class="md:col-span-2">
        <div class="relative">
          <select 
            class="w-full appearance-none pl-4 pr-10 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors bg-white" 
            bind:value={filters.year} 
            on:change={handleSearch}
          >
            <option value="">Todos los años</option>
            {#each years as year}
              <option value={year}>{year}</option>
            {/each}
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>
      
      <div class="md:col-span-2">
        <div class="relative">
          <select 
            class="w-full appearance-none pl-4 pr-10 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors bg-white"
            bind:value={filters.rating}
            on:change={handleSearch}
          >
            <option value="">Todas las calificaciones</option>
            <option value="5">5 estrellas</option>
            <option value="4">4+ estrellas</option>
            <option value="3">3+ estrellas</option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-4">
      <button 
        class="flex items-center text-sm border border-gray-300 rounded-md px-4 py-2 bg-gray-50 hover:bg-gray-100 transition duration-150" 
        on:click={resetFilters}
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Limpiar filtros
      </button>
    </div>
  </div>
  
  {#if loading}
    <div class="text-center my-16">
      <div class="inline-block w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
      <p class="mt-4 text-lg text-gray-600">Cargando películas...</p>
    </div>
  {:else if error}
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md text-red-700 mb-6">{error}</div>
  {:else if movies.length === 0}
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md text-blue-700 mb-6">
      No se encontraron películas con los criterios seleccionados.
    </div>
  {:else}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {#each movies as movie}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
          <div class="relative h-60 overflow-hidden">
            <img 
              src={movie.photo_url || '/img/default-movie.jpg'} 
              class="w-full h-full object-cover transform transition-transform duration-500 hover:scale-105" 
              alt={movie.title}
              loading="lazy">
            {#if movie.rating}
              <div class="absolute top-3 right-3 bg-yellow-400 text-yellow-800 font-bold rounded-full w-10 h-10 flex items-center justify-center shadow-md">
                {movie.rating}
              </div>
            {/if}
          </div>
          <div class="p-5 flex-grow">
            <div class="flex justify-between items-start mb-2">
              <h2 class="font-bold text-xl text-gray-800">{movie.title}</h2>
            </div>
            <div class="flex items-center mb-3">
              <span class="text-sm text-gray-500 mr-3">{movie.year || 'Sin año'}</span>
              {#if movie.genre}
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{movie.genre}</span>
              {/if}
            </div>
            <p class="text-gray-600 line-clamp-3 mb-4 text-sm">{movie.description || 'Sin descripción disponible'}</p>
          </div>
          <div class="px-5 pb-5 border-t border-gray-100 pt-3 mt-auto">
            <a href={`/movies/${movie.id}`} class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-medium px-4 py-2 rounded-lg transition duration-200">
              Ver detalles
            </a>
          </div>
        </div>
      {/each}
    </div>

    <!-- Paginación -->
    {#if totalPages > 1}
      <div class="flex justify-center mt-10">
        <nav class="flex items-center space-x-2">
          <button 
            class="px-3 py-2 rounded-md {currentPage === 1 ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}"
            disabled={currentPage === 1}
            on:click={() => handlePageChange(currentPage - 1)}
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </button>
          
          {#each Array(totalPages) as _, i}
            {#if i + 1 === currentPage || i + 1 === 1 || i + 1 === totalPages || (i + 1 >= currentPage - 1 && i + 1 <= currentPage + 1)}
              <button 
                class="w-10 h-10 rounded-md {i + 1 === currentPage ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'}"
                on:click={() => handlePageChange(i + 1)}
              >
                {i + 1}
              </button>
            {:else if i + 1 === currentPage - 2 || i + 1 === currentPage + 2}
              <span class="px-1 text-gray-500">...</span>
            {/if}
          {/each}
          
          <button 
            class="px-3 py-2 rounded-md {currentPage === totalPages ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}"
            disabled={currentPage === totalPages}
            on:click={() => handlePageChange(currentPage + 1)}
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </button>
        </nav>
      </div>
    {/if}
  {/if}
</div>
