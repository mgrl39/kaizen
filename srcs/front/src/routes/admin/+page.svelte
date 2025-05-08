<script lang="ts">
  import { t } from '$lib/i18n';
  
  // Datos del dashboard (actualizados para reflejar un solo cine)
  const stats = [
    { 
      name: 'totalMovies', 
      value: '125', 
      path: '/admin/movies',
      icon: 'M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z',
      color: 'indigo'
    },
    { 
      name: 'totalScreens', 
      value: '8', 
      path: '/admin/cinemas',
      icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
      color: 'emerald'
    },
    { 
      name: 'totalUsers', 
      value: '1,240', 
      path: '/admin/users',
      icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
      color: 'violet'
    },
    { 
      name: 'totalBookings', 
      value: '8,564', 
      path: '/admin/bookings',
      icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
      color: 'amber'
    }
  ];
  
  // Lista básica de actividades recientes
  const recentActivities = [
    { id: 1, description: 'Nueva película añadida: "Dune: Part Two"', date: '15/07/2023' },
    { id: 2, description: 'Actualización del cine: "Cineplex Central"', date: '14/07/2023' },
    { id: 3, description: 'Nuevo usuario registrado: maria.garcia@example.com', date: '13/07/2023' }
  ];
</script>

<div>
  <h1 class="text-2xl font-bold text-gray-900 mb-6">{$t('dashboard')}</h1>
  
  <!-- Tarjetas de estadísticas -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    {#each stats as stat}
      <a href={stat.path} class="bg-white overflow-hidden rounded-xl shadow hover:shadow-md transition-shadow">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-{stat.color}-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d={stat.icon} />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">{$t(stat.name)}</dt>
                <dd class="text-2xl font-semibold text-gray-900">{stat.value}</dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3 border-t border-gray-200">
          <div class="text-sm">
            <span class="font-medium text-{stat.color}-600 hover:text-{stat.color}-500">
              {$t('viewDetails')} →
            </span>
          </div>
        </div>
      </a>
    {/each}
  </div>
  
  <!-- Contenido principal en dos columnas -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <!-- Actividad reciente -->
    <div class="lg:col-span-2">
      <div class="bg-white rounded-xl shadow mb-5">
        <div class="px-5 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">{$t('recentActivity')}</h2>
        </div>
        <div class="divide-y divide-gray-200">
          {#each recentActivities as activity}
            <div class="p-5 hover:bg-gray-50 transition-colors">
              <div class="flex justify-between">
                <div>
                  <p class="text-sm text-gray-900">{activity.description}</p>
                  <p class="text-xs text-gray-500 mt-1">{activity.date}</p>
                </div>
                <button class="text-gray-400 hover:text-gray-500">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                  </svg>
                </button>
              </div>
            </div>
          {/each}
        </div>
        {#if recentActivities.length > 0}
          <div class="bg-gray-50 px-5 py-3 border-t border-gray-200 text-right">
            <a href="/admin/activity" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
              {$t('viewAllActivity')} →
            </a>
          </div>
        {/if}
      </div>
    </div>
    
    <!-- Acciones rápidas -->
    <div class="lg:col-span-1">
      <div class="bg-white rounded-xl shadow mb-5">
        <div class="px-5 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">{$t('quickActions')}</h2>
        </div>
        <div class="p-5 space-y-3">
          <a href="/admin/movies/add" class="flex items-center justify-center w-full py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {$t('addMovie')}
          </a>
          <a href="/admin/cinemas/add" class="flex items-center justify-center w-full py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {$t('addCinema')}
          </a>
          <a href="/admin/users/add" class="flex items-center justify-center w-full py-2.5 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {$t('addUser')}
          </a>
        </div>
      </div>
      
      <!-- Próximos estrenos -->
      <div class="bg-white rounded-xl shadow">
        <div class="px-5 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">{$t('upcomingPremieres')}</h2>
        </div>
        <div class="p-5 space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-900">Dune: Part Two</span>
            <span class="text-xs text-gray-500">15/09/2023</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-900">The Batman 2</span>
            <span class="text-xs text-gray-500">22/10/2023</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-900">Avatar 3</span>
            <span class="text-xs text-gray-500">18/12/2023</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> 