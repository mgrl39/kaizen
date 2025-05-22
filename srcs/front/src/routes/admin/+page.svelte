<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  
  // Datos iniciales del dashboard
  let stats = [
    { 
      name: 'totalMovies', 
      value: '0', 
      path: '/admin/movies',
      icon: 'film',
      color: 'primary'
    },
    { 
      name: 'totalScreens', 
      value: '0', 
      path: '/admin/cinemas',
      icon: 'building',
      color: 'success'
    },
    { 
      name: 'totalUsers', 
      value: '0', 
      path: '/admin/users',
      icon: 'people',
      color: 'info'
    },
    { 
      name: 'totalBookings', 
      value: '0', 
      path: '/admin/bookings',
      icon: 'calendar',
      color: 'warning'
    }
  ];
  
  // Lista básica de actividades recientes
  const recentActivities = [
    { id: 1, description: 'Nueva película añadida: "Dune: Part Two"', date: '15/07/2023' },
    { id: 2, description: 'Actualización del cine: "Cineplex Central"', date: '14/07/2023' },
    { id: 3, description: 'Nuevo usuario registrado: maria.garcia@example.com', date: '13/07/2023' }
  ];

  // Carga los datos desde la API
  onMount(async () => {
    try {
      // Obtener conteo de usuarios
      const usersResponse = await fetch('/api/v1/users');
      if (usersResponse.ok) {
        const userData = await usersResponse.json();
        if (userData.success && userData.data) {
          // Actualizar el conteo de usuarios
          stats = stats.map(stat => 
            stat.name === 'totalUsers' 
              ? { ...stat, value: userData.data.total?.toString() || '0' } 
              : stat
          );
        }
      }
    } catch (error) {
      console.error('Error al cargar datos del dashboard:', error);
    }
  });
</script>

<div class="container-fluid">
  <h1 class="h3 mb-4">{$t('dashboard')}</h1>
  
  <!-- Tarjetas de estadísticas -->
  <div class="row g-4 mb-4">
    {#each stats as stat}
      <div class="col-md-6 col-lg-3">
        <div class="card border-{stat.color} h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="bg-light p-3 rounded me-3">
                <i class="bi bi-{stat.icon} text-{stat.color} fs-4"></i>
              </div>
              <div>
                <h6 class="card-subtitle mb-1 text-muted">{$t(stat.name)}</h6>
                <h2 class="card-title mb-0">{stat.value}</h2>
              </div>
            </div>
          </div>
          <div class="card-footer bg-transparent border-top">
            <a href={stat.path} class="text-{stat.color} text-decoration-none">
              {$t('viewDetails')} <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    {/each}
  </div>
  
  <div class="row g-4">
    <!-- Actividad reciente -->
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="card-title mb-0">{$t('recentActivity')}</h5>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            {#each recentActivities as activity}
              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <div>
                    <p class="mb-1">{activity.description}</p>
                    <small class="text-muted">{activity.date}</small>
                  </div>
                </div>
              </li>
            {/each}
          </ul>
        </div>
        {#if recentActivities.length > 0}
          <div class="card-footer bg-transparent text-end">
            <a href="/admin/activity" class="text-decoration-none">
              {$t('viewAllActivity')} <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        {/if}
      </div>
    </div>
    
    <!-- Acciones rápidas -->
    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">{$t('quickActions')}</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="/admin/movies/add" class="btn btn-primary">
              <i class="bi bi-plus-circle me-2"></i> {$t('addMovie')}
            </a>
            <a href="/admin/cinemas/add" class="btn btn-success">
              <i class="bi bi-plus-circle me-2"></i> {$t('addCinema')}
            </a>
            <a href="/admin/users/add" class="btn btn-info">
              <i class="bi bi-plus-circle me-2"></i> {$t('addUser')}
            </a>
          </div>
        </div>
      </div>
      
      <!-- Próximos estrenos -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{$t('upcomingPremieres')}</h5>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Dune: Part Two</span>
              <span class="badge bg-primary rounded-pill">15/09/2023</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>The Batman 2</span>
              <span class="badge bg-primary rounded-pill">22/10/2023</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Avatar 3</span>
              <span class="badge bg-primary rounded-pill">18/12/2023</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div> 