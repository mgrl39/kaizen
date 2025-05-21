<script>
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';
  import { writable } from 'svelte/store';

  // Store básico para la búsqueda
  const searchQuery = writable('');
  const searchCategory = writable('all'); // 'all', 'movies', 'cinemas'
  const isSearching = writable(false);

  let searchInput;

  // Categorías disponibles para búsqueda
  const categories = [
    { id: 'all', label: 'Todo', endpoint: '/search' },
    { id: 'movies', label: 'Películas', endpoint: '/movies/search' },
    { id: 'cinemas', label: 'Cines', endpoint: '/cinemas' }
  ];

  // Manejar envío del formulario
  function handleSubmit(e) {
    e.preventDefault();
    if ($searchQuery) {
      // Determinar la URL basada en la categoría seleccionada
      let searchUrl = '/search';
      
      if ($searchCategory !== 'all') {
        // Si es una categoría específica, usar esa página
        searchUrl = `/${$searchCategory}`;
      }
      
      goto(`${searchUrl}?q=${encodeURIComponent($searchQuery)}`);
    }
  }

  // Cambiar categoría
  function setCategory(categoryId) {
    searchCategory.set(categoryId);
    if (searchInput) {
      searchInput.focus();
    }
  }

  // Limpiar búsqueda
  function clearSearch() {
    searchQuery.set('');
    if (searchInput) {
      searchInput.focus();
    }
  }

  onMount(() => {
    // Enfocar el campo de búsqueda automáticamente en la página de búsqueda
    if (window.location.pathname === '/search' && searchInput) {
      searchInput.focus();
    }
  });
</script>

<div id="search-container" class="search-container">
  <form on:submit={handleSubmit} class="search-form">
    <!-- Categorías de búsqueda -->
    <div class="search-categories mb-2 d-flex justify-content-center">
      <div class="btn-group btn-group-sm">
        {#each categories as category}
          <button 
            type="button" 
            class="btn {$searchCategory === category.id ? 'btn-primary' : 'btn-outline-secondary'}"
            on:click={() => setCategory(category.id)}
          >
            <i class="bi bi-{category.id === 'movies' ? 'film' : category.id === 'cinemas' ? 'building' : 'search'} me-1"></i>
            <span>{category.label}</span>
          </button>
        {/each}
      </div>
    </div>

    <!-- Campo de búsqueda -->
    <div class="input-group">
      <span class="input-group-text bg-transparent border-end-0">
        <i class="bi bi-search"></i>
      </span>
      <input
        type="search"
        bind:this={searchInput}
        bind:value={$searchQuery}
        class="form-control border-start-0"
        placeholder={$searchCategory === 'movies' ? 'Buscar películas...' : 
                    $searchCategory === 'cinemas' ? 'Buscar cines...' : 
                    'Buscar en Kaizen Cinema...'}
        aria-label="Buscar"
        autocomplete="off"
      />
      {#if $searchQuery}
        <button 
          type="button" 
          class="btn btn-outline-secondary border-start-0" 
          on:click={clearSearch}
        >
          <i class="bi bi-x"></i>
        </button>
      {/if}
      <button type="submit" class="btn btn-primary">
        <span class="d-none d-sm-inline">Buscar</span>
        <i class="bi bi-search d-sm-none"></i>
      </button>
    </div>
    
    <!-- Texto de ayuda -->
    <div class="form-text text-center mt-1">
      {#if $searchCategory === 'movies'}
        Busca por título, género o año de estreno
      {:else if $searchCategory === 'cinemas'}
        Busca por nombre o ubicación del cine
      {:else}
        Busca películas, cines o sesiones
      {/if}
    </div>
  </form>
</div>

<style>
  .search-container {
    position: relative;
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
  }
  
  .search-form {
    width: 100%;
  }
  
  .form-text {
    font-size: 0.8rem;
    opacity: 0.8;
  }
</style>