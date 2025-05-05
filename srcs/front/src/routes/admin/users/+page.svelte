<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  
  // Para Carbon Components
  import { 
    DataTable,
    Pagination,
    Button,
    SkeletonText,
    Tag,
    Toolbar,
    ToolbarContent,
    ToolbarSearch
  } from "carbon-components-svelte";
  
  let users = [];
  let loading = true;
  let totalItems = 0;
  let firstRowIndex = 0;
  let currentPageSize = 10;
  
  const headers = [
    { key: 'username', value: 'Usuario' },
    { key: 'name', value: 'Nombre' },
    { key: 'email', value: 'Email' },
    { key: 'role', value: 'Rol' },
    { key: 'actions', value: 'Acciones' }
  ];
  
  async function fetchUsers() {
    loading = true;
    try {
      const token = localStorage.getItem('token');
      const response = await fetch(`${API_URL}/users`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (response.ok) {
        // Formatear los datos para la tabla
        users = data.data ? data.data : data;
        totalItems = users.length;
      } else {
        console.error('Error al cargar los usuarios:', data);
      }
    } catch (error) {
      console.error('Error de conexión:', error);
    } finally {
      loading = false;
    }
  }
  
  function handleAddUser() {
    // Implementar navegación a formulario de creación
    alert('Función para añadir usuario');
  }
  
  function handleEditUser(id) {
    // Implementar edición
    alert(`Editar usuario ID: ${id}`);
  }
  
  function handleDeleteUser(id) {
    // Implementar eliminación
    alert(`Eliminar usuario ID: ${id}`);
  }
  
  function getRoleColor(role) {
    switch(role) {
      case 'admin':
        return 'red';
      case 'staff':
        return 'blue';
      default:
        return 'green';
    }
  }
  
  onMount(() => {
    fetchUsers();
  });
</script>

<div class="users-container">
  <header class="page-header">
    <div class="bx--grid">
      <div class="bx--row">
        <div class="bx--col">
          <h2 class="bx--type-productive-heading-04">Gestión de Usuarios</h2>
          <p class="bx--type-body-long-01">Administra los usuarios de la plataforma</p>
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
          <DataTable headers={headers} rows={users.slice(firstRowIndex, firstRowIndex + currentPageSize)}>
            <Toolbar>
              <ToolbarContent>
                <ToolbarSearch persistent placeholder="Buscar usuarios..." />
                <Button on:click={handleAddUser}>Añadir Usuario</Button>
              </ToolbarContent>
            </Toolbar>
            <svelte:fragment slot="cell" let:row let:cell>
              {#if cell.key === 'role'}
                <Tag type={getRoleColor(cell.value)}>{cell.value || 'user'}</Tag>
              {:else if cell.key === 'actions'}
                <div class="table-actions">
                  <Button kind="ghost" size="small" iconDescription="Editar" on:click={() => handleEditUser(row.id)}>
                    <i class="bi bi-pencil"></i>
                  </Button>
                  <Button kind="ghost" size="small" iconDescription="Eliminar" on:click={() => handleDeleteUser(row.id)}>
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