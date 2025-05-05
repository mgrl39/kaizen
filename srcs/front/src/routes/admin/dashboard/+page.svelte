<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';

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
    // Redirigir al dashboard principal
    goto('/admin/dashboard');
  });
</script>

<div class="redirect-container">
  <div class="spinner-border text-primary" role="status">
    <span class="visually-hidden">Redirigiendo...</span>
  </div>
  <p class="mt-3">Redirigiendo al panel de administración...</p>
</div>

<style>
  .redirect-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
  }
</style> 