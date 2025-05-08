<script lang="ts">
  import { t } from '$lib/i18n';
  
  // Usuarios de ejemplo (en un entorno real vendrían de una API)
  const users = [
    { id: 1, name: 'John Doe', email: 'john.doe@example.com', role: 'user', registered: '01/05/2023' },
    { id: 2, name: 'Jane Smith', email: 'jane.smith@example.com', role: 'admin', registered: '15/03/2023' },
    { id: 3, name: 'Bob Johnson', email: 'bob.johnson@example.com', role: 'user', registered: '22/06/2023' },
    { id: 4, name: 'Maria Garcia', email: 'maria.garcia@example.com', role: 'user', registered: '13/07/2023' },
  ];
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
                {user.role}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-500">{user.registered}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <a href={`/admin/users/${user.id}`} class="text-blue-500 hover:text-blue-700 mr-3">
                {$t('edit')}
              </a>
              <button class="text-red-500 hover:text-red-700">
                {$t('delete')}
              </button>
            </td>
          </tr>
        {/each}
      </tbody>
    </table>
  </div>
</div> 