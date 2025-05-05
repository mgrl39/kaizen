<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  
  // Definir interfaz para User
  interface User {
    id: number;
    username: string;
    name?: string;
    email: string;
    role?: string;
  }
  
  // Añadir tipos a las variables
  let users: User[] = [];
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
  
  function handleAddUser(): void {
    alert('Función para añadir usuario');
  }
  
  function handleEditUser(id: number): void {
    alert(`Editar usuario ID: ${id}`);
  }
  
  function handleDeleteUser(id: number): void {
    alert(`Eliminar usuario ID: ${id}`);
  }
  
  function getRoleBadgeClass(role: string | undefined): string {
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
                    <button class="btn btn-sm btn-outline-primary" on:click={() => handleEditUser(user.id)} aria-label="Editar usuario">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" on:click={() => handleDeleteUser(user.id)} aria-label="Eliminar usuario">
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

<style>
  .admin-actions {
    display: flex;
    gap: 0.5rem;
  }
</style> 