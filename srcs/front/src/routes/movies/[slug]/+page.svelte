<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  
  export let data;
  
  let movie = null;
  let loading = true;
  let error = null;
  
  // Función para formatear la fecha
  function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-ES', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    }).format(date);
  }
  
  onMount(async () => {
    try {
      const response = await fetch(`${API_URL}/movies/${data.slug}`, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      const result = await response.json();
      
      if (response.ok && result.success) {
        movie = result.data;
        console.log('Movie data:', movie);
      } else {
        error = result.message || 'No se pudo cargar la información de la película';
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
    } finally {
      loading = false;
    }
  });

  function goBack() {
    history.back();
  }
  
  function shareMovie() {
    if (navigator.share) {
      navigator.share({
        title: movie.title,
        text: `Echa un vistazo a ${movie.title}`,
        url: window.location.href
      })
      .catch((error) => console.log('Error al compartir:', error));
    } else {
      // Fallback para navegadores que no soportan Web Share API
      navigator.clipboard.writeText(window.location.href)
        .then(() => alert('Enlace copiado al portapapeles'))
        .catch(() => alert('No se pudo copiar el enlace'));
    }
  }
</script>

<!-- Diseño horizontal con fondo transparente, pegado a la navbar -->
<div class="pt-0 pb-4 px-4">
  {#if loading}
    <div class="min-h-[30vh] flex items-center justify-center">
      <div class="w-10 h-10 border-2 border-t-transparent border-white rounded-full animate-spin"></div>
      <p class="ml-3 text-white text-opacity-80">Cargando...</p>
    </div>
  {:else if error}
    <div class="min-h-[30vh] flex items-center justify-center">
      <div class="backdrop-blur-md bg-black bg-opacity-40 p-6 rounded-xl border border-white border-opacity-10 max-w-md">
        <p class="text-red-400 font-medium text-center">{error}</p>
        <button 
          class="mt-4 w-full py-2 bg-white bg-opacity-10 hover:bg-opacity-20 text-white rounded-lg transition duration-300" 
          on:click={goBack}
        >
          Volver
        </button>
      </div>
    </div>
  {:else if movie}
    <!-- Contenedor principal -->
    <div class="w-full flex flex-col">
      <!-- Botón de regreso -->
      <button 
        class="self-start mb-2 text-white text-opacity-70 hover:text-opacity-100 flex items-center transition duration-300" 
        on:click={goBack}
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Volver
      </button>
      
      <!-- Vista horizontal -->
      <div class="flex flex-col lg:flex-row w-full backdrop-blur-md bg-black bg-opacity-30 rounded-2xl overflow-hidden border border-white border-opacity-10">
        <!-- Imagen de fondo con gradiente -->
        <div class="fixed inset-0 -z-10">
          {#if movie.photo_url}
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/80 to-transparent"></div>
            <img 
              src={movie.photo_url} 
              alt="" 
              class="w-full h-full object-cover opacity-20"
            />
          {/if}
        </div>
        
        <!-- Poster de la película -->
        <div class="lg:w-1/4 xl:w-1/5">
          <div class="h-72 lg:h-full relative">
            <img 
              src={movie.photo_url || '/img/default-movie.jpg'} 
              alt={movie.title}
              class="w-full h-full object-cover"
            />
          </div>
        </div>
        
        <!-- Información -->
        <div class="lg:w-3/4 xl:w-4/5 p-6 flex flex-col">
          <!-- Encabezado con título y clasificación -->
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
            <div>
              <h1 class="text-3xl font-bold text-white">{movie.title}</h1>
              <div class="flex flex-wrap items-center mt-2">
                {#if movie.release_date}
                  <span class="text-white text-opacity-60 text-sm mr-3">{formatDate(movie.release_date)}</span>
                {/if}
                
                {#if movie.duration}
                  <span class="text-white text-opacity-60 text-sm flex items-center mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {movie.duration} min
                  </span>
                {/if}
              </div>
            </div>
            
            {#if movie.rating}
              <div class="mt-3 sm:mt-0 bg-white bg-opacity-5 rounded-full px-3 py-1 backdrop-blur-sm">
                <span class="text-sm font-bold text-white text-opacity-90">{movie.rating}</span>
              </div>
            {/if}
          </div>
          
          <!-- Contenido en distribución horizontal -->
          <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-auto">
            <!-- Sinopsis -->
            <div>
              <h2 class="text-lg font-medium text-white text-opacity-90 mb-2">Sinopsis</h2>
              <p class="text-white text-opacity-70">{movie.synopsis || 'No hay sinopsis disponible para esta película.'}</p>
            </div>
            
            <!-- Información adicional -->
            <div class="flex flex-wrap mt-4 gap-x-8 gap-y-2">
              {#if movie.slug}
                <div>
                  <h3 class="text-xs font-medium text-white text-opacity-50">ID</h3>
                  <p class="text-white text-opacity-90 text-sm">{movie.id}</p>
                </div>
              {/if}
              
              {#if movie.created_at}
                <div>
                  <h3 class="text-xs font-medium text-white text-opacity-50">Añadida el</h3>
                  <p class="text-white text-opacity-90 text-sm">{formatDate(movie.created_at)}</p>
                </div>
              {/if}
            </div>
          </div>
          
          <!-- Acciones -->
          <div class="mt-6 flex flex-wrap gap-3">
            <!-- Botón de agregar a lista -->
            <button class="bg-white bg-opacity-10 hover:bg-opacity-20 text-white px-6 py-2 rounded-full transition duration-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Mi lista
            </button>
            
            <!-- Botón de compartir mejorado -->
            <button 
              class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full transition duration-300" 
              on:click={shareMovie}
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
              </svg>
              Compartir
            </button>
          </div>
        </div>
      </div>
    </div>
  {:else}
    <div class="min-h-[30vh] flex items-center justify-center">
      <div class="backdrop-blur-md bg-black bg-opacity-40 p-6 rounded-xl border border-white border-opacity-10 max-w-md">
        <p class="text-white text-opacity-80 text-center">No se encontró la película solicitada.</p>
        <button 
          class="mt-4 w-full py-2 bg-white bg-opacity-10 hover:bg-opacity-20 text-white rounded-lg transition duration-300" 
          on:click={goBack}
        >
          Volver
        </button>
      </div>
    </div>
  {/if}
</div>

<style>
  /* Esto asume que hay un fondo oscuro en la aplicación principal */
  :global(body) {
    background-color: #121212;
    color: white;
  }
</style>