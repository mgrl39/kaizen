<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  
  let cinemas = [];
  let loading = true;
  let error = null;
  let searchQuery = '';
  let selectedCinema = null;
  let showDeleteModal = false;
  
  // Paginación
  let currentPage = 1;
  let totalPages = 1;
  let itemsPerPage = 10;
  
  // Ordenación
  let sortBy = 'name';
  let sortDirection = 'asc';
  
  async function fetchCinemas() {
    loading = true;
    const token = localStorage.getItem('token');
    
    if (!token) return;
    
    try {
      // En un entorno real, esto sería una llamada a la API
      // const response = await fetch(`${API_URL}/admin/cinemas?page=${currentPage}&sort=${sortBy}&direction=${sortDirection}`, {
      //   headers: {
      //     'Authorization': `Bearer ${token}`,
      //     'Accept': 'application/json'
      //   }
      // });
      // const data = await response.json();
      
      // Simulamos datos para desarrollo
      setTimeout(() => {
        const mockCinemas = [
          { id: 1, name: 'Cines Kaizen Madrid', location: 'Madrid', rooms_count: 8, status: 'active' },
          { id: 2, name: 'Cines Kaizen Barcelona', location: 'Barcelona', rooms_count: 6, status: 'active' },
          { id: 3, name: 'Cines Kaizen Valencia', location: 'Valencia', rooms_count: 5, status: 'active' },
          { id: 4, name: 'Cines Kaizen Sevilla', location: 'Sevilla', rooms_count: 4, status: 'active' },
          { id: 5, name: 'Cines Kaizen Bilbao', location: 'Bilbao', rooms_count: 4, status: 'active' },
          { id: 6, name: 'Cines Kaizen Málaga', location: 'Málaga', rooms_count: 5, status: 'maintenance' },
          { id: 7, name: 'Cines Kaizen Zaragoza', location: 'Zaragoza', rooms_count: 3, status: 'active' },
          { id: 8, name: 'Cines Kaizen Murcia', location: 'Murcia', rooms_count: 3, status: 'active' },
          { id: 9, name: 'Cines Kaizen Alicante', location: 'Alicante', rooms_count: 4, status: 'coming_soon' },
          { id: 10, name: 'Cines Kaizen Palma', location: 'Palma de Mallorca', rooms_count: 5, status: 'active' },
          { id: 11, name: 'Cines Kaizen Granada', location: 'Granada', rooms_count: 3, status: 'maintenance' },
          { id: 12, name: 'Cines Kaizen Las Palmas', location: 'Las Palmas', rooms_count: 4, status: 'active' },
        ];
        
        // Filtrar por búsqueda si es necesario
        let filteredCinemas = mockCinemas;
        if (searchQuery) {
          const query = searchQuery.toLowerCase();
          filteredCinemas = mockCinemas.filter(cinema => 
            cinema.name.toLowerCase().includes(query) || 
            cinema.location.toLowerCase().includes(query)
          );
        }
        
        // Ordenar
        filteredCinemas.sort((a, b) => {
          const valueA = a[sortBy];
          const valueB = b[sortBy];
          
          if (typeof valueA === 'string') {
            const comparison = valueA.localeCompare(valueB);
            return sortDirection === 'asc' ? comparison : -comparison;
          } else {
            const comparison = valueA - valueB;
            return sortDirection === 'asc' ? comparison : -comparison;
          }
        });
        
        // Calcular paginación
        totalPages = Math.ceil(filteredCinemas.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        
        cinemas = filteredCinemas.slice(startIndex, endIndex);
        loading = false;
      }, 500);
      
    } catch (e) {
      error = 'Error al cargar los cines';
      loading = false;
    }
  }
  
  function handleSort(column) {
    if (sortBy === column) {
      sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
      sortBy = column;
      sortDirection = 'asc';
    }
    fetchCinemas();
  }
  
  function changePage(page) {
    currentPage = page;
    fetchCinemas();
  }
  
  function search() {
    currentPage = 1;
    fetchCinemas();
  }
  
  function openDeleteModal(cinema) {
    selectedCinema = cinema;
    showDeleteModal = true;
  }
  
  function closeDeleteModal() {
    showDeleteModal = false;
    selectedCinema = null;
  }
  
  async function deleteCinema() {
    if (!selectedCinema) return;
    
    const token = localStorage.getItem('token');
    if (!token) return;
    
    try {
      // En un entorno real, esto sería una llamada a la API
      // await fetch(`${API_URL}/admin/cinemas/${selectedCinema.id}`, {
      //   method: 'DELETE',
      //   headers: {
      //     'Authorization': `Bearer ${token}`,
      //     'Accept': 'application/json'
      //   }
      // });
      
      // Simulamos la eliminación
      cinemas = cinemas.filter(c => c.id !== selectedCinema.id);
      closeDeleteModal();
      
    } catch (e) {
      error = 'Error al eliminar el cine';
    }
  }
  
  function getStatusBadge(status) {
    switch (status) {
      case 'active':
        return 'bg-success';
      case 'maintenance':
        return 'bg-warning';
      case 'coming_soon':
        return 'bg-info';
      default:
        return 'bg-secondary';
    }
  }
  
  function getStatusText(status) {
    switch (status) {
      case 'active':
        return 'Activo';
      case 'maintenance':
        return 'En mantenimiento';
      case 'coming_soon':
        return 'Próximamente';
      default:
        return 'Desconocido';
    }
  }
  
  onMount(() => {
    fetchCinemas();
  });
</script>

<div class="cinema-management">
  <div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h1>Gestión de Cines</h1>
    <a href="/admin/cinemas/new" class="btn btn-primary">
      <i class="bi bi-plus-circle me-2"></i>Nuevo Cine
    </a>
  </div>
  
  <!-- Buscador y filtros -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="input-group">
            <input 
              type="text" 
              class="form-control" 
              placeholder="Buscar por nombre o ubicación..." 
              bind:value={searchQuery}
              on:keyup={(e) => e.key === 'Enter' && search()}
            >
            <button class="btn btn-primary" on:click={search}>
              <i class="bi bi-search me-1"></i>Buscar
            </button>
          </div>
        </div>
        
        <div class="col-md-4 ms-auto">
          <select class="form-select" bind:value={itemsPerPage} on:change={() => { currentPage = 1; fetchCinemas(); }}>
            <option value="5">5 por página</option>
            <option value="10">10 por página</option>
            <option value="20">20 por página</option>
            <option value="50">50 por página</option>
          </select>
        </div>
      </div>
    </div>
  </div>
  
  {#if loading}
    <div class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  {:else if error}
    <div class="alert alert-danger">{error}</div>
  {:else}
    <!-- Tabla de cines -->
    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th style="width: 50px;">#</th>
              <th style="cursor: pointer;" on:click={() => handleSort('name')}>
                Nombre
                {#if sortBy === 'name'}
                  <i class="bi bi-arrow-{sortDirection === 'asc' ? 'up' : 'down'}-short"></i>
                {/if}
              </th>
              <th style="cursor: pointer;" on:click={() => handleSort('location')}>
                Ubicación
                {#if sortBy === 'location'}
                  <i class="bi bi-arrow-{sortDirection === 'asc' ? 'up' : 'down'}-short"></i>
                {/if}
              </th>
              <th style="cursor: pointer;" on:click={() => handleSort('rooms_count')}>
                Salas
                {#if sortBy === 'rooms_count'}
                  <i class="bi bi-arrow-{sortDirection === 'asc' ? 'up' : 'down'}-short"></i>
                {/if}
              </th>
              <th style="cursor: pointer;" on:click={() => handleSort('status')}>
                Estado
                {#if sortBy === 'status'}
                  <i class="bi bi-arrow-{sortDirection === 'asc' ? 'up' : 'down'}-short"></i>
                {/if}
              </th>
              <th style="width: 150px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            {#each cinemas as cinema}
              <tr>
                <td>{cinema.id}</td>
                <td>{cinema.name}</td>
                <td>{cinema.location}</td>
                <td>{cinema.rooms_count} salas</td>
                <td>
                  <span class="badge {getStatusBadge(cinema.status)}">
                    {getStatusText(cinema.status)}
                  </span>
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <a href={`/admin/cinemas/${cinema.id}`} class="btn btn-sm btn-primary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href={`/admin/cinemas/${cinema.id}/edit`} class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-sm btn-danger" on:click={() => openDeleteModal(cinema)}>
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            {:else}
              <tr>
                <td colspan="6" class="text-center py-4">No se encontraron cines</td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
      
      <!-- Paginación -->
      {#if totalPages > 1}
        <div class="card-footer">
          <nav>
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item {currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" on:click={() => changePage(currentPage - 1)} disabled={currentPage === 1}>
                  <i class="bi bi-chevron-left"></i>
                </button>
              </li>
              
              {#each Array(totalPages) as _, i}
                <li class="page-item {currentPage === i + 1 ? 'active' : ''}">
                  <button class="page-link" on:click={() => changePage(i + 1)}>
                    {i + 1}
                  </button>
                </li>
              {/each}
              
              <li class="page-item {currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" on:click={() => changePage(currentPage + 1)} disabled={currentPage === totalPages}>
                  <i class="bi bi-chevron-right"></i>
                </button>
              </li>
            </ul>
          </nav>
        </div>
      {/if}
    </div>
  {/if}
</div>

<!-- Modal de confirmación para eliminar -->
{#if showDeleteModal}
  <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmar eliminación</h5>
          <button type="button" class="btn-close" on:click={closeDeleteModal}></button>
        </div>
        <div class="modal-body">
          <p>¿Estás seguro que deseas eliminar el cine "{selectedCinema?.name}"?</p>
          <p class="text-danger">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" on:click={closeDeleteModal}>Cancelar</button>
          <button type="button" class="btn btn-danger" on:click={deleteCinema}>Eliminar</button>
        </div>
      </div>
    </div>
  </div>
{/if}

<style>
  .table th {
    font-weight: 600;
  }
  
  .table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
  }
  
  .page-link {
    cursor: pointer;
  }
</style> 