<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';

  let stats = {
    cinemas: 0,
    movies: 0,
    users: 0,
    bookings: 0
  };
  
  let loading = true;
  let error : string | null = null;
  
  // Datos de ejemplo para las gráficas
  const monthlyData = [
    { month: 'Ene', bookings: 65 },
    { month: 'Feb', bookings: 59 },
    { month: 'Mar', bookings: 80 },
    { month: 'Abr', bookings: 81 },
    { month: 'May', bookings: 56 },
    { month: 'Jun', bookings: 55 },
    { month: 'Jul', bookings: 40 },
    { month: 'Ago', bookings: 70 },
    { month: 'Sep', bookings: 90 },
    { month: 'Oct', bookings: 110 },
    { month: 'Nov', bookings: 95 },
    { month: 'Dic', bookings: 120 }
  ];
  
  const recentMovies = [
    { id: 1, title: 'Dune 2', bookings: 245, revenue: '€4,325.00', poster: 'https://source.unsplash.com/150x200/?movie,scifi' },
    { id: 2, title: 'Deadpool 3', bookings: 187, revenue: '€3,187.00', poster: 'https://source.unsplash.com/150x200/?movie,action' },
    { id: 3, title: 'Gladiator 2', bookings: 156, revenue: '€2,854.00', poster: 'https://source.unsplash.com/150x200/?movie,roman' },
    { id: 4, title: 'Joker 2', bookings: 132, revenue: '€2,412.00', poster: 'https://source.unsplash.com/150x200/?movie,thriller' }
  ];
  
  async function fetchDashboardData() {
    loading = true;
    const token = localStorage.getItem('token');
    
    if (!token) return;
    
    try {
      // En un entorno real, esto sería una llamada a la API
      // const response = await fetch(`${API_URL}/admin/dashboard`, {
      //   headers: {
      //     'Authorization': `Bearer ${token}`,
      //     'Accept': 'application/json'
      //   }
      // });
      // const data = await response.json();
      
      // Simulamos datos para desarrollo
      setTimeout(() => {
        stats = {
          cinemas: 12,
          movies: 64,
          users: 523,
          bookings: 1847
        };
        loading = false;
      }, 1000);
      
    } catch (e) {
      error = 'Error al cargar los datos del dashboard';
      loading = false;
    }
  }
  
  onMount(() => {
    fetchDashboardData();
  });
</script>

<div class="dashboard-container">
  <div class="dashboard-header">
    <h1 class="mb-4">Dashboard</h1>
    <p class="text-muted">Bienvenido al panel de administración de Kaizen Cinemas</p>
  </div>
  
  {#if loading}
    <div class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  {:else if error}
    <div class="alert alert-danger">{error}</div>
  {:else}
    <!-- Resumen de estadísticas -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="bi bi-building"></i>
          </div>
          <div class="stat-details">
            <h3>{stats.cinemas}</h3>
            <p>Cines</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="bi bi-film"></i>
          </div>
          <div class="stat-details">
            <h3>{stats.movies}</h3>
            <p>Películas</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="bi bi-people"></i>
          </div>
          <div class="stat-details">
            <h3>{stats.users}</h3>
            <p>Usuarios</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="bi bi-ticket-perforated"></i>
          </div>
          <div class="stat-details">
            <h3>{stats.bookings}</h3>
            <p>Reservas</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Gráficas y datos adicionales -->
    <div class="row">
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0">Reservas mensuales</h5>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <div class="chart-area">
                {#each monthlyData as data, i}
                  <div class="chart-bar" style="height: {data.bookings}px; left: {i * 8.33}%;">
                    <div class="chart-tooltip">
                      {data.month}: {data.bookings} reservas
                    </div>
                  </div>
                {/each}
              </div>
              <div class="chart-labels">
                {#each monthlyData as data}
                  <div class="chart-label">{data.month}</div>
                {/each}
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0">Películas populares</h5>
          </div>
          <div class="card-body p-0">
            <ul class="list-group list-group-flush">
              {#each recentMovies as movie}
                <li class="list-group-item">
                  <div class="d-flex align-items-center">
                    <img src={movie.poster} alt={movie.title} class="movie-thumb me-3">
                    <div class="movie-details">
                      <h6 class="mb-1">{movie.title}</h6>
                      <div class="d-flex text-muted small">
                        <span class="me-3"><i class="bi bi-ticket me-1"></i> {movie.bookings}</span>
                        <span><i class="bi bi-currency-euro me-1"></i> {movie.revenue}</span>
                      </div>
                    </div>
                  </div>
                </li>
              {/each}
            </ul>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Accesos rápidos -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">Acciones rápidas</h5>
          </div>
          <div class="card-body">
            <div class="row action-buttons">
              <div class="col-md-3 col-sm-6">
                <a href="/admin/cinemas/new" class="btn btn-outline-primary btn-action">
                  <i class="bi bi-plus-circle"></i>
                  <span>Nuevo cine</span>
                </a>
              </div>
              <div class="col-md-3 col-sm-6">
                <a href="/admin/movies/new" class="btn btn-outline-success btn-action">
                  <i class="bi bi-plus-circle"></i>
                  <span>Nueva película</span>
                </a>
              </div>
              <div class="col-md-3 col-sm-6">
                <a href="/admin/users" class="btn btn-outline-info btn-action">
                  <i class="bi bi-people"></i>
                  <span>Ver usuarios</span>
                </a>
              </div>
              <div class="col-md-3 col-sm-6">
                <a href="/admin/bookings" class="btn btn-outline-warning btn-action">
                  <i class="bi bi-ticket-perforated"></i>
                  <span>Ver reservas</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  {/if}
</div>

<style>
  .dashboard-header {
    margin-bottom: 30px;
  }
  
  .stat-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    padding: 20px;
    display: flex;
    align-items: center;
    height: 100px;
    margin-bottom: 20px;
  }
  
  .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
  }
  
  .stat-icon i {
    font-size: 24px;
    color: white;
  }
  
  .stat-details h3 {
    font-size: 24px;
    margin-bottom: 0;
    font-weight: 600;
  }
  
  .stat-details p {
    margin-bottom: 0;
    color: #6c757d;
  }
  
  .chart-container {
    position: relative;
    height: 250px;
    margin-top: 20px;
    padding-bottom: 30px;
  }
  
  .chart-area {
    position: relative;
    height: 200px;
    width: 100%;
    border-bottom: 1px solid #dee2e6;
  }
  
  .chart-bar {
    position: absolute;
    bottom: 0;
    width: 20px;
    background-color: #6610f2;
    border-radius: 4px 4px 0 0;
    margin-left: -10px;
    opacity: 0.7;
    transition: opacity 0.3s;
  }
  
  .chart-bar:hover {
    opacity: 1;
  }
  
  .chart-bar:hover .chart-tooltip {
    display: block;
  }
  
  .chart-tooltip {
    display: none;
    position: absolute;
    top: -40px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #333;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
  }
  
  .chart-tooltip:after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #333 transparent transparent transparent;
  }
  
  .chart-labels {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
  }
  
  .chart-label {
    font-size: 12px;
    width: 8.33%;
    text-align: center;
    color: #6c757d;
  }
  
  .movie-thumb {
    width: 40px;
    height: 60px;
    border-radius: 4px;
    object-fit: cover;
  }
  
  .action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
  }
  
  .btn-action {
    width: 100%;
    height: 100px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
  }
  
  .btn-action i {
    font-size: 24px;
    margin-bottom: 10px;
  }
  
  @media (max-width: 768px) {
    .stat-card {
      margin-bottom: 15px;
    }
    
    .chart-container {
      height: 200px;
    }
    
    .chart-area {
      height: 150px;
    }
  }
</style> 