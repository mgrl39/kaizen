<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { fade, fly } from 'svelte/transition';
  import SharePopup from '$lib/components/SharePopup.svelte';
  
  export let data;
  
  // Imagen por defecto en base64 (un placeholder gris simple)
  const DEFAULT_IMAGE_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
  
  // Estado
  let movie = null;
  let loading = true;
  let error = null;
  let screenings = [];
  let activeTab = 'info'; // 'info', 'screenings', 'reviews'
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
          src={movie.photo_url} 
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
        class="btn btn-sm btn-outline-light mb-3 opacity-75 hover:opacity-100" 
        on:click={goBack}
      >
        <i class="bi bi-arrow-left me-1"></i>
        Volver a cartelera
      </button>
      
      <!-- Información de la película -->
      <div class="card bg-dark bg-opacity-50 border-0 backdrop-blur-md overflow-hidden">
        <div class="row g-0">
          <!-- Poster -->
          <div class="col-md-4 col-lg-3">
            <div class="position-relative h-100">
              <img 
                src={movie.photo_url || DEFAULT_IMAGE_BASE64} 
                alt={movie.title}
                class="w-100 h-100 object-cover"
                style="min-height: 300px;"
                on:error={(e) => {
                  if (e.target) {
                    e.target.src = DEFAULT_IMAGE_BASE64;
                    e.target.onerror = null;
                  }
                }}
              />
              {#if movie.rating}
                <span class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark fs-6">
                  <i class="bi bi-star-fill me-1"></i>
                  {movie.rating}
                </span>
              {/if}
            </div>
          </div>
          
          <!-- Información -->
          <div class="col-md-8 col-lg-9">
            <div class="card-body p-4">
              <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
                <div>
                  <h1 class="card-title display-6 fw-bold mb-1">{movie.title}</h1>
                  <div class="d-flex flex-wrap align-items-center text-muted mb-3">
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
                  </div>
                </div>
              </div>
              
              <!-- Pestañas -->
              <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                  <button 
                    class="nav-link {activeTab === 'info' ? 'active bg-dark bg-opacity-50 text-white' : 'text-light text-opacity-75'}" 
                    on:click={() => activeTab = 'info'}
                  >
                    <i class="bi bi-info-circle me-1"></i>
                    Información
                  </button>
                </li>
                <li class="nav-item">
                  <button 
                    class="nav-link {activeTab === 'screenings' ? 'active bg-dark bg-opacity-50 text-white' : 'text-light text-opacity-75'}" 
                    on:click={() => activeTab = 'screenings'}
                  >
                    <i class="bi bi-calendar2-week me-1"></i>
                    Proyecciones
                  </button>
                </li>
                <li class="nav-item">
                  <button 
                    class="nav-link {activeTab === 'reviews' ? 'active bg-dark bg-opacity-50 text-white' : 'text-light text-opacity-75'}" 
                    on:click={() => activeTab = 'reviews'}
                  >
                    <i class="bi bi-chat-quote me-1"></i>
                    Reseñas
                  </button>
                </li>
              </ul>
              
              <!-- Contenido de pestañas -->
              {#if activeTab === 'info'}
                <div in:fade={{ duration: 200 }}>
                  <h5 class="mb-3">Sinopsis</h5>
                  <p class="card-text">{movie.synopsis || 'No hay sinopsis disponible para esta película.'}</p>
                  
                  {#if movie.genres && movie.genres.length > 0}
                    <h5 class="mt-4 mb-2">Géneros</h5>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                      {#each movie.genres as genre}
                        <span class="badge bg-primary">{genre}</span>
                      {/each}
                    </div>
                  {/if}
                  
                  <div class="row mt-4">
                    {#if movie.id}
                      <div class="col-md-4 mb-3">
                        <h6 class="text-muted">ID</h6>
                        <p>{movie.id}</p>
                      </div>
                    {/if}
                    
                    {#if movie.created_at}
                      <div class="col-md-4 mb-3">
                        <h6 class="text-muted">Añadida el</h6>
                        <p>{formatDate(movie.created_at)}</p>
                      </div>
                    {/if}
                  </div>
                </div>
              {:else if activeTab === 'screenings'}
                <div in:fade={{ duration: 200 }}>
                  {#if screenings.length > 0}
                    <div class="table-responsive">
                      <table class="table table-dark table-hover">
                        <thead>
                          <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Sala</th>
                            <th>Cine</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          {#each screenings as screening}
                            <tr>
                              <td>{formatDate(screening.date)}</td>
                              <td>{screening.time}</td>
                              <td>{screening.room?.name || 'N/A'}</td>
                              <td>{screening.room?.cinema?.name || 'N/A'}</td>
                              <td>
                                {#if screening.price}
                                  {new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(screening.price)}
                                {:else}
                                  N/A
                                {/if}
                              </td>
                              <td>
                                <a href={`/booking/${screening.id}`} class="btn btn-sm btn-primary">
                                  Reservar
                                </a>
                              </td>
                            </tr>
                          {/each}
                        </tbody>
                      </table>
                    </div>
                  {:else}
                    <div class="alert alert-info">
                      <i class="bi bi-info-circle-fill me-2"></i>
                      No hay proyecciones disponibles para esta película.
                    </div>
                  {/if}
                </div>
              {:else if activeTab === 'reviews'}
                <div in:fade={{ duration: 200 }}>
                  <div class="alert alert-secondary">
                    <i class="bi bi-chat-left-text me-2"></i>
                    Las reseñas estarán disponibles próximamente.
                  </div>
                  
                  <!-- Formulario para añadir reseña -->
                  <div class="card bg-dark bg-opacity-50 mt-4">
                    <div class="card-header">
                      <h5 class="mb-0">Añadir reseña</h5>
                    </div>
                    <div class="card-body">
                      <form>
                        <div class="mb-3">
                          <label for="rating" class="form-label">Valoración</label>
                          <div class="d-flex">
                            {#each Array(5) as _, i}
                              <button type="button" class="btn btn-link text-warning p-0 me-2 fs-4">
                                <i class="bi bi-star"></i>
                              </button>
                            {/each}
                          </div>
                        </div>
                        <div class="mb-3">
                          <label for="comment" class="form-label">Comentario</label>
                          <textarea 
                            id="comment" 
                            class="form-control bg-dark text-white border-secondary" 
                            rows="3" 
                            placeholder="Escribe tu opinión sobre la película..."
                          ></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                          Enviar reseña
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              {/if}
              
              <!-- Acciones -->
              <div class="d-flex flex-wrap gap-2 mt-4">
                <button 
                  class="btn btn-outline-light d-flex align-items-center" 
                  on:click={addToFavorites}
                >
                  <i class="bi bi-heart me-2"></i>
                  <span>Añadir a favoritos</span>
                </button>
                
                <div class="dropdown">
                  <button 
                    class="btn btn-primary d-flex align-items-center justify-content-center share-btn"
                    type="button"
                    on:click={shareMovie}
                  >
                    <i class="bi bi-share-fill"></i>
                  </button>
                </div>
                
                {#if screenings.length > 0}
                  <a 
                    href={`/booking/${screenings[0].id}`} 
                    class="btn btn-success d-flex align-items-center"
                  >
                    <i class="bi bi-ticket-perforated me-2"></i>
                    <span>Comprar entradas</span>
                  </a>
                {/if}
              </div>
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
  on:toast={({ detail }) => showToast('', detail.type, detail.icon)}
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
  
  /* Mejoras para las pestañas */
  .nav-tabs {
    border-bottom-color: rgba(255, 255, 255, 0.2);
  }
  
  .nav-tabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    border-radius: 0;
    padding: 0.5rem 1rem;
    margin-right: 0.5rem;
    transition: all 0.2s ease;
  }
  
  .nav-tabs .nav-link:hover:not(.active) {
    border-color: rgba(255, 255, 255, 0.3);
    background-color: rgba(255, 255, 255, 0.05);
  }
  
  .nav-tabs .nav-link.active {
    border-bottom: 2px solid #0d6efd;
  }
  
  /* Ajustes para tablas */
  .table-dark {
    background-color: rgba(33, 37, 41, 0.6);
  }

  .share-btn {
    width: 42px;
    height: 42px;
    padding: 0;
    border-radius: 50%;
    transition: all 0.2s ease;
  }

  .share-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(var(--bs-primary-rgb), 0.3);
  }

  .share-btn i {
    font-size: 1.2rem;
  }
</style>