<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  
  // Datos para el panel de reportes
  let salesData = {
    daily: [1200, 980, 1400, 1150, 1700, 1950, 2100],
    monthly: [28000, 32000, 35000, 30000, 42000, 48000],
    yearly: [320000, 380000, 420000, 480000, 520000]
  };
  
  let moviesData = [
    { name: "Dune: Part Two", tickets: 1245, revenue: 14940 },
    { name: "The Batman", tickets: 980, revenue: 11760 },
    { name: "Interstellar", tickets: 750, revenue: 9000 },
    { name: "Avatar 3", tickets: 620, revenue: 7440 },
    { name: "Inception", tickets: 580, revenue: 6960 }
  ];
  
  let screenUtilization = [
    { screen: 1, utilization: 87, type: "IMAX" },
    { screen: 2, utilization: 78, type: "3D" },
    { screen: 3, utilization: 92, type: "Standard" },
    { screen: 4, utilization: 65, type: "Standard" },
    { screen: 5, utilization: 73, type: "VIP" },
    { screen: 6, utilization: 81, type: "Standard" },
    { screen: 7, utilization: 62, type: "Standard" },
    { screen: 8, utilization: 76, type: "3D" }
  ];
  
  // Estado para filtros de reportes
  let timeRange = 'weekly';
  let isLoading = true;
  
  // Fechas para el filtro
  const currentDate = new Date();
  let startDate = new Date(currentDate);
  startDate.setDate(currentDate.getDate() - 7);
  let endDate = new Date(currentDate);
  
  // Formato de fecha para mostrar
  function formatDate(date) {
    return date.toISOString().split('T')[0];
  }
  
  // Actualizar rango de fechas según el filtro seleccionado
  function updateDateRange(range) {
    timeRange = range;
    endDate = new Date();
    
    if (range === 'daily') {
      startDate = new Date(endDate);
      startDate.setDate(endDate.getDate() - 1);
    } else if (range === 'weekly') {
      startDate = new Date(endDate);
      startDate.setDate(endDate.getDate() - 7);
    } else if (range === 'monthly') {
      startDate = new Date(endDate);
      startDate.setMonth(endDate.getMonth() - 1);
    } else if (range === 'yearly') {
      startDate = new Date(endDate);
      startDate.setFullYear(endDate.getFullYear() - 1);
    }
  }
  
  // Función para obtener informes
  function fetchReports() {
    isLoading = true;
    
    // En un entorno real, aquí harías una llamada a la API
    // Simulamos una carga con un pequeño retraso
    setTimeout(() => {
      isLoading = false;
    }, 800);
  }
  
  // Al cambiar el rango de tiempo, actualizamos los informes
  $: if (timeRange) {
    updateDateRange(timeRange);
    fetchReports();
  }
  
  // Calcular totales
  $: totalTickets = moviesData.reduce((sum, movie) => sum + movie.tickets, 0);
  $: totalRevenue = moviesData.reduce((sum, movie) => sum + movie.revenue, 0);
  $: averageUtilization = Math.round(screenUtilization.reduce((sum, screen) => sum + screen.utilization, 0) / screenUtilization.length);
  
  // Simular carga inicial
  onMount(() => {
    fetchReports();
  });
  
  // Función para obtener la clase de color según el porcentaje de utilización
  function getUtilizationColorClass(percent) {
    if (percent >= 85) return 'bg-green-500';
    if (percent >= 70) return 'bg-blue-500';
    if (percent >= 50) return 'bg-yellow-500';
    return 'bg-red-500';
  }
</script>

<div>
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">{$t('reports')}</h1>
    <div class="flex space-x-2">
      <button 
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        on:click={() => window.print()}
      >
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        {$t('printReport')}
      </button>
      <button 
        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        {$t('exportPDF')}
      </button>
    </div>
  </div>
  
  <!-- Filtros -->
  <div class="bg-white p-5 rounded-xl shadow mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label for="time-range" class="block text-sm font-medium text-gray-700">{$t('timeRange')}</label>
        <select 
          id="time-range" 
          name="time-range" 
          bind:value={timeRange}
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
        >
          <option value="daily">{$t('daily')}</option>
          <option value="weekly">{$t('weekly')}</option>
          <option value="monthly">{$t('monthly')}</option>
          <option value="yearly">{$t('yearly')}</option>
        </select>
      </div>
      
      <div>
        <label for="start-date" class="block text-sm font-medium text-gray-700">{$t('startDate')}</label>
        <input 
          type="date" 
          id="start-date" 
          name="start-date" 
          value={formatDate(startDate)}
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          disabled
        />
      </div>
      
      <div>
        <label for="end-date" class="block text-sm font-medium text-gray-700">{$t('endDate')}</label>
        <input 
          type="date" 
          id="end-date" 
          name="end-date" 
          value={formatDate(endDate)}
          class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
          disabled
        />
      </div>
    </div>
  </div>
  
  {#if isLoading}
    <div class="flex justify-center items-center py-20">
      <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span class="text-lg text-gray-500">{$t('loadingReports')}</span>
    </div>
  {:else}
    <!-- Tarjetas de resumen -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">{$t('totalTickets')}</dt>
                <dd class="text-2xl font-semibold text-gray-900">{totalTickets}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">{$t('totalRevenue')}</dt>
                <dd class="text-2xl font-semibold text-gray-900">${totalRevenue}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">{$t('averageOccupancy')}</dt>
                <dd class="text-2xl font-semibold text-gray-900">{averageUtilization}%</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Reportes detallados -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Top películas -->
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">{$t('topMovies')}</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {$t('movie')}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {$t('tickets')}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {$t('revenue')}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              {#each moviesData as movie}
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {movie.name}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {movie.tickets}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${movie.revenue}
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Utilización de salas -->
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">{$t('screenUtilization')}</h3>
        </div>
        <div class="p-6">
          <ul class="space-y-4">
            {#each screenUtilization as screen}
              <li>
                <div class="flex items-center justify-between mb-1">
                  <span class="text-sm font-medium text-gray-700">
                    {$t('screen')} {screen.screen} ({screen.type})
                  </span>
                  <span class="text-sm font-medium text-gray-700">
                    {screen.utilization}%
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div class={`h-2.5 rounded-full ${getUtilizationColorClass(screen.utilization)}`} style={`width: ${screen.utilization}%`}></div>
                </div>
              </li>
            {/each}
          </ul>
        </div>
      </div>
      
      <!-- Ventas por período -->
      <div class="bg-white rounded-xl shadow overflow-hidden lg:col-span-2">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">{$t('salesOverTime')}</h3>
        </div>
        <div class="p-6 h-80 flex items-center justify-center">
          <!-- Aquí iría un gráfico real, pero como es un ejemplo usamos un placeholder -->
          <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">
              {$t('salesChartPlaceholder')}
            </p>
            <p class="text-sm text-gray-500">
              ({$t('timeRange')}: {$t(timeRange)}, {formatDate(startDate)} - {formatDate(endDate)})
            </p>
          </div>
        </div>
      </div>
    </div>
  {/if}
</div> 