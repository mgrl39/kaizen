<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  
  // Para Carbon Components
  import { 
    DataTable,
    Pagination,
    Button,
    SkeletonText,
    Search,
    Toolbar,
    ToolbarContent,
    ToolbarSearch,
    ToolbarMenu,
    ToolbarMenuItem
  } from "carbon-components-svelte";
  
  let movies = [];
  let loading = true;
  let totalItems = 0;
  let firstRowIndex = 0;
  let currentPageSize = 10;
  
  const headers = [
    { key: 'title', value: 'Título' },
    { key: 'duration', value: 'Duración' },
    { key: 'category', value: 'Categoría' },
    { key: 'premiere_date', value: 'Fecha estreno' },
    { key: 'actions', value: 'Acciones' }
  ];
  
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
        // Formatear los datos para la tabla
        movies = data.data ? data.data : data;
        totalItems = movies.length;
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

<div class="movies-container">
  <header class="page-header">
    <div class="bx--grid">
      <div class="bx--row">
        <div class="bx--col">
          <h2 class="bx--type-productive-heading-04">Gestión de Películas</h2>
          <p class="bx--type-body-long-01">Administra el catálogo de películas de los cines</p>
        </div>
      </div>
    </div>
  </header>

  <section class="bx--grid">
    <div class="bx--row">
      <div class="bx--col">
        {#if loading}
          <div style="padding: 2rem 0">
            <SkeletonText paragraph lines={15} />
          </div>
        {:else}
          <DataTable headers={headers} rows={movies.slice(firstRowIndex, firstRowIndex + currentPageSize)}>
            <Toolbar>
              <ToolbarContent>
                <ToolbarSearch persistent placeholder="Buscar películas..." />
                <Button on:click={handleAddMovie}>Añadir Película</Button>
              </ToolbarContent>
            </Toolbar>
            <svelte:fragment slot="cell" let:row let:cell>
              {#if cell.key === 'actions'}
                <div class="table-actions">
                  <Button kind="ghost" size="small" iconDescription="Editar" on:click={() => handleEditMovie(row.id)}>
                    <i class="bi bi-pencil"></i>
                  </Button>
                  <Button kind="ghost" size="small" iconDescription="Eliminar" on:click={() => handleDeleteMovie(row.id)}>
                    <i class="bi bi-trash"></i>
                  </Button>
                </div>
              {:else}
                {cell.value || '-'}
              {/if}
            </svelte:fragment>
          </DataTable>
          
          <Pagination
            bind:pageSize={currentPageSize}
            bind:page={firstRowIndex}
            totalItems={totalItems}
            pageSizes={[5, 10, 20, 30, 40, 50]}
          />
        {/if}
      </div>
    </div>
  </section>
</div>

<style>
  .page-header {
    margin-bottom: 2rem;
  }
  
  .table-actions {
    display: flex;
    gap: 0.5rem;
  }
</style> 