<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  
  // Películas de ejemplo (en un entorno real vendrían de una API)
  const movies = [
    { id: 1, title: 'Inception', director: 'Christopher Nolan', year: 2010, status: 'active', poster: 'https://source.unsplash.com/random/300x450/?inception', genre: 'Sci-Fi, Action' },
    { id: 2, title: 'The Godfather', director: 'Francis Ford Coppola', year: 1972, status: 'active', poster: 'https://source.unsplash.com/random/300x450/?godfather', genre: 'Crime, Drama' },
    { id: 3, title: 'Pulp Fiction', director: 'Quentin Tarantino', year: 1994, status: 'active', poster: 'https://source.unsplash.com/random/300x450/?pulp', genre: 'Crime, Drama' },
    { id: 4, title: 'The Dark Knight', director: 'Christopher Nolan', year: 2008, status: 'inactive', poster: 'https://source.unsplash.com/random/300x450/?batman', genre: 'Action, Crime' },
    { id: 5, title: 'Dune: Part Two', director: 'Denis Villeneuve', year: 2023, status: 'active', poster: 'https://source.unsplash.com/random/300x450/?dune', genre: 'Sci-Fi, Adventure' },
  ];
  
  // Estadísticas básicas
  const totalMovies = movies.length;
  const activeMovies = movies.filter(m => m.status === 'active').length;
  const inactiveMovies = movies.filter(m => m.status === 'inactive').length;
  
  // Estado para búsqueda y filtros
  let searchQuery = "";
  let statusFilter = "";
  
  // Filtrado de películas
  $: filteredMovies = movies.filter(movie => {
    let matchesStatus = true;
    let matchesSearch = true;
    
    if (statusFilter && movie.status !== statusFilter) {
      matchesStatus = false;
    }
    
    if (searchQuery) {
      const query = searchQuery.toLowerCase();
      matchesSearch = 
        movie.title.toLowerCase().includes(query) ||
        movie.director.toLowerCase().includes(query) ||
        movie.genre.toLowerCase().includes(query);
    }
    
    return matchesStatus && matchesSearch;
  });
  
  // Función para obtener clase de badge según el estado
  function getStatusBadgeClass(status: string): string {
    return status === 'active' ? 'bg-success' : 'bg-danger';
  }
  
  function resetFilters(): void {
    searchQuery = "";
    statusFilter = "";
  }
  
  // Seleccionar/deseleccionar películas
  let selectedMovies = new Set<number>();
  let selectAll = false;
  
  $: {
    if (selectAll) {
      selectedMovies = new Set(filteredMovies.map(movie => movie.id));
    }
  }
  
  function toggleSelectAll(): void {
    selectAll = !selectAll;
    if (!selectAll) {
      selectedMovies = new Set();
    }
  }
  
  function toggleSelectMovie(id: number): void {
    if (selectedMovies.has(id)) {
      selectedMovies.delete(id);
      selectAll = false;
    } else {
      selectedMovies.add(id);
      if (selectedMovies.size === filteredMovies.length) {
        selectAll = true;
      }
    }
    selectedMovies = selectedMovies; // Trigger reactivity
  }
  
  onMount(() => {
    // Initialize any Bootstrap components if needed
  });
</script>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">{$t('movies')}</h1>
    <div>
      {#if selectedMovies.size > 0}
        <div class="btn-group me-2">
          <button class="btn btn-sm btn-outline-primary">
            <i class="bi bi-download me-1"></i> {$t('export')}
          </button>
          <button class="btn btn-sm btn-outline-danger">
            <i class="bi bi-trash me-1"></i> {$t('delete')}
          </button>
        </div>
      {/if}
      <a href="/admin/movies/add" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        {$t('addMovie')}
      </a>
    </div>
  </div>
  
  <!-- Dashboard Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card border-primary h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-film text-primary fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">{$t('totalMovies')}</h6>
              <h2 class="card-title mb-0">{totalMovies}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-success h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-check-circle text-success fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">{$t('activeMovies')}</h6>
              <h2 class="card-title mb-0">{activeMovies}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-danger h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-x-circle text-danger fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">{$t('inactiveMovies')}</h6>
              <h2 class="card-title mb-0">{inactiveMovies}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Filtros y búsqueda -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-8">
          <div class="input-group">
            <span class="input-group-text">
              <i class="bi bi-search"></i>
            </span>
            <input 
              type="text" 
              class="form-control" 
              placeholder={$t('searchMovies')}
              bind:value={searchQuery}
            />
          </div>
        </div>
        
        <div class="col-md-3">
          <select 
            class="form-select"
            bind:value={statusFilter}
          >
            <option value="">{$t('allStatuses')}</option>
            <option value="active">{$t('active')}</option>
            <option value="inactive">{$t('inactive')}</option>
          </select>
        </div>
        
        <div class="col-md-1 d-flex align-items-center">
          <button class="btn btn-outline-secondary w-100" on:click={resetFilters}>
            <i class="bi bi-x-circle"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Tabla de películas -->
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col" style="width: 40px;">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="selectAll" bind:checked={selectAll} on:click={toggleSelectAll}>
                </div>
              </th>
              <th scope="col" style="width: 60px;">{$t('id')}</th>
              <th scope="col">{$t('movie')}</th>
              <th scope="col">{$t('director')}</th>
              <th scope="col">{$t('year')}</th>
              <th scope="col">{$t('genre')}</th>
              <th scope="col">{$t('status')}</th>
              <th scope="col" class="text-end">{$t('actions')}</th>
            </tr>
          </thead>
          <tbody>
            {#each filteredMovies as movie}
              <tr class={selectedMovies.has(movie.id) ? 'table-active' : ''}>
                <td>
                  <div class="form-check">
                    <input 
                      class="form-check-input" 
                      type="checkbox" 
                      checked={selectedMovies.has(movie.id)}
                      on:click={() => toggleSelectMovie(movie.id)}
                    >
                  </div>
                </td>
                <td>{movie.id}</td>
                <td>
                  <div class="d-flex align-items-center">
                    <img src={movie.poster} alt={movie.title} class="rounded me-2" style="width: 40px; height: 60px; object-fit: cover;">
                    <div>
                      <strong>{movie.title}</strong>
                    </div>
                  </div>
                </td>
                <td>{movie.director}</td>
                <td>{movie.year}</td>
                <td><small class="text-muted">{movie.genre}</small></td>
                <td>
                  <span class={`badge ${getStatusBadgeClass(movie.status)}`}>
                    {$t(movie.status)}
                  </span>
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    <a href={`/admin/movies/${movie.id}`} class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href={`/admin/movies/${movie.id}/edit`} class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            {/each}
            
            {#if filteredMovies.length === 0}
              <tr>
                <td colspan="8" class="text-center py-4">
                  <i class="bi bi-search fs-4 mb-2 d-block text-muted"></i>
                  <p class="text-muted">{$t('noMoviesFound')}</p>
                </td>
              </tr>
            {/if}
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
      <div>
        {#if filteredMovies.length > 0}
          <small class="text-muted">
            {$t('showing')} {filteredMovies.length} {$t('of')} {movies.length} {$t('movies')}
          </small>
        {/if}
      </div>
      <nav aria-label="Movies pagination">
        <ul class="pagination pagination-sm mb-0">
          <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
          <li class="page-item active"><a class="page-link" href="#">1</a></li>
          <li class="page-item"><a class="page-link" href="#">2</a></li>
          <li class="page-item"><a class="page-link" href="#">3</a></li>
          <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
        </ul>
      </nav>
    </div>
  </div>
</div> 