<script lang="ts">
  import { onMount } from 'svelte';
  
  // Define user type
  interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    created_at: string;
    status: string;
  }
  
  // Estado para los usuarios
  let users: User[] = [];
  let loading = true;
  let error: string | null = null;
  
  // Estado para paginación
  let currentPage = 1;
  let totalPages = 1;
  let totalUsers = 0;
  
  // Estado para búsqueda y filtros
  let searchQuery = '';
  let filterRole = 'all';
  
  // Función para formatear fecha de API (si viene en formato ISO)
  function formatDate(dateString: string): string {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString();
  }
  
  // Filtrar usuarios según la búsqueda y filtros seleccionados
  $: filteredUsers = users.filter(user => {
    // Filtrar por búsqueda
    const matchesSearch = searchQuery === '' || 
      user.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.toLowerCase());
    
    // Filtrar por rol
    const matchesRole = filterRole === 'all' || user.role === filterRole;
    
    return matchesSearch && matchesRole;
  });
  
  // Calcular estadísticas
  $: totalAdmins = users.filter(user => user.role === 'admin').length;
  $: totalManagers = users.filter(user => user.role === 'manager').length;
  $: activeUsers = users.filter(user => user.status === 'active').length;
  
  // Obtener la función de clase para el rol
  function getRoleBadgeClass(role: string): string {
    switch (role) {
      case 'admin': return 'bg-danger';
      case 'manager': return 'bg-warning';
      default: return 'bg-info';
    }
  }
  
  // Obtener la función de clase para el estado
  function getStatusBadgeClass(status: string): string {
    switch (status) {
      case 'active': return 'bg-success';
      case 'inactive': return 'bg-secondary';
      default: return 'bg-warning';
    }
  }
  
  // Cargar usuarios desde la API
  async function loadUsers(page = 1) {
    loading = true;
    error = null;
    
    try {
      const response = await fetch(`/api/v1/users?page=${page}`);
      
      if (!response.ok) {
        throw new Error(`Error ${response.status}: ${response.statusText}`);
      }
      
      const result = await response.json();
      
      if (result.success) {
        users = result.data.data; // Accessing paginated data
        currentPage = result.data.current_page;
        totalPages = result.data.last_page;
        totalUsers = result.data.total;
      } else {
        throw new Error(result.message || 'Error al cargar usuarios');
      }
    } catch (err: unknown) {
      console.error('Error al cargar usuarios:', err);
      error = err instanceof Error ? err.message : 'Error desconocido';
      users = [];
    } finally {
      loading = false;
    }
  }
  
  // Eliminar usuario
  async function deleteUser(userId: number) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
      try {
        const response = await fetch(`/api/v1/users/${userId}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          credentials: 'include' // Para enviar cookies de autenticación
        });
        
        if (!response.ok) {
          throw new Error(`Error ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
          // Actualizar lista de usuarios
          loadUsers(currentPage);
        } else {
          throw new Error(result.message || 'Error al eliminar usuario');
        }
      } catch (err: unknown) {
        console.error('Error al eliminar usuario:', err);
        alert(err instanceof Error ? err.message : 'Error desconocido');
      }
    }
  }
  
  // Cambiar de página
  function goToPage(page: number) {
    if (page !== currentPage && page > 0 && page <= totalPages) {
      loadUsers(page);
    }
  }
  
  // Cargar datos al montar el componente
  onMount(() => {
    loadUsers();
  });
</script>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Usuarios</h1>
    <a href="/admin/users/add" class="btn btn-primary">
      <i class="bi bi-person-plus me-2"></i>
      Añadir Usuario
    </a>
  </div>
  
  <!-- Dashboard Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card border-primary h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-people text-primary fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">Total Usuarios</h6>
              <h2 class="card-title mb-0">{totalUsers}</h2>
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
              <i class="bi bi-person-check text-success fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">Usuarios Activos</h6>
              <h2 class="card-title mb-0">{activeUsers}</h2>
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
              <i class="bi bi-person-fill-lock text-danger fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">Administradores</h6>
              <h2 class="card-title mb-0">{totalAdmins}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Filters and Search -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-8">
          <div class="input-group">
            <span class="input-group-text bg-light">
              <i class="bi bi-search"></i>
            </span>
            <input 
              type="text" 
              class="form-control"
              placeholder="Buscar usuarios"
              bind:value={searchQuery}
            />
            {#if searchQuery}
              <button class="btn btn-outline-secondary" on:click={() => searchQuery = ''}>
                <i class="bi bi-x"></i>
              </button>
            {/if}
          </div>
        </div>
        
        <div class="col-md-4">
          <select class="form-select" bind:value={filterRole}>
            <option value="all">Todos los roles</option>
            <option value="admin">Administrador</option>
            <option value="manager">Gestor</option>
            <option value="user">Usuario</option>
          </select>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Users Table -->
  <div class="card">
    <div class="card-body p-0">
      {#if loading}
        <div class="d-flex justify-content-center align-items-center py-5">
          <div class="spinner-border text-primary me-3" role="status">
            <span class="visually-hidden">Cargando</span>
          </div>
          <span class="fs-5">Cargando usuarios...</span>
        </div>
      {:else if error}
        <div class="alert alert-danger m-3">
          <p>Error al cargar usuarios: {error}</p>
          <button 
            class="btn btn-sm btn-primary mt-2"
            on:click={() => loadUsers()}
          >
            Reintentar
          </button>
        </div>
      {:else if filteredUsers.length === 0}
        <div class="text-center py-5">
          <i class="bi bi-exclamation-circle text-secondary fs-1"></i>
          <p class="mt-3">No se encontraron usuarios</p>
        </div>
      {:else}
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Registrado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {#each filteredUsers as user}
                <tr>
                  <td class="fw-medium">{user.name}</td>
                  <td>{user.email}</td>
                  <td>
                    <span class="badge {getRoleBadgeClass(user.role)}">
                      {user.role}
                    </span>
                  </td>
                  <td>
                    <span class="badge {getStatusBadgeClass(user.status)}">
                      {user.status}
                    </span>
                  </td>
                  <td>{formatDate(user.created_at)}</td>
                  <td class="text-end">
                    <div class="btn-group btn-group-sm">
                      <a href={`/admin/users/${user.id}`} class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <button 
                        class="btn btn-outline-danger"
                        on:click={() => deleteUser(user.id)}
                      >
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        </div>
        
        <!-- Paginación -->
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
          <div class="text-muted small">
            Mostrando {filteredUsers.length} de {totalUsers} usuarios
          </div>
          {#if totalPages > 1}
            <nav aria-label="Page navigation">
              <ul class="pagination mb-0">
                <li class="page-item {currentPage === 1 ? 'disabled' : ''}">
                  <button 
                    class="page-link" 
                    on:click={() => goToPage(currentPage - 1)}
                    aria-label="Previous"
                  >
                    <span aria-hidden="true">&laquo;</span>
                  </button>
                </li>
                
                {#each Array(totalPages) as _, i}
                  <li class="page-item {currentPage === i + 1 ? 'active' : ''}">
                    <button 
                      class="page-link"
                      on:click={() => goToPage(i + 1)}
                    >
                      {i + 1}
                    </button>
                  </li>
                {/each}
                
                <li class="page-item {currentPage === totalPages ? 'disabled' : ''}">
                  <button 
                    class="page-link"
                    on:click={() => goToPage(currentPage + 1)}
                    aria-label="Next"
                  >
                    <span aria-hidden="true">&raquo;</span>
                  </button>
                </li>
              </ul>
            </nav>
          {/if}
        </div>
      {/if}
    </div>
  </div>
</div> 