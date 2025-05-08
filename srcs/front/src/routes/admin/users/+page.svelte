<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  
  // Estado para los usuarios
  let users = [];
  let loading = true;
  let error = null;
  
  // Estado para paginación
  let currentPage = 1;
  let totalPages = 1;
  let totalUsers = 0;
  
  // Función para formatear fecha de API (si viene en formato ISO)
  function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString();
  }
  
  // Cargar usuarios desde la API
  async function loadUsers(page = 1) {
    loading = true;
    error = null;
    
    try {
      const response = await fetch(`/api/v1/users?page=${page}`);
      if (!response.ok) {
        throw new Error(`Error: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data.success && data.data) {
        users = data.data.data || [];
        currentPage = data.data.current_page || 1;
        totalPages = data.data.last_page || 1;
        totalUsers = data.data.total || 0;
      } else {
        throw new Error('Formato de respuesta inválido');
      }
    } catch (err) {
      console.error('Error al cargar usuarios:', err);
      error = err.message;
      users = [];
    } finally {
      loading = false;
    }
  }
  
  // Eliminar usuario
  async function deleteUser(userId) {
    if (confirm($t('confirmDeleteUser'))) {
      try {
        const response = await fetch(`/api/v1/users/${userId}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
          }
        });
        
        if (response.ok) {
          // Recargar la lista después de eliminar
          await loadUsers(currentPage);
        } else {
          throw new Error(`Error: ${response.status}`);
        }
      } catch (err) {
        console.error('Error al eliminar usuario:', err);
        alert($t('errorDeletingUser'));
      }
    }
  }
  
  // Cambiar de página
  function goToPage(page) {
    if (page !== currentPage && page > 0 && page <= totalPages) {
      loadUsers(page);
    }
  }
  
  // Cargar datos al montar el componente
  onMount(() => {
    loadUsers();
  });
</script>

<div>
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">{$t('users')}</h1>
    <a href="/admin/users/add" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
      {$t('addUser')}
    </a>
  </div>
  
  <!-- Búsqueda simple -->
  <div class="bg-white p-4 rounded-lg shadow mb-6">
    <input 
      type="text" 
      placeholder={$t('searchUsers')}
      class="w-full p-2 border border-gray-300 rounded"
    />
  </div>
  
  <!-- Tabla de usuarios -->
  <div class="bg-white rounded-lg shadow overflow-x-auto">
    {#if loading}
      <div class="p-6 text-center">
        <p>{$t('loading')}...</p>
      </div>
    {:else if error}
      <div class="p-6 text-center text-red-500">
        <p>{$t('errorLoadingUsers')}: {error}</p>
        <button 
          class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded"
          on:click={() => loadUsers()}
        >
          {$t('retry')}
        </button>
      </div>
    {:else if users.length === 0}
      <div class="p-6 text-center">
        <p>{$t('noUsersFound')}</p>
      </div>
    {:else}
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {$t('name')}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {$t('email')}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {$t('role')}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {$t('registered')}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              {$t('actions')}
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          {#each users as user}
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{user.name}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{user.email}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'}`}>
                  {user.role || 'user'}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{formatDate(user.created_at)}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href={`/admin/users/${user.id}`} class="text-blue-500 hover:text-blue-700 mr-3">
                  {$t('edit')}
                </a>
                <button 
                  class="text-red-500 hover:text-red-700"
                  on:click={() => deleteUser(user.id)}
                >
                  {$t('delete')}
                </button>
              </td>
            </tr>
          {/each}
        </tbody>
      </table>
      
      <!-- Paginación -->
      {#if totalPages > 1}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
          <div class="text-sm text-gray-700">
            {$t('showing')} {(currentPage - 1) * 15 + 1} {$t('to')} {Math.min(currentPage * 15, totalUsers)} {$t('of')} {totalUsers} {$t('users')}
          </div>
          <div class="flex space-x-1">
            <button 
              class="px-3 py-1 border border-gray-300 rounded-md text-sm {currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}"
              disabled={currentPage === 1}
              on:click={() => goToPage(currentPage - 1)}
            >
              {$t('previous')}
            </button>
            
            {#if totalPages <= 5}
              {#each Array(totalPages) as _, i}
                <button 
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm {currentPage === i + 1 ? 'bg-blue-50 text-blue-600 border-blue-500' : 'hover:bg-gray-50'}"
                  on:click={() => goToPage(i + 1)}
                >
                  {i + 1}
                </button>
              {/each}
            {:else}
              <!-- Lógica simplificada de paginación para muchas páginas -->
              {#if currentPage > 1}
                <button 
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50"
                  on:click={() => goToPage(1)}
                >
                  1
                </button>
              {/if}
              
              {#if currentPage > 3}
                <span class="px-3 py-1">...</span>
              {/if}
              
              {#if currentPage > 2}
                <button 
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50"
                  on:click={() => goToPage(currentPage - 1)}
                >
                  {currentPage - 1}
                </button>
              {/if}
              
              <button 
                class="px-3 py-1 border border-blue-500 rounded-md text-sm bg-blue-50 text-blue-600"
              >
                {currentPage}
              </button>
              
              {#if currentPage < totalPages - 1}
                <button 
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50"
                  on:click={() => goToPage(currentPage + 1)}
                >
                  {currentPage + 1}
                </button>
              {/if}
              
              {#if currentPage < totalPages - 2}
                <span class="px-3 py-1">...</span>
              {/if}
              
              {#if currentPage < totalPages}
                <button 
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50"
                  on:click={() => goToPage(totalPages)}
                >
                  {totalPages}
                </button>
              {/if}
            {/if}
            
            <button 
              class="px-3 py-1 border border-gray-300 rounded-md text-sm {currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}"
              disabled={currentPage === totalPages}
              on:click={() => goToPage(currentPage + 1)}
            >
              {$t('next')}
            </button>
          </div>
        </div>
      {/if}
    {/if}
  </div>
</div> 