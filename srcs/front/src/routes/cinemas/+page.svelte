<script lang="ts">
  import { t } from '$lib/i18n';
  import { page } from '$app/stores';
  import { theme } from '$lib/theme';
  import HeroBanner from '$lib/components/HeroBanner.svelte';
  import { onMount } from 'svelte';

  // Obtener parámetros de búsqueda de la URL
  $: searchQuery = $page.url.searchParams.get('search') || '';
  $: locationFilter = $page.url.searchParams.get('location') || '';

  // Estado para almacenar los datos
  let rooms = [];
  let loading = true;
  let error = null;

  // Datos estáticos del cine (ya que solo tenemos uno)
  const cinema = {
    name: "Kaizen Cinema",
    description: "Tu cine de confianza con la mejor tecnología y comodidad.",
    address: "Westfield La Maquinista",
    phone: "+34 123 456 789",
    email: "kaizen@doncom.me",
    opening_hours: "Lunes a Domingo: 11:00 - 00:00",
    features: [
      "Parking gratuito",
      "Cafetería",
      "Snack bar",
      "Zona de juegos",
      "Acceso para discapacitados"
    ],
    has_3d: true,
    has_imax: true,
    has_vip: true,
    image_url: "/images/cinema-hero.jpg"
  };

  // Función para obtener las características de una sala según su tipo
  function getRoomFeatures(type, features) {
    const baseFeatures = {
      'standard': ['Sonido envolvente', 'Butacas cómodas'],
      'imax': ['Pantalla IMAX gigante', 'Sonido Dolby Atmos', 'Experiencia inmersiva'],
      'vip': ['Asientos reclinables de lujo', 'Servicio personalizado', 'Menú exclusivo'],
      '3d': ['Tecnología 3D avanzada', 'Gafas 3D premium']
    };

    let roomFeatures = [...(baseFeatures[type] || [])];
    
    // Añadir características adicionales basadas en features
    if (features) {
      if (features.is_3d) roomFeatures.push('Compatible con 3D');
      if (features.is_imax) roomFeatures.push('Certificado IMAX');
      if (features.is_vip) roomFeatures.push('Experiencia VIP');
    }

    return roomFeatures;
  }

  // Función para formatear el precio
  function formatPrice(price) {
    return new Intl.NumberFormat('es-ES', {
      style: 'currency',
      currency: 'EUR'
    }).format(price);
  }

  // Función para cargar las salas
  async function loadRooms() {
    try {
      loading = true;
      error = null;
      
      const response = await fetch('/api/v1/rooms');
      const result = await response.json();
      
      if (!result.success) {
        throw new Error(result.message);
      }
      
      // Formatear las salas con sus características
      rooms = result.data.map(room => ({
        ...room,
        features: getRoomFeatures(room.type, room.features),
        formattedPrice: formatPrice(room.price),
        capacity: room.layout.total_seats
      }));
      
    } catch (e) {
      error = e.message;
      console.error('Error cargando datos de las salas:', e);
    } finally {
      loading = false;
    }
  }

  onMount(() => {
    loadRooms();
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
{:else}
  <div data-bs-theme={$theme}>
    <!-- Hero Banner -->
    <HeroBanner 
      title={$t('ourCinema')}
      subtitle={$t('cinemaSubtitle')}
      imageUrl="/images/banners/f16awk8g.png"
      overlayOpacity="60"
    />

    <div class="container py-5">
      <!-- Información principal y características -->
      <div class="row g-4 mb-5">
        <!-- Columna izquierda: Información de contacto -->
        <div class="col-lg-4">
          <div class="info-card h-100">
            <div class="info-card-header">
              <i class="bi bi-info-circle-fill text-primary"></i>
              <h3 class="h5 mb-0">{$t('contactInfo')}</h3>
            </div>
            <div class="info-card-body">
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-geo-alt"></i>
                </div>
                <div class="info-content">
                  <h4>{$t('address')}</h4>
                  <p>{cinema.address}</p>
                  <a 
                    href={`https://maps.google.com/?q=${encodeURIComponent(cinema.address)}`} 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="map-link"
                  >
                    <i class="bi bi-map"></i>
                    <span>{$t('viewOnMap')}</span>
                  </a>
                </div>
              </div>
              
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-telephone"></i>
                </div>
                <div class="info-content">
                  <h4>{$t('phone')}</h4>
                  <p>{cinema.phone}</p>
                </div>
              </div>
              
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-envelope"></i>
                </div>
                <div class="info-content">
                  <h4>Email</h4>
                  <p>{cinema.email}</p>
                </div>
              </div>
              
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-clock"></i>
                </div>
                <div class="info-content">
                  <h4>{$t('openingHours')}</h4>
                  <p>{cinema.opening_hours}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Columna central: Descripción y características -->
        <div class="col-lg-4">
          <div class="info-card h-100">
            <div class="info-card-header">
              <i class="bi bi-building text-primary"></i>
              <h3 class="h5 mb-0">{$t('aboutUs')}</h3>
            </div>
            <div class="info-card-body">
              <p class="description">{cinema.description}</p>
              
              <div class="features-section">
                <h4 class="features-title">{$t('features')}</h4>
                <div class="features-badges">
                  {#if cinema.has_3d}
                    <span class="feature-badge feature-3d">
                      <i class="bi bi-badge-3d"></i>
                      <span>3D</span>
                    </span>
                  {/if}
                  
                  {#if cinema.has_imax}
                    <span class="feature-badge feature-imax">
                      <i class="bi bi-badge-hd"></i>
                      <span>IMAX</span>
                    </span>
                  {/if}
                  
                  {#if cinema.has_vip}
                    <span class="feature-badge feature-vip">
                      <i class="bi bi-star"></i>
                      <span>VIP</span>
                    </span>
                  {/if}
                </div>
              </div>
              
              <div class="services-section">
                <h4 class="services-title">{$t('services')}</h4>
                <div class="services-grid">
                  {#each cinema.features as feature}
                    <div class="service-item">
                      <i class="bi bi-check-circle-fill"></i>
                      <span>{feature}</span>
                    </div>
                  {/each}
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Columna derecha: Botones de acción -->
        <div class="col-lg-4">
          <div class="info-card h-100">
            <div class="info-card-header">
              <i class="bi bi-lightning-charge text-primary"></i>
              <h3 class="h5 mb-0">{$t('quickActions')}</h3>
            </div>
            <div class="info-card-body">
              <div class="action-buttons">
                <a href="/movies" class="action-button movies">
                  <i class="bi bi-film"></i>
                  <span>{$t('viewMovies')}</span>
                </a>
                
                <a href="/bookings/new" class="action-button tickets">
                  <i class="bi bi-ticket-perforated"></i>
                  <span>{$t('buyTickets')}</span>
                </a>
                
                <a href="/contact" class="action-button contact">
                  <i class="bi bi-chat-dots"></i>
                  <span>{$t('contact')}</span>
                </a>
              </div>
              
              <div class="social-section">
                <h4 class="social-title">{$t('followUs')}</h4>
                <div class="social-buttons">
                  <a href="#facebook" class="social-button facebook">
                    <i class="bi bi-facebook"></i>
                  </a>
                  <a href="#instagram" class="social-button instagram">
                    <i class="bi bi-instagram"></i>
                  </a>
                  <a href="#twitter" class="social-button twitter">
                    <i class="bi bi-twitter"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Sección de salas -->
      <div class="rooms-section">
        <div class="section-header">
          <h2 class="section-title">{$t('ourRooms')}</h2>
          <div class="section-divider"></div>
        </div>
        
        <div class="row g-4">
          {#each rooms as room, i}
            <div class="col-md-6 col-lg-4">
              <div class="room-card" style="--room-color: var(--room-color-{i % 5})">
                <div class="room-header">
                  <div class="room-icon">
                    <i class="bi bi-camera-reels"></i>
                  </div>
                  <h3 class="room-title">{room.name}</h3>
                  <div class="room-price">
                    {room.formattedPrice}
                    <span class="price-label">precio base</span>
                  </div>
                </div>
                
                <div class="room-content">
                  <div class="room-info">
                    <div class="room-capacity">
                      <i class="bi bi-people"></i>
                      <span>{room.capacity} {$t('capacity')}</span>
                    </div>
                    <div class="room-layout">
                      <i class="bi bi-grid"></i>
                      <span>{room.layout.rows} filas × {room.layout.seats_per_row} asientos</span>
                    </div>
                  </div>
                  
                  {#if room.features.length > 0}
                    <div class="room-features">
                      {#each room.features as feature}
                        <span class="room-feature">
                          <i class="bi bi-check2"></i>
                          <span>{feature}</span>
                        </span>
                      {/each}
                    </div>
                  {/if}
                </div>
              </div>
            </div>
          {/each}
        </div>
        
        <!-- Botón para ver cartelera -->
        <div class="cta-section">
          <a href="/movies" class="cta-button">
            <i class="bi bi-film"></i>
            <span>{$t('viewNowShowing')}</span>
          </a>
        </div>
      </div>
    </div>
  </div>
{/if}

<style>
  /* Variables de color */
  :root {
    /* Colores principales */
    --primary: #6366f1;
    --primary-hover: #4f46e5;
    --secondary: #a855f7;
    --secondary-hover: #9333ea;
    
    /* Colores de características */
    --feature-3d: #3b82f6;
    --feature-imax: #0ea5e9;
    --feature-vip: #eab308;
    
    /* Colores de salas */
    --room-color-0: #FF6B6B;
    --room-color-1: #4ECDC4;
    --room-color-2: #45B7D1;
    --room-color-3: #96CEB4;
    --room-color-4: #FFEEAD;
    
    /* Colores de acciones */
    --action-movies: #3b82f6;
    --action-tickets: #10b981;
    --action-contact: #6366f1;
    
    /* Colores de redes sociales */
    --social-facebook: #1877f2;
    --social-instagram: #e4405f;
    --social-twitter: #1da1f2;
  }

  /* Tarjetas de información */
  .info-card {
    background: var(--bs-body-bg);
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
                0 4px 6px -2px rgba(0, 0, 0, 0.05);
  }

  .info-card-header {
    padding: 1.25rem;
    border-bottom: 1px solid var(--bs-border-color);
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .info-card-header i {
    font-size: 1.25rem;
  }

  .info-card-body {
    padding: 1.25rem;
  }

  /* Items de información */
  .info-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--bs-border-color);
  }

  .info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .info-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
  }

  .info-content h4 {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--bs-body-color);
  }

  .info-content p {
    margin-bottom: 0.5rem;
    color: var(--bs-body-color);
    opacity: 0.8;
  }

  .map-link {
    color: var(--primary);
    text-decoration: none;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: color 0.2s ease;
  }

  .map-link:hover {
    color: var(--primary-hover);
  }

  /* Sección de características */
  .features-section,
  .services-section {
    margin-top: 1.5rem;
  }

  .features-title,
  .services-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1rem;
  }

  .features-badges {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  .feature-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: white;
    transition: transform 0.2s ease;
  }

  .feature-badge:hover {
    transform: translateY(-1px);
  }

  .feature-3d {
    background: var(--feature-3d);
  }

  .feature-imax {
    background: var(--feature-imax);
  }

  .feature-vip {
    background: var(--feature-vip);
  }

  /* Servicios */
  .services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.75rem;
  }

  .service-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
  }

  .service-item i {
    color: var(--primary);
    font-size: 1rem;
  }

  /* Botones de acción */
  .action-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .action-button {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 0.75rem;
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: transform 0.2s ease, opacity 0.2s ease;
  }

  .action-button:hover {
    transform: translateY(-1px);
    opacity: 0.9;
    color: white;
  }

  .action-button.movies {
    background: var(--action-movies);
  }

  .action-button.tickets {
    background: var(--action-tickets);
  }

  .action-button.contact {
    background: var(--action-contact);
  }

  /* Redes sociales */
  .social-section {
    margin-top: auto;
  }

  .social-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1rem;
  }

  .social-buttons {
    display: flex;
    gap: 0.75rem;
  }

  .social-button {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: transform 0.2s ease, opacity 0.2s ease;
  }

  .social-button:hover {
    transform: translateY(-1px);
    opacity: 0.9;
    color: white;
  }

  .social-button.facebook {
    background: var(--social-facebook);
  }

  .social-button.instagram {
    background: var(--social-instagram);
  }

  .social-button.twitter {
    background: var(--social-twitter);
  }

  /* Sección de salas */
  .rooms-section {
    margin-top: 4rem;
  }

  .section-header {
    text-align: center;
    margin-bottom: 3rem;
  }

  .section-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .section-divider {
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    margin: 0 auto;
    border-radius: 2px;
  }

  /* Tarjetas de sala - Nuevo diseño */
  .room-card {
    background: var(--bs-body-bg);
    border-radius: 1rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .room-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, 
      var(--room-color) 0%,
      transparent 60%
    );
    opacity: 0.1;
    transition: opacity 0.2s ease;
  }

  .room-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }

  .room-card:hover::before {
    opacity: 0.15;
  }

  .room-header {
    padding: 1.5rem 1.5rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .room-icon {
    width: 50px;
    height: 50px;
    background: var(--room-color);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    transform: rotate(-5deg);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  }

  .room-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    color: var(--room-color);
  }

  .room-content {
    padding: 0 1.5rem 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .room-price {
    margin-top: 0.5rem;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--room-color);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .price-label {
    font-size: 0.75rem;
    font-weight: 400;
    opacity: 0.7;
    text-transform: uppercase;
  }

  .room-info {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .room-capacity,
  .room-layout {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
    background: rgba(var(--room-color-rgb), 0.1);
    border-radius: 0.5rem;
    color: var(--room-color);
  }

  .room-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
  }

  .room-feature {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    background: rgba(var(--room-color-rgb), 0.05);
    color: var(--room-color);
    border: 1px solid rgba(var(--room-color-rgb), 0.2);
    transition: all 0.2s ease;
  }

  .room-feature:hover {
    background: rgba(var(--room-color-rgb), 0.1);
    border-color: var(--room-color);
  }

  .room-feature i {
    font-size: 0.875rem;
  }

  /* CTA Section */
  .cta-section {
    text-align: center;
    margin-top: 3rem;
  }

  .cta-button {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: transform 0.2s ease, opacity 0.2s ease;
  }

  .cta-button:hover {
    transform: translateY(-2px);
    opacity: 0.9;
    color: white;
  }

  /* Ajustes para tema oscuro */
  :global([data-bs-theme="dark"]) {
    .info-card {
      background: rgba(17, 24, 39, 0.8);
      border-color: rgba(255, 255, 255, 0.1);
    }

    .info-card-header {
      border-color: rgba(255, 255, 255, 0.1);
    }

    .info-item {
      border-color: rgba(255, 255, 255, 0.1);
    }

    .room-card {
      background: rgba(17, 24, 39, 0.8);
    }

    .room-capacity {
      background: rgba(255, 255, 255, 0.1);
    }

    .room-feature {
      background: rgba(255, 255, 255, 0.05);
      border-color: rgba(255, 255, 255, 0.2);
    }

    .room-feature:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.3);
    }
  }
</style>