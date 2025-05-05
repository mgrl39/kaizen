<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  
  let movies = [];
  let loading = true;
  
  async function fetchMovies() {
    loading = true;
    try {
      const token = localStorage.getItem('token');
      const response = await fetch(`${API_URL}/movies`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (response.ok) {
        movies = data.data ? data.data : data;
      } else {
        console.error('Error al cargar las películas:', data);
      }
    } catch (error) {
      console.error('Error de conexión:', error);
    } finally {
      loading = false;
    }
  }
  
  function handleAddMovie() {
    // Implementar navegación a formulario de creación
    alert('Función para añadir película');
  }
  
  function handleEditMovie(id) {
    // Implementar edición
    alert(`Editar película ID: ${id}`);
  }
  
  function handleDeleteMovie(id) {
    // Implementar eliminación
    alert(`Eliminar película ID: ${id}`);
  }
  
  onMount(() => {
    fetchMovies();
  });
</script>

<!-- Usa las clases comunes definidas en el layout -->
<div class="admin-page-header">
  <h2>Gestión de Películas</h2>
  <p>Administra el catálogo de películas de los cines</p>
</div>

<div class="card admin-card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <input type="text" class="form-control" placeholder="Buscar películas...">
    </div>
    <button class="btn btn-primary" on:click={handleAddMovie}>
      <i class="bi bi-plus-lg me-1"></i> Añadir Película
    </button>
  </div>
  <div class="card-body p-0">
    {#if loading}
      <div class="p-4 text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>
    {:else if movies.length === 0}
      <div class="p-4 text-center">
        <p class="mb-0">No hay películas disponibles</p>
      </div>
    {:else}
      <div class="table-responsive">
        <table class="table table-hover admin-table mb-0">
          <thead>
            <tr>
              <th>Título</th>
              <th>Duración</th>
              <th>Categoría</th>
              <th>Fecha estreno</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            {#each movies as movie}
              <tr>
                <td>{movie.title}</td>
                <td>{movie.duration} min</td>
                <td>{movie.category || '-'}</td>
                <td>{movie.premiere_date || '-'}</td>
                <td>
                  <div class="admin-actions">
                    <button class="btn btn-sm btn-outline-primary" on:click={() => handleEditMovie(movie.id)}>
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" on:click={() => handleDeleteMovie(movie.id)}>
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    {/if}
  </div>
  <div class="card-footer">
    <nav>
      <ul class="pagination mb-0 justify-content-center">
        <li class="page-item disabled">
          <a class="page-link" href="#" tabindex="-1">Anterior</a>
        </li>
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item">
          <a class="page-link" href="#">Siguiente</a>
        </li>
      </ul>
    </nav>
  </div>
</div> 