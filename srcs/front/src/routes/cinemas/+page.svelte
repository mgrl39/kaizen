<script lang="ts">
  import { t } from '$lib/i18n';
  import { page } from '$app/stores';
  import { theme } from '$lib/theme';
  import HeroBanner from '$lib/components/HeroBanner.svelte';
  import { onMount } from 'svelte';

  // Obtener parámetros de búsqueda de la URL
  $: searchQuery = $page.url.searchParams.get('search') || '';
  $: locationFilter = $page.url.searchParams.get('location') || '';

  // Estado para almacenar los datos del cine
  let cinema = null;
  let rooms = [];
  let loading = true;
  let error = null;

  // Función para formatear los servicios
  function formatFeatures(features) {
    if (!features) return [];
    
    // Si es un string, intentamos parsearlo como JSON
    if (typeof features === 'string') {
      try {
        features = JSON.parse(features);
      } catch (e) {
        console.error('Error parsing features:', e);
        return [];
      }
    }
    
    // Si no es un array después de parsear, lo convertimos en array
    if (!Array.isArray(features)) {
      console.warn('Features is not an array:', features);
      return [];
    }
    
    return features.map(feature => {
      if (Array.isArray(feature)) {
        return feature.join('');
      }
      if (typeof feature === 'string') {
        // Decodificar caracteres Unicode
        return decodeURIComponent(JSON.parse(`"${feature}"`));
      }
      return String(feature);
    });
  }

  // Función para cargar los datos del cine
  async function loadCinemaData() {
    try {
      loading = true;
      error = null;
      
      const response = await fetch('/api/v1/cinemas/1');
      const result = await response.json();
      
      if (!result.success) {
        throw new Error(result.message);
      }
      
      const cinemaData = result.data;
      
      // Formatear las características tanto del cine como de las salas
      cinema = {
        ...cinemaData,
        features: formatFeatures(cinemaData.features)
      };
      
      // Formatear las características de cada sala
      rooms = cinemaData.rooms.map(room => ({
        ...room,
        features: formatFeatures(room.features)
      }));
      
    } catch (e) {
      error = e.message;
      console.error('Error cargando datos del cine:', e);
    } finally {
      loading = false;
    }
  }

  onMount(() => {
    loadCinemaData();
  });
</script>

{#if loading}
  <div class="d-flex justify-content-center py-5">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Cargando...</span>
    </div>
  </div>
{:else if error}
  <div class="alert alert-danger m-3" role="alert">
    Error: {error}
  </div>
{:else if cinema}
  <div data-bs-theme={$theme}>
    <!-- Hero Banner con imagen específica para la página de cines -->
    <HeroBanner 
      title={$t('ourCinema')}
      subtitle={$t('cinemaSubtitle')}
      imageUrl={cinema.image_url}
      overlayOpacity="60"
    />

    <div class="container py-5">
      <!-- Información principal y características -->
      <div class="row g-4 mb-5">
        <!-- Columna izquierda: Información de contacto -->
        <div class="col-lg-4">
          <div class="card h-100 border-0 shadow">
            <div class="card-header bg-light text-dark">
              <h3 class="h5 mb-0">{$t('contactInfo')}</h3>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <div class="d-flex">
                  <i class="bi bi-geo-alt text-primary me-3 mt-1"></i>
                  <div>
                    <p class="fw-medium mb-1">{$t('address')}</p>
                    <p class="mb-1">{cinema.address}</p>
                    <a 
                      href={`https://maps.google.com/?q=${encodeURIComponent(cinema.address)}`} 
                      target="_blank" 
                      rel="noopener noreferrer"
                      class="text-primary small d-inline-flex align-items-center"
                    >
                      <i class="bi bi-map me-1"></i>{$t('viewOnMap')}
                    </a>
                  </div>
                </div>
              </div>
              
              <div class="mb-4">
                <div class="d-flex">
                  <i class="bi bi-telephone text-primary me-3 mt-1"></i>
                  <div>
                    <p class="fw-medium mb-1">{$t('phone')}</p>
                    <p class="mb-0">{cinema.phone}</p>
                  </div>
                </div>
              </div>
              
              <div class="mb-4">
                <div class="d-flex">
                  <i class="bi bi-envelope text-primary me-3 mt-1"></i>
                  <div>
                    <p class="fw-medium mb-1">Email</p>
                    <p class="mb-0">{cinema.email}</p>
                  </div>
                </div>
              </div>
              
              <div class="mb-0">
                <div class="d-flex">
                  <i class="bi bi-clock text-primary me-3 mt-1"></i>
                  <div>
                    <p class="fw-medium mb-1">{$t('openingHours')}</p>
                    <p class="mb-0">{cinema.opening_hours}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Columna central: Descripción y características -->
        <div class="col-lg-4">
          <div class="card h-100 border-0 shadow">
            <div class="card-header bg-light text-dark">
              <h3 class="h5 mb-0">{$t('aboutUs')}</h3>
            </div>
            <div class="card-body">
              <p class="mb-4">{cinema.description}</p>
              
              <h4 class="h6 fw-bold mb-3">{$t('features')}</h4>
              <div class="d-flex flex-wrap gap-2 mb-4">
                {#if cinema.has_3d}
                  <span class="badge bg-primary">
                    <i class="bi bi-badge-3d me-1"></i>3D
                  </span>
                {/if}
                
                {#if cinema.has_imax}
                  <span class="badge bg-info">
                    <i class="bi bi-badge-hd me-1"></i>IMAX
                  </span>
                {/if}
                
                {#if cinema.has_vip}
                  <span class="badge bg-warning">
                    <i class="bi bi-star me-1"></i>Salas VIP
                  </span>
                {/if}
              </div>
              
              <h4 class="h6 fw-bold mb-3">{$t('services')}</h4>
              <div class="row g-2">
                {#each cinema.features as feature}
                  <div class="col-6">
                    <div class="d-flex align-items-center">
                      <i class="bi bi-check-circle text-success me-2"></i>
                      <span>{feature}</span>
                    </div>
                  </div>
                {/each}
              </div>
            </div>
          </div>
        </div>
        
        <!-- Columna derecha: Botones de acción -->
        <div class="col-lg-4">
          <div class="card h-100 border-0 shadow">
            <div class="card-header bg-light text-dark">
              <h3 class="h5 mb-0">{$t('quickActions')}</h3>
            </div>
            <div class="card-body">
              <div class="d-grid gap-3 mb-4">
                <a 
                  href="/movies" 
                  class="btn btn-primary"
                >
                  <i class="bi bi-film me-2"></i>
                  {$t('viewMovies')}
                </a>
                
                <a 
                  href="/bookings/new" 
                  class="btn btn-gradient"
                >
                  <i class="bi bi-ticket-perforated me-2"></i>
                  {$t('buyTickets')}
                </a>
                
                <a 
                  href="/contact" 
                  class="btn btn-outline-secondary"
                >
                  <i class="bi bi-chat-dots me-2"></i>
                  {$t('contact')}
                </a>
              </div>
              
              <h4 class="h6 fw-bold mb-3">{$t('followUs')}</h4>
              <div class="d-flex gap-2">
                <a href="#test" class="btn btn-outline-primary btn-sm rounded-circle" aria-label="Facebook">
                  <i class="bi bi-facebook"></i>
                </a>
                <a href="#test" class="btn btn-outline-danger btn-sm rounded-circle" aria-label="Instagram">
                  <i class="bi bi-instagram"></i>
                </a>
                <a href="#test" class="btn btn-outline-info btn-sm rounded-circle" aria-label="Twitter">
                  <i class="bi bi-twitter"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Sección de salas -->
      <div class="mb-5">
        <h3 class="h3 fw-bold mb-4">{$t('ourRooms')}</h3>
        
        <div class="row g-4">
          {#each rooms as room}
            <div class="col-md-6 col-lg-4">
              <div class="card border-0 shadow h-100">
                <div class="card-body">
                  <h4 class="h5 fw-bold mb-2">{room.name}</h4>
                  <p class="mb-3">{$t('capacity')}: {room.capacity} personas</p>
                  
                  {#if room.features.length > 0}
                    <div class="d-flex flex-wrap gap-2">
                      {#each room.features as feature}
                        <span class="badge bg-secondary">
                          {feature}
                        </span>
                      {/each}
                    </div>
                  {/if}
                </div>
              </div>
            </div>
          {/each}
        </div>
      </div>
      
      <!-- Botón para ver cartelera -->
      <div class="text-center">
        <a 
          href="/movies" 
          class="btn btn-lg btn-primary"
        >
          <i class="bi bi-film me-2"></i>
          {$t('viewNowShowing')}
        </a>
      </div>
    </div>
  </div>
{:else}
  <div class="d-flex justify-content-center py-5">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Cargando...</span>
    </div>
  </div>
{/if}

<style>
  /* Estilo para botón con gradiente */
  .btn-gradient {
    background: linear-gradient(to right, var(--bs-primary), var(--bs-indigo));
    color: white;
    border: none;
  }
  
  .btn-gradient:hover {
    background: linear-gradient(to right, var(--bs-primary-dark), var(--bs-indigo-dark));
    color: white;
  }
  
  /* Variables para colores más oscuros */
  :root {
    --bs-primary-dark: #5b21b6;
    --bs-indigo-dark: #4338ca;
  }
</style>