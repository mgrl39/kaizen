<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  
  let users = [];
  let loading = true;
  
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
        users = data.data ? data.data : data;
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
    alert('Función para añadir usuario');
  }
  
  function handleEditUser(id) {
    alert(`Editar usuario ID: ${id}`);
  }
  
  function handleDeleteUser(id) {
    alert(`Eliminar usuario ID: ${id}`);
  }
  
  function getRoleBadgeClass(role) {
    switch(role) {
      case 'admin':
        return 'bg-danger';
      case 'staff':
        return 'bg-info';
      default:
        return 'bg-success';
    }
  }
  
  onMount(() => {
    fetchUsers();
  });
</script>

<div class="admin-page-header">
  <h2>Gestión de Usuarios</h2>
  <p>Administra los usuarios de la plataforma</p>
</div>

<div class="card admin-card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <input type="text" class="form-control" placeholder="Buscar usuarios...">
    </div>
    <button class="btn btn-primary" on:click={handleAddUser}>
      <i class="bi bi-plus-lg me-1"></i> Añadir Usuario
    </button>
  </div>
  <div class="card-body p-0">
    {#if loading}
      <div class="p-4 text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>
      </div>
    {:else if users.length === 0}
      <div class="p-4 text-center">
        <p class="mb-0">No hay usuarios disponibles</p>
      </div>
    {:else}
      <div class="table-responsive">
        <table class="table table-hover admin-table mb-0">
          <thead>
            <tr>
              <th>Usuario</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            {#each users as user}
              <tr>
                <td>{user.username}</td>
                <td>{user.name || '-'}</td>
                <td>{user.email}</td>
                <td>
                  <span class="badge {getRoleBadgeClass(user.role)}">{user.role || 'user'}</span>
                </td>
                <td>
                  <div class="admin-actions">
                    <button class="btn btn-sm btn-outline-primary" on:click={() => handleEditUser(user.id)}>
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" on:click={() => handleDeleteUser(user.id)}>
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