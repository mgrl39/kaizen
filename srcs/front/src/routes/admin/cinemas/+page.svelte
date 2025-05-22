<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  
  // Un solo cine en el sistema
  const cinema = {
    id: 1,
    name: 'Cineplex Central',
    location: 'Downtown',
    screens: 8,
    status: 'active',
    description: 'El mejor cine de la ciudad con las mejores instalaciones y tecnología de última generación.',
    phone: '+34 912 345 678',
    email: 'info@cineplexcentral.com',
    address: 'Calle Principal 123, 28001 Madrid'
  };
  
  // Capacidad total y otras estadísticas
  let totalCapacity = 0;
  let avgScreenSize = 0;
  
  // Generar datos para las salas de cine
  const screens = Array(cinema.screens).fill(0).map((_, i) => {
    const capacity = Math.floor(Math.random() * 50) + 100;
    const type = i < 2 ? 'IMAX' : i < 4 ? '3D' : 'Standard';
    totalCapacity += capacity;
    
    return {
      id: i + 1,
      name: `Sala ${i + 1}`,
      type,
      capacity,
      status: Math.random() > 0.1 ? 'active' : 'maintenance'
    };
  });
  
  // Calcular estadísticas
  avgScreenSize = Math.round(totalCapacity / cinema.screens);
  
  onMount(() => {
    // Initialize any Bootstrap components if needed
  });
</script>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">{$t('cinema')}</h1>
    <a href="/admin/cinemas/edit" class="btn btn-success">
      <i class="bi bi-pencil-square me-2"></i>
      {$t('editCinema')}
    </a>
  </div>
  
  <!-- Dashboard Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card h-100 border-primary">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-film text-primary fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">{$t('screens')}</h6>
              <h2 class="card-title mb-0">{cinema.screens}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 border-success">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-people text-success fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">{$t('totalCapacity')}</h6>
              <h2 class="card-title mb-0">{totalCapacity}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 border-info">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-layout-text-window text-info fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">{$t('avgScreenSize')}</h6>
              <h2 class="card-title mb-0">{avgScreenSize}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 border-warning">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="bg-light p-3 rounded me-3">
              <i class="bi bi-building text-warning fs-4"></i>
            </div>
            <div>
              <h6 class="card-subtitle mb-1 text-muted">{$t('status')}</h6>
              <h2 class="card-title mb-0">
                <span class="badge bg-success">{$t(cinema.status)}</span>
              </h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Información del cine -->
  <div class="card mb-4">
    <div class="card-header bg-white">
      <h2 class="h5 mb-0">{cinema.name}</h2>
    </div>
    <div class="card-body">
      <div class="row mb-4">
        <div class="col-md-8">
          <p class="lead">{cinema.description}</p>
        </div>
        <div class="col-md-4">
          <div class="text-end">
            <h5 class="text-muted">{$t('features')}</h5>
            <div class="mt-2">
              <span class="badge bg-primary me-1">IMAX</span>
              <span class="badge bg-primary me-1">3D</span>
              <span class="badge bg-primary me-1">Dolby Atmos</span>
              <span class="badge bg-primary">VIP</span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <h5 class="text-muted">{$t('location')}</h5>
            <p class="mb-0">{cinema.location}</p>
          </div>
          
          <div class="mb-3">
            <h5 class="text-muted">{$t('address')}</h5>
            <p class="mb-0">{cinema.address}</p>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="mb-3">
            <h5 class="text-muted">{$t('contact')}</h5>
            <p class="mb-0">{cinema.phone}</p>
            <p class="mb-0">{cinema.email}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Salas del cine -->
  <div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h2 class="h5 mb-0">{$t('screens')}</h2>
      <button class="btn btn-sm btn-outline-primary">
        <i class="bi bi-plus me-1"></i>
        {$t('addScreen')}
      </button>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>{$t('screen')}</th>
              <th>{$t('type')}</th>
              <th>{$t('capacity')}</th>
              <th>{$t('status')}</th>
              <th class="text-end">{$t('actions')}</th>
            </tr>
          </thead>
          <tbody>
            {#each screens as screen}
              <tr>
                <td>
                  <span class="fw-bold">{screen.name}</span>
                </td>
                <td>{screen.type}</td>
                <td>{screen.capacity} {$t('seats')}</td>
                <td>
                  <span class="badge {screen.status === 'active' ? 'bg-success' : 'bg-warning'}">
                    {$t(screen.status)}
                  </span>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-secondary me-1">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-primary me-1">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white text-end">
      <small class="text-muted">{$t('totalScreens')}: {cinema.screens}</small>
    </div>
  </div>
</div> 