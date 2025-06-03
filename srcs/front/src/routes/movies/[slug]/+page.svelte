<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { fade, fly } from 'svelte/transition';
  import SharePopup from '$lib/components/SharePopup.svelte';
  
  export let data;
  
  // Imagen por defecto en base64 (un placeholder gris simple)
  const DEFAULT_IMAGE_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
  
  // Función para obtener la URL correcta de la imagen
  function getImageUrl(photoUrl) {
    if (!photoUrl) return DEFAULT_IMAGE_BASE64;
    return `/images/movies/${photoUrl}`;
  }
  
  // Estado
  let movie = null;
  let loading = true;
  let error = null;
  let screenings = [];
  let activeTab = 'info'; // 'info', 'screenings'
  let showSharePopup = false;
  
  // Función para formatear la fecha
  function formatDate(dateString) {
    if (!dateString) return 'Fecha no disponible';
    try {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }).format(date);
    } catch (e) {
      console.error('Error formateando fecha:', e);
      return 'Fecha inválida';
    }
  }
  
  // Función para formatear duración
  function formatDuration(minutes) {
    if (!minutes) return 'Duración no disponible';
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return `${hours}h ${mins}m`;
  }
  
  // Función para agrupar proyecciones por fecha
  function groupScreeningsByDate(screenings) {
    return screenings.reduce((groups, screening) => {
      const date = screening.date;
      if (!groups[date]) {
        groups[date] = [];
      }
      groups[date].push(screening);
      return groups;
    }, {});
  }

  // Función para formatear hora
  function formatTime(timeString) {
    if (!timeString) return 'N/A';
    try {
      const [hours, minutes] = timeString.split(':');
      return `${hours}:${minutes}`;
    } catch (e) {
      console.error('Error formateando hora:', e);
      return timeString;
    }
  }
  
  // Cargar datos de la película
  async function loadMovieData() {
    try {
      loading = true;
      error = null;
      
      const response = await fetch(`${API_URL}/movies/${data.slug}`, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      const result = await response.json();
      
      if (response.ok && result.success) {
        movie = result.data;
        console.log('Movie data:', movie);
        
        // Cargar proyecciones si está disponible
        if (movie.id) {
          loadScreenings(movie.id);
        }
      } else {
        error = result.message || 'No se pudo cargar la información de la película';
      }
    } catch (e) {
      console.error('Error de conexión:', e);
      error = 'Error de conexión con el servidor';
    } finally {
      loading = false;
    }
  }
  
  // Cargar proyecciones
  async function loadScreenings(movieId) {
    try {
      const response = await fetch(`${API_URL}/movies/${movieId}/screenings`, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      const result = await response.json();
      
      if (response.ok && result.success) {
        screenings = result.data;
      } else {
        console.error('Error cargando proyecciones:', result.message);
      }
    } catch (e) {
      console.error('Error de conexión al cargar proyecciones:', e);
    }
  }

  function goBack() {
    history.back();
  }
  
  function shareMovie() {
    showSharePopup = true;
  }
  
  // Sistema de notificaciones toast
  let toast = { visible: false, message: '', type: 'info' };
  
  function showToast(message, type = 'info') {
    toast = { visible: true, message, type };
    setTimeout(() => {
      toast = { ...toast, visible: false };
    }, 3000);
  }
  
  // Añadir a favoritos
  function addToFavorites() {
    // Aquí iría la lógica para añadir a favoritos
    showToast(`${movie.title} añadida a favoritos`);
  }
  
  onMount(() => {
    loadMovieData();
  });
</script>

<!-- Diseño principal -->
<div class="container-fluid p-0">
  {#if loading}
    <div class="min-vh-50 d-flex align-items-center justify-content-center py-5" in:fade>
      <div class="spinner-border text-light me-3" role="status"></div>
      <span class="text-light">Cargando información de la película...</span>
    </div>
  {:else if error}
    <div class="min-vh-50 d-flex align-items-center justify-content-center py-5" in:fade>
      <div class="card bg-dark text-white border-danger w-100 max-w-md">
        <div class="card-header bg-danger bg-opacity-25 text-danger">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          Error
        </div>
        <div class="card-body text-center">
          <p class="mb-4">{error}</p>
          <button 
            class="btn btn-outline-light" 
            on:click={goBack}
          >
            <i class="bi bi-arrow-left me-2"></i>
            Volver
          </button>
        </div>
      </div>
    </div>
  {:else if movie}
    <!-- Fondo con efecto parallax -->
    <div class="position-fixed top-0 start-0 w-100 h-100 -z-10">
      {#if movie.photo_url}
        <div class="position-absolute inset-0 bg-gradient-to-b from-black/80 via-black/95 to-black"></div>
        <img 
          src={getImageUrl(movie.photo_url)} 
          alt="" 
          class="w-100 h-100 object-cover opacity-20"
          style="object-position: center 20%;"
          on:error={(e) => {
            if (e.target) {
              e.target.src = DEFAULT_IMAGE_BASE64;
              e.target.onerror = null;
            }
          }}
        />
      {:else}
        <div class="position-absolute inset-0 bg-black"></div>
      {/if}
    </div>
    
    <!-- Contenido principal -->
    <div class="container py-4" in:fade={{ duration: 300, delay: 150 }}>
      <!-- Botón de regreso -->
      <button 
        class="btn btn-sm btn-outline-light mb-4 opacity-75 hover:opacity-100" 
        on:click={goBack}
      >
        <i class="bi bi-arrow-left me-1"></i>
        Volver a cartelera
      </button>
      
      <!-- Información de la película -->
      <div class="row">
        <!-- Columna izquierda con poster y acciones -->
        <div class="col-md-3">
          <div class="card bg-dark border-0 mb-4">
            <div class="position-relative">
              <img 
                src={getImageUrl(movie.photo_url) || DEFAULT_IMAGE_BASE64} 
                alt={movie.title}
                class="card-img-top movie-poster"
                on:error={(e) => {
                  if (e.target) {
                    e.target.src = DEFAULT_IMAGE_BASE64;
                    e.target.onerror = null;
                  }
                }}
              />
              {#if movie.rating}
                <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                  {movie.rating}
                </span>
              {/if}
            </div>
            
            <!-- Acciones -->
            <div class="card-body">
              <div class="d-grid gap-2">
                {#if screenings.length > 0}
                  <a 
                    href={`/booking/${screenings[0].id}`} 
                    class="btn btn-success d-flex align-items-center justify-content-center"
                  >
                    <i class="bi bi-ticket-perforated me-2"></i>
                    <span>Comprar entradas</span>
                  </a>
                {/if}
                
                <button 
                  class="btn btn-outline-light d-flex align-items-center justify-content-center" 
                  on:click={addToFavorites}
                >
                  <i class="bi bi-heart me-2"></i>
                  <span>Añadir a favoritos</span>
                </button>
                
                <button 
                  class="btn btn-primary d-flex align-items-center justify-content-center"
                  type="button"
                  on:click={shareMovie}
                >
                  <i class="bi bi-share-fill me-2"></i>
                  <span>Compartir</span>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Columna derecha con información -->
        <div class="col-md-9">
          <div class="card bg-dark border-0">
            <div class="card-body">
              <h1 class="display-6 fw-bold mb-2">{movie.title}</h1>
              
              <div class="d-flex flex-wrap align-items-center text-muted mb-4">
                {#if movie.release_date}
                  <span class="me-3">
                    <i class="bi bi-calendar3 me-1"></i>
                    {formatDate(movie.release_date)}
                  </span>
                {/if}
                
                {#if movie.duration}
                  <span class="me-3">
                    <i class="bi bi-clock me-1"></i>
                    {formatDuration(movie.duration)}
                  </span>
                {/if}
                
                {#if movie.directors}
                  <span>
                    <i class="bi bi-camera-reels me-1"></i>
                    {movie.directors}
                  </span>
                {/if}
              </div>
              
              <!-- Pestañas -->
              <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                  <button 
                    class="nav-link {activeTab === 'info' ? 'active' : ''}" 
                    on:click={() => activeTab = 'info'}
                  >
                    <i class="bi bi-info-circle me-1"></i>
                    Información
                  </button>
                </li>
                <li class="nav-item">
                  <button 
                    class="nav-link {activeTab === 'screenings' ? 'active' : ''}" 
                    on:click={() => activeTab = 'screenings'}
                  >
                    <i class="bi bi-calendar2-week me-1"></i>
                    Proyecciones
                  </button>
                </li>
              </ul>
              
              <!-- Contenido de pestañas -->
              {#if activeTab === 'info'}
                <div class="tab-content" in:fade={{ duration: 200 }}>
                  <div class="mb-4">
                    <h5 class="mb-3">Sinopsis</h5>
                    <p class="card-text">{movie.synopsis || 'No hay sinopsis disponible para esta película.'}</p>
                  </div>
                  
                  {#if movie.genres && movie.genres.length > 0}
                    <div class="mb-4">
                      <h5 class="mb-3">Géneros</h5>
                      <div class="d-flex flex-wrap gap-2">
                        {#each movie.genres as genre}
                          <span class="badge bg-primary">{genre.name}</span>
                        {/each}
                      </div>
                    </div>
                  {/if}
                  
                  {#if movie.actors && movie.actors.length > 0}
                    <div class="mb-4">
                      <h5 class="mb-3">Reparto</h5>
                      <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                        {#each movie.actors as actor}
                          <div class="col">
                            <a 
                              href="/actors/{actor.slug}" 
                              class="text-decoration-none"
                            >
                              <div class="card bg-dark actor-card">
                                <div class="card-body text-center p-3">
                                  <div class="actor-initials mb-2">
                                    {actor.name.split(' ').map(word => word[0]).join('').toUpperCase()}
                                  </div>
                                  <h4 class="card-title h6 text-white mb-0">
                                    {actor.name}
                                  </h4>
                                </div>
                              </div>
                            </a>
                          </div>
                        {/each}
                      </div>
                    </div>
                  {/if}
                </div>
              {:else if activeTab === 'screenings'}
                <div class="tab-content" in:fade={{ duration: 200 }}>
                  {#if screenings.length > 0}
                    {#each Object.entries(groupScreeningsByDate(screenings)) as [date, dateScreenings]}
                      <div class="mb-4">
                        <h5 class="mb-3">
                          <i class="bi bi-calendar-date me-2"></i>
                          {formatDate(date)}
                        </h5>
                        <div class="row g-3">
                          {#each dateScreenings as screening}
                            <div class="col-md-6 col-lg-4">
                              <div class="card bg-dark screening-card h-100">
                                <div class="card-body">
                                  <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                      <h6 class="mb-1">
                                        <i class="bi bi-clock me-2"></i>
                                        {formatTime(screening.time)}
                                      </h6>
                                      <p class="mb-0 text-muted small">
                                        {screening.room?.cinema?.name || 'N/A'} - Sala {screening.room?.name || 'N/A'}
                                      </p>
                                    </div>
                                    {#if screening.is_3d}
                                      <span class="badge bg-info">3D</span>
                                    {/if}
                                  </div>
                                  
                                  <div class="d-flex justify-content-between align-items-center">
                                    <div class="price-tag">
                                      {#if screening.price}
                                        <span class="fs-5 fw-bold">
                                          {new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(screening.price)}
                                        </span>
                                      {:else}
                                        <span class="text-muted">Precio no disponible</span>
                                      {/if}
                                    </div>
                                    <a 
                                      href={`/booking/${screening.id}`} 
                                      class="btn btn-primary btn-sm"
                                    >
                                      Reservar
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          {/each}
                        </div>
                      </div>
                    {/each}
                  {:else}
                    <div class="alert alert-info">
                      <i class="bi bi-info-circle-fill me-2"></i>
                      No hay proyecciones disponibles para esta película.
                    </div>
                  {/if}
                </div>
              {/if}
            </div>
          </div>
        </div>
      </div>
    </div>
  {:else}
    <div class="min-vh-50 d-flex align-items-center justify-content-center py-5" in:fade>
      <div class="card bg-dark text-white border-secondary w-100 max-w-md">
        <div class="card-body text-center">
          <p class="mb-4">No se encontró la película solicitada.</p>
          <button 
            class="btn btn-outline-light" 
            on:click={goBack}
          >
            <i class="bi bi-arrow-left me-2"></i>
            Volver
          </button>
        </div>
      </div>
    </div>
  {/if}
  
  <!-- Toast de notificación -->
  {#if toast.visible}
    <div 
      class="position-fixed bottom-0 end-0 p-3"
      style="z-index: 5"
      in:fly={{ y: 50, duration: 300 }}
      out:fade
    >
      <div class="toast show bg-dark text-white border-{toast.type === 'error' ? 'danger' : 'success'}">
        <div class="toast-header bg-dark text-white border-bottom border-secondary">
          <i class="bi bi-{toast.type === 'error' ? 'exclamation-circle-fill text-danger' : 'check-circle-fill text-success'} me-2"></i>
          <strong class="me-auto">Kaizen Cinema</strong>
          <button type="button" class="btn-close btn-close-white" on:click={() => toast.visible = false}></button>
        </div>
        <div class="toast-body">
          {toast.message}
        </div>
      </div>
    </div>
  {/if}
</div>

<!-- Share Popup -->
<SharePopup 
  bind:show={showSharePopup}
  title={movie?.title || ''}
  url={window.location.href}
  on:toast={({ detail }) => showToast(detail.message, detail.type)}
/>

<style>
  /* Estilos adicionales */
  .min-vh-50 {
    min-height: 50vh;
  }
  
  .max-w-md {
    max-width: 500px;
  }
  
  .object-cover {
    object-fit: cover;
  }
  
  .backdrop-blur-md {
    backdrop-filter: blur(12px);
  }
  
  .-z-10 {
    z-index: -10;
  }
  
  /* Estilos para el poster */
  .movie-poster {
    width: 100%;
    max-width: 300px;
    height: auto;
    aspect-ratio: 2/3;
    object-fit: cover;
    border-radius: 8px;
    margin: 0 auto;
    display: block;
  }
  
  /* Estilos para las pestañas */
  .nav-tabs {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .nav-tabs .nav-link {
    color: rgba(255, 255, 255, 0.75);
    border: none;
    border-bottom: 2px solid transparent;
    padding: 0.5rem 1rem;
    margin-right: 1rem;
    background: none;
  }
  
  .nav-tabs .nav-link:hover {
    color: white;
    border-bottom-color: rgba(255, 255, 255, 0.5);
  }
  
  .nav-tabs .nav-link.active {
    color: white;
    background: none;
    border-bottom: 2px solid var(--bs-primary);
  }
  
  /* Estilos para las tarjetas de actores */
  .actor-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .actor-card:hover {
    transform: translateY(-5px);
    border-color: var(--bs-primary);
  }
  
  .actor-initials {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: bold;
    color: white;
    margin: 0 auto;
  }
  
  /* Estilos para las tarjetas de proyecciones */
  .screening-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .screening-card:hover {
    transform: translateY(-5px);
    border-color: var(--bs-primary);
  }
  
  .price-tag {
    color: var(--bs-success);
  }
  
  /* Ajustes generales */
  .card {
    background: rgba(33, 37, 41, 0.95);
  }
  
  .tab-content {
    min-height: 200px;
  }
</style>