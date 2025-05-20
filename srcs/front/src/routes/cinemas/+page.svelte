<script lang="ts">
  import { t } from '$lib/i18n';
  import { page } from '$app/stores';
  import { theme } from '$lib/theme';
  import HeroBanner from '$lib/components/HeroBanner.svelte';

  // Obtener parámetros de búsqueda de la URL
  $: searchQuery = $page.url.searchParams.get('search') || '';
  $: locationFilter = $page.url.searchParams.get('location') || '';

  // Datos hardcodeados del cine
  const cinema = {
    id: 1,
    name: 'Kaizen Cinema',
    location: 'Madrid',
    address: 'Calle Gran Vía 123, Madrid',
    phone: '+34 912 345 678',
    email: 'info@kaizencinema.com',
    description: 'Bienvenidos a Kaizen Cinema, el lugar perfecto para disfrutar de las mejores películas con la mejor calidad de imagen y sonido. Nuestras modernas instalaciones están diseñadas para ofrecerte la mejor experiencia cinematográfica.',
    image_url: 'https://source.unsplash.com/random/1920x1080/?cinema,theater,auditorium',
    has_3d: true,
    has_imax: true,
    has_vip: true,
    rooms_count: 5,
    opening_hours: '10:00 - 00:00',
    features: ['Parking gratuito', 'Cafetería', 'Zona infantil', 'Acceso para discapacitados']
  };
  
  // Información de salas del cine
  const rooms = [
    { id: 1, name: 'Sala 1', capacity: 120, features: ['3D', 'Dolby Atmos'] },
    { id: 2, name: 'Sala 2', capacity: 150, features: ['IMAX', 'Dolby Atmos'] },
    { id: 3, name: 'Sala 3', capacity: 80, features: ['VIP'] },
    { id: 4, name: 'Sala 4', capacity: 100, features: [] },
    { id: 5, name: 'Sala 5', capacity: 90, features: ['3D'] }
  ];
</script>

<div data-bs-theme={$theme}>
  <!-- Hero Banner con imagen específica para la página de cines -->
  <HeroBanner 
    title="Nuestro Cine"
    subtitle="Descubre el mejor lugar para disfrutar del séptimo arte"
    imageUrl={cinema.image_url}
    overlayOpacity="60"
  />

  <div class="container py-5">
    <!-- Información principal y características -->
    <div class="row g-4 mb-5">
      <!-- Columna izquierda: Información de contacto -->
      <div class="col-lg-4">
        <div class="card h-100 border-0 shadow">
          <div class="card-header bg-primary text-white">
            <h3 class="h5 mb-0">Información de contacto</h3>
          </div>
          <div class="card-body">
            <div class="mb-4">
              <div class="d-flex">
                <i class="bi bi-geo-alt text-primary me-3 mt-1"></i>
                <div>
                  <p class="fw-medium mb-1">Dirección</p>
                  <p class="mb-1">{cinema.address}</p>
                  <a 
                    href={`https://maps.google.com/?q=${encodeURIComponent(cinema.address)}`} 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="text-primary small d-inline-flex align-items-center"
                  >
                    <i class="bi bi-map me-1"></i>Ver en mapa
                  </a>
                </div>
              </div>
            </div>
            
            <div class="mb-4">
              <div class="d-flex">
                <i class="bi bi-telephone text-primary me-3 mt-1"></i>
                <div>
                  <p class="fw-medium mb-1">Teléfono</p>
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
                  <p class="fw-medium mb-1">Horario</p>
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
          <div class="card-header bg-primary text-white">
            <h3 class="h5 mb-0">Sobre nosotros</h3>
          </div>
          <div class="card-body">
            <p class="mb-4">{cinema.description}</p>
            
            <h4 class="h6 fw-bold mb-3">Características</h4>
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
            
            <h4 class="h6 fw-bold mb-3">Servicios</h4>
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
          <div class="card-header bg-primary text-white">
            <h3 class="h5 mb-0">Acciones rápidas</h3>
          </div>
          <div class="card-body">
            <div class="d-grid gap-3 mb-4">
              <a 
                href="/movies" 
                class="btn btn-primary"
              >
                <i class="bi bi-film me-2"></i>
                Ver cartelera
              </a>
              
              <a 
                href="/bookings/new" 
                class="btn btn-gradient"
              >
                <i class="bi bi-ticket-perforated me-2"></i>
                Comprar entradas
              </a>
              
              <a 
                href="/contact" 
                class="btn btn-outline-secondary"
              >
                <i class="bi bi-chat-dots me-2"></i>
                Contactar
              </a>
            </div>
            
            <h4 class="h6 fw-bold mb-3">Síguenos</h4>
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
      <h3 class="h3 fw-bold mb-4">Nuestras salas</h3>
      
      <div class="row g-4">
        {#each rooms as room}
          <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow h-100">
              <div class="card-body">
                <h4 class="h5 fw-bold mb-2">{room.name}</h4>
                <p class="mb-3">Capacidad: {room.capacity} personas</p>
                
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
        Ver películas en cartelera
      </a>
    </div>
  </div>
</div>

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