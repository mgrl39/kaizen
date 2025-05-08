<script lang="ts">
  import { t } from '$lib/i18n';
  
  // Variables para el formulario
  let title = '';
  let director = '';
  let year = new Date().getFullYear();
  let description = '';
  let duration = 120; // En minutos
  let genres = [];
  let posterUrl = '';
  let trailerUrl = '';
  let status = 'active';
  
  // Lista de géneros disponibles
  const availableGenres = [
    'action', 'adventure', 'comedy', 'drama', 'fantasy', 
    'horror', 'romance', 'sci-fi', 'thriller', 'documentary'
  ];
  
  // Función para manejar el envío del formulario
  function handleSubmit() {
    // Simulación de envío - en un entorno real, esto enviaría los datos a la API
    console.log({
      title,
      director,
      year,
      description,
      duration,
      genres,
      posterUrl,
      trailerUrl,
      status
    });
    
    // Aquí iría la lógica para enviar los datos a tu backend
    alert('Película añadida correctamente');
  }
  
  // Función para volver atrás
  function goBack() {
    window.history.back();
  }
</script>

<div>
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">{$t('addMovie')}</h1>
    <a href="/admin/movies" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
      <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      {$t('backToMovies')}
    </a>
  </div>
  
  <div class="bg-white shadow overflow-hidden rounded-xl">
    <form on:submit|preventDefault={handleSubmit} class="p-6 space-y-6">
      <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
        <!-- Título de la película -->
        <div class="sm:col-span-4">
          <label for="title" class="block text-sm font-medium text-gray-700">{$t('title')}</label>
          <div class="mt-1">
            <input 
              type="text" 
              name="title" 
              id="title" 
              bind:value={title}
              required
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
            />
          </div>
        </div>
        
        <!-- Director -->
        <div class="sm:col-span-3">
          <label for="director" class="block text-sm font-medium text-gray-700">{$t('director')}</label>
          <div class="mt-1">
            <input 
              type="text" 
              name="director" 
              id="director" 
              bind:value={director}
              required
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
            />
          </div>
        </div>
        
        <!-- Año -->
        <div class="sm:col-span-1">
          <label for="year" class="block text-sm font-medium text-gray-700">{$t('year')}</label>
          <div class="mt-1">
            <input 
              type="number" 
              name="year" 
              id="year" 
              bind:value={year}
              min="1900" 
              max="2100"
              required
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
            />
          </div>
        </div>
        
        <!-- Duración en minutos -->
        <div class="sm:col-span-2">
          <label for="duration" class="block text-sm font-medium text-gray-700">{$t('duration')} ({$t('minutes')})</label>
          <div class="mt-1">
            <input 
              type="number" 
              name="duration" 
              id="duration" 
              bind:value={duration}
              min="1" 
              max="500"
              required
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
            />
          </div>
        </div>
        
        <!-- Descripción -->
        <div class="sm:col-span-6">
          <label for="description" class="block text-sm font-medium text-gray-700">{$t('description')}</label>
          <div class="mt-1">
            <textarea 
              id="description" 
              name="description" 
              bind:value={description}
              rows="4" 
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"
            ></textarea>
          </div>
          <p class="mt-2 text-sm text-gray-500">{$t('briefDescriptionOfMovie')}</p>
        </div>
        
        <!-- Géneros -->
        <div class="sm:col-span-6">
          <label class="block text-sm font-medium text-gray-700">{$t('genres')}</label>
          <div class="mt-2 space-y-2">
            <div class="flex flex-wrap gap-2">
              {#each availableGenres as genre}
                <label class="inline-flex items-center">
                  <input 
                    type="checkbox" 
                    bind:group={genres} 
                    value={genre} 
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">{$t(genre)}</span>
                </label>
              {/each}
            </div>
          </div>
        </div>
        
        <!-- URL del póster -->
        <div class="sm:col-span-3">
          <label for="posterUrl" class="block text-sm font-medium text-gray-700">{$t('posterUrl')}</label>
          <div class="mt-1">
            <input 
              type="url" 
              name="posterUrl" 
              id="posterUrl" 
              bind:value={posterUrl}
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
              placeholder="https://example.com/poster.jpg"
            />
          </div>
        </div>
        
        <!-- URL del tráiler -->
        <div class="sm:col-span-3">
          <label for="trailerUrl" class="block text-sm font-medium text-gray-700">{$t('trailerUrl')}</label>
          <div class="mt-1">
            <input 
              type="url" 
              name="trailerUrl" 
              id="trailerUrl" 
              bind:value={trailerUrl}
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
              placeholder="https://youtube.com/watch?v=example"
            />
          </div>
        </div>
        
        <!-- Estado -->
        <div class="sm:col-span-2">
          <label for="status" class="block text-sm font-medium text-gray-700">{$t('status')}</label>
          <div class="mt-1">
            <select 
              id="status" 
              name="status" 
              bind:value={status}
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
            >
              <option value="active">{$t('active')}</option>
              <option value="inactive">{$t('inactive')}</option>
              <option value="coming_soon">{$t('comingSoon')}</option>
            </select>
          </div>
        </div>
      </div>
      
      <div class="pt-5 border-t border-gray-200">
        <div class="flex justify-end">
          <button 
            type="button" 
            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            on:click={goBack}
          >
            {$t('cancel')}
          </button>
          <button 
            type="submit" 
            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {$t('save')}
          </button>
        </div>
      </div>
    </form>
  </div>
</div> 