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
  function formatDate(date: Date): string {
    return date.toISOString().split('T')[0];
  }
  
  // Actualizar rango de fechas según el filtro seleccionado
  function updateDateRange(range: string): void {
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
  function fetchReports(): void {
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
  function getUtilizationBadgeClass(percent: number): string {
    if (percent >= 85) return 'bg-success';
    if (percent >= 70) return 'bg-info';
    if (percent >= 50) return 'bg-warning';
    return 'bg-danger';
  }
  
  function getUtilizationBarClass(percent: number): string {
    if (percent >= 85) return 'bg-success';
    if (percent >= 70) return 'bg-info';
    if (percent >= 50) return 'bg-warning';
    return 'bg-danger';
  }
</script>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">{$t('reports')}</h1>
    <div class="btn-group">
      <button class="btn btn-primary" on:click={() => window.print()}>
        <i class="bi bi-printer me-2"></i>
        {$t('printReport')}
      </button>
      <button class="btn btn-outline-primary">
        <i class="bi bi-file-earmark-pdf me-2"></i>
        {$t('exportPDF')}
      </button>
    </div>
  </div>
  
  <!-- Filtros -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label for="time-range" class="form-label">{$t('timeRange')}</label>
          <select 
            id="time-range" 
            class="form-select"
            bind:value={timeRange}
          >
            <option value="daily">{$t('daily')}</option>
            <option value="weekly">{$t('weekly')}</option>
            <option value="monthly">{$t('monthly')}</option>
            <option value="yearly">{$t('yearly')}</option>
          </select>
        </div>
        
        <div class="col-md-4">
          <label for="start-date" class="form-label">{$t('startDate')}</label>
          <input 
            type="date" 
            id="start-date" 
            class="form-control"
            value={formatDate(startDate)}
            disabled
          />
        </div>
        
        <div class="col-md-4">
          <label for="end-date" class="form-label">{$t('endDate')}</label>
          <input 
            type="date" 
            id="end-date" 
            class="form-control"
            value={formatDate(endDate)}
            disabled
          />
        </div>
      </div>
    </div>
  </div>
  
  {#if isLoading}
    <div class="d-flex justify-content-center align-items-center py-5">
      <div class="spinner-border text-primary me-3" role="status">
        <span class="visually-hidden">{$t('loading')}</span>
      </div>
      <span class="fs-5">{$t('loadingReports')}</span>
    </div>
  {:else}
    <!-- Dashboard Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="card border-primary h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="bg-light p-3 rounded me-3">
                <i class="bi bi-ticket-perforated text-primary fs-4"></i>
              </div>
              <div>
                <h6 class="card-subtitle mb-1 text-muted">{$t('totalTickets')}</h6>
                <h2 class="card-title mb-0">{totalTickets}</h2>
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
                <i class="bi bi-currency-dollar text-success fs-4"></i>
              </div>
              <div>
                <h6 class="card-subtitle mb-1 text-muted">{$t('totalRevenue')}</h6>
                <h2 class="card-title mb-0">${totalRevenue}</h2>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card border-info h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="bg-light p-3 rounded me-3">
                <i class="bi bi-graph-up text-info fs-4"></i>
              </div>
              <div>
                <h6 class="card-subtitle mb-1 text-muted">{$t('averageOccupancy')}</h6>
                <h2 class="card-title mb-0">{averageUtilization}%</h2>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Reportes detallados -->
    <div class="row g-4">
      <!-- Top películas -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header bg-white">
            <h2 class="h5 mb-0">{$t('topMovies')}</h2>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>{$t('movie')}</th>
                    <th>{$t('tickets')}</th>
                    <th>{$t('revenue')}</th>
                    <th class="text-end">{$t('share')}</th>
                  </tr>
                </thead>
                <tbody>
                  {#each moviesData as movie}
                    <tr>
                      <td class="fw-medium">{movie.name}</td>
                      <td>{movie.tickets}</td>
                      <td>${movie.revenue}</td>
                      <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end">
                          <span class="me-2">{(movie.tickets / totalTickets * 100).toFixed(1)}%</span>
                          <div class="progress" style="width: 60px; height: 6px;">
                            <div 
                              class="progress-bar bg-primary" 
                              role="progressbar" 
                              style="width: {movie.tickets / totalTickets * 100}%" 
                              aria-valuenow="{movie.tickets / totalTickets * 100}" 
                              aria-valuemin="0" 
                              aria-valuemax="100">
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                  {/each}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Ocupación de salas -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header bg-white">
            <h2 class="h5 mb-0">{$t('screenUtilization')}</h2>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>{$t('screen')}</th>
                    <th>{$t('type')}</th>
                    <th>{$t('utilization')}</th>
                    <th>{$t('status')}</th>
                  </tr>
                </thead>
                <tbody>
                  {#each screenUtilization as screen}
                    <tr>
                      <td class="fw-medium">{$t('screen')} {screen.screen}</td>
                      <td>{screen.type}</td>
                      <td>
                        <div class="progress" style="height: 10px;">
                          <div 
                            class="progress-bar {getUtilizationBarClass(screen.utilization)}" 
                            role="progressbar" 
                            style="width: {screen.utilization}%" 
                            aria-valuenow="{screen.utilization}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="badge {getUtilizationBadgeClass(screen.utilization)}">
                          {screen.utilization}%
                        </span>
                      </td>
                    </tr>
                  {/each}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Gráfico de ventas -->
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-white d-flex justify-content-between">
            <h2 class="h5 mb-0">{$t('salesOverTime')}</h2>
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-secondary active">D</button>
              <button class="btn btn-outline-secondary">W</button>
              <button class="btn btn-outline-secondary">M</button>
              <button class="btn btn-outline-secondary">Y</button>
            </div>
          </div>
          <div class="card-body">
            <div class="sales-chart-placeholder bg-light d-flex justify-content-center align-items-center p-5 rounded">
              <p class="text-center text-muted">
                <i class="bi bi-bar-chart fs-1 d-block mb-2"></i>
                <span>Sales chart would be rendered here with a real chart library</span>
              </p>
            </div>
            <div class="row mt-4">
              <div class="col-md-4">
                <div class="d-flex align-items-center">
                  <div class="bg-primary rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                  <span class="text-muted">Ventas de tickets</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex align-items-center">
                  <div class="bg-success rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                  <span class="text-muted">Ventas de concesiones</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex align-items-center">
                  <div class="bg-info rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                  <span class="text-muted">Ventas totales</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  {/if}
</div> 