<script>
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { API_URL } from '$lib/config';
  import Search from '$lib/components/Search.svelte';
  import { writable } from 'svelte/store';

  // Stores para la página
  const results = writable([]);
  const isLoading = writable(true);
  const error = writable(null);
  const totalResults = writable(0);

  // Obtener parámetros de la URL
  $: query = $page.url.searchParams.get('q') || '';
  $: category = $page.url.searchParams.get('category') || 'all';
  $: page_num = parseInt($page.url.searchParams.get('page') || '1');

  // Función para realizar la búsqueda según la categoría
  async function performSearch() {
    if (!query) {
      $results = [];
      $isLoading = false;
      return;
    }

    $isLoading = true;
    $error = null;

    try {
      // Determinar el endpoint según la categoría
      let endpoint = '';
      let params = new URLSearchParams();
      
      if (category === 'movies') {
        // Usar el endpoint de búsqueda de películas
        endpoint = `${API_URL}/movies/search`;
        params.append('title', query);
      } else if (category === 'cinemas') {
        // Usar el endpoint de búsqueda de cines
        endpoint = `${API_URL}/cinemas`;
        params.append('name', query);
      } else {
        // Búsqueda general (combina resultados)
        endpoint = `${API_URL}/search`;
        params.append('q', query);
      }
      
      // Añadir parámetros de paginación
      params.append('page', page_num.toString());
      params.append('per_page', '10');
      
      const response = await fetch(`${endpoint}?${params.toString()}`);
      
      if (!response.ok) {
        throw new Error(`Error en la búsqueda: ${response.statusText}`);
      }
      
      const data = await response.json();
      
      if (data.success) {
        $results = data.data || [];
        $totalResults = data.pagination?.total || 0;
      } else {
        throw new Error(data.message || 'Error desconocido en la búsqueda');
      }
    } catch (err) {
      console.error('Error al buscar:', err);
      $error = err.message;
      $results = [];
    } finally {
      $isLoading = false;
    }
  }

  // Realizar búsqueda cuando cambian los parámetros
  $: {
    if (query) {
      performSearch();
    } else {
      $results = [];
      $isLoading = false;
    }
  }

  // Función para formatear los resultados según su tipo
  function getItemType(item) {
    if (item.hasOwnProperty('title') && item.hasOwnProperty('synopsis')) {
      return 'movie';
    } else if (item.hasOwnProperty('name') && item.hasOwnProperty('address')) {
      return 'cinema';
    } else if (item.hasOwnProperty('date') && item.hasOwnProperty('time')) {
      return 'session';
    }
    return 'unknown';
  }

  // Función para obtener la URL de detalle según el tipo
  function getDetailUrl(item) {
    const type = getItemType(item);
    
    if (type === 'movie') {
      return `/movies/${item.slug || item.id}`;
    } else if (type === 'cinema') {
      return `/cinemas/${item.slug || item.id}`;
    } else if (type === 'session') {
      return `/sessions/${item.id}`;
    }
    
    return '#';
  }

  // Función para obtener el título del resultado
  function getItemTitle(item) {
    const type = getItemType(item);
    
    if (type === 'movie') {
      return item.title;
    } else if (type === 'cinema') {
      return item.name;
    } else if (type === 'session') {
      return `Sesión: ${item.movie_title || 'Película'}`;
    }
    
    return 'Resultado';
  }

  // Función para obtener la descripción del resultado
  function getItemDescription(item) {
    const type = getItemType(item);
    
    if (type === 'movie') {
      return item.synopsis?.substring(0, 150) + (item.synopsis?.length > 150 ? '...' : '');
    } else if (type === 'cinema') {
      return item.address;
    } else if (type === 'session') {
      return `${item.date} - ${item.time} - ${item.cinema_name || 'Cine'}`;
    }
    
    return '';
  }
</script>

<svelte:head>
  <title>{query ? `Búsqueda: ${query}` : 'Búsqueda'} | Kaizen Cinema</title>
</svelte:head>

<div class="container mt-5 pt-5">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-10">
      <h1 class="h3 mb-4">
        <i class="bi bi-search me-2"></i>
        Búsqueda
      </h1>
      
      <!-- Componente de búsqueda -->
      <div class="mb-5">
        <Search />
      </div>
      
      {#if query}
        <!-- Resultados de búsqueda -->
        <div class="search-results">
          <h2 class="h5 mb-3">
            Resultados para: <span class="text-primary">"{query}"</span>
            {#if $totalResults > 0}
              <span class="text-muted">({$totalResults} resultados)</span>
            {/if}
          </h2>
          
          {#if $isLoading}
            <div class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
              <p class="mt-3">Buscando resultados...</p>
            </div>
          {:else if $error}
            <div class="alert alert-danger">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Error: {$error}
            </div>
          {:else if $results.length === 0}
            <div class="text-center py-5">
              <i class="bi bi-search text-muted display-1"></i>
              <h3 class="mt-3">No se encontraron resultados</h3>
              <p class="text-muted">Intenta con otros términos de búsqueda</p>
            </div>
          {:else}
            <div class="row row-cols-1 row-cols-md-2 g-4">
              {#each $results as item}
                {@const itemType = getItemType(item)}
                <div class="col">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-3">
                        <div class="search-result-icon me-3">
                          <i class="bi bi-{itemType === 'movie' ? 'film' : itemType === 'cinema' ? 'building' : 'calendar-event'} fs-3"></i>
                        </div>
                        <div>
                          <h3 class="h5 card-title mb-1">{getItemTitle(item)}</h3>
                          <div class="badge bg-secondary">
                            {itemType === 'movie' ? 'Película' : itemType === 'cinema' ? 'Cine' : 'Sesión'}
                          </div>
                        </div>
                      </div>
                      
                      <p class="card-text">{getItemDescription(item)}</p>
                      
                      {#if itemType === 'movie'}
                        <div class="mt-2 mb-3">
                          {#if item.rating}
                            <span class="badge bg-info me-1">{item.rating}</span>
                          {/if}
                          {#if item.duration}
                            <span class="badge bg-light text-dark me-1">{item.duration} min</span>
                          {/if}
                          {#if item.release_date}
                            <span class="badge bg-light text-dark">{new Date(item.release_date).getFullYear()}</span>
                          {/if}
                        </div>
                      {/if}
                    </div>
                    <div class="card-footer bg-transparent">
                      <a href={getDetailUrl(item)} class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-right me-1"></i>
                        Ver detalles
                      </a>
                    </div>
                  </div>
                </div>
              {/each}
            </div>
            
            <!-- Paginación -->
            {#if $totalResults > 10}
              <nav aria-label="Paginación" class="mt-4">
                <ul class="pagination justify-content-center">
                  <li class="page-item {page_num <= 1 ? 'disabled' : ''}">
                    <a class="page-link" href="/search?q={query}&category={category}&page={page_num - 1}" aria-label="Anterior">
                      <span aria-hidden="true">&laquo;</span>
                    </a>
                  </li>
                  
                  {#each Array(Math.min(5, Math.ceil($totalResults / 10))) as _, i}
                    {@const pageNum = i + 1}
                    <li class="page-item {pageNum === page_num ? 'active' : ''}">
                      <a class="page-link" href="/search?q={query}&category={category}&page={pageNum}">
                        {pageNum}
                      </a>
                    </li>
                  {/each}
                  
                  {#if Math.ceil($totalResults / 10) > 5}
                    <li class="page-item disabled">
                      <span class="page-link">...</span>
                    </li>
                  {/if}
                  
                  <li class="page-item {page_num >= Math.ceil($totalResults / 10) ? 'disabled' : ''}">
                    <a class="page-link" href="/search?q={query}&category={category}&page={page_num + 1}" aria-label="Siguiente">
                      <span aria-hidden="true">&raquo;</span>
                    </a>
                  </li>
                </ul>
              </nav>
            {/if}
          {/if}
        </div>
      {:else}
        <!-- Página inicial de búsqueda -->
        <div class="text-center py-5">
          <i class="bi bi-search display-1 text-primary"></i>
          <h2 class="mt-4">¿Qué estás buscando?</h2>
          <p class="text-muted mb-4">Busca películas, cines o sesiones disponibles</p>
        </div>
      {/if}
    </div>
  </div>
</div>

<style>
  .search-result-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: var(--bs-light);
  }
</style>