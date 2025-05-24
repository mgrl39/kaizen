<script lang="ts">
  import { onMount } from 'svelte';
  import type { Booking } from '$lib/types';
  import { t } from '$lib/i18n';
  import HeroBanner from '$lib/components/HeroBanner.svelte';
  import { API_URL } from '$lib/config';
  import { page } from '$app/stores';

  // Estado para las reservas
  let bookings: Booking[] = [];
  let loading: boolean = true;
  let error: string | null = null;
  
  // Datos para el hero banner
  const heroData = {
    title: "Mis Reservas",
    subtitle: "Gestiona tus reservas de películas y proyecciones",
    imageUrl: "https://source.unsplash.com/1200x600/?tickets,cinema",
    overlayOpacity: "50"
  };

  // Imagen por defecto en base64 (un placeholder gris simple)
  const DEFAULT_IMAGE_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

  // Función para obtener la URL correcta de la imagen
  function getImageUrl(photoUrl) {
    if (!photoUrl) return DEFAULT_IMAGE_BASE64;
    
    // Si ya es una URL completa
    if (photoUrl.startsWith('http')) {
      return photoUrl;
    }
    
    // Si es solo el nombre del archivo
    if (!photoUrl.includes('/')) {
      return `${API_URL}/images/${photoUrl}`;
    }
    
    // Si ya tiene una ruta parcial
    return `${API_URL}/images/${photoUrl}`;
  }

  // Función para manejar errores de carga de imagen
  function handleImageError(event) {
    if (event.target) {
      event.target.src = DEFAULT_IMAGE_BASE64;
      event.target.onerror = null;
    }
  }

  onMount(async () => {
    try {
      // Obtener las reservas del usuario actual
      const token = localStorage.getItem('token');
      if (!token) {
        throw new Error('No hay sesión activa');
      }

      const response = await fetch(`${API_URL}/bookings`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      
      if (!response.ok) {
        throw new Error(`API respondió con estado: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data && data.success && Array.isArray(data.data)) {
        bookings = data.data;
        console.log('Reservas cargadas:', bookings);
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
      
      loading = false;
    } catch (err) {
      console.error('Error cargando reservas:', err);
      error = `Error cargando reservas: ` + (err instanceof Error ? err.message : String(err));
      loading = false;
    }
  });

  // Estado para filtros
  let searchQuery = '';
  let sortBy = 'date'; // 'date', 'movie', 'cinema'
  
  // Reservas filtradas
  $: filteredBookings = bookings
    .filter(booking => {
      const searchLower = searchQuery.toLowerCase();
      
      // Filtrar por búsqueda (título de película, nombre del cine)
      if (searchQuery && !booking.movie.title.toLowerCase().includes(searchLower) && 
          !booking.cinema.name.toLowerCase().includes(searchLower)) {
        return false;
      }
      
      return true;
    })
    .sort((a, b) => {
      if (sortBy === 'date') {
        return new Date(b.date).getTime() - new Date(a.date).getTime();
      } else if (sortBy === 'movie') {
        return a.movie.title.localeCompare(b.movie.title);
      } else if (sortBy === 'cinema') {
        return a.cinema.name.localeCompare(b.cinema.name);
      }
      return 0;
    });

  // Formatear fecha y hora
  function formatDate(dateString) {
    const options = { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('es-ES', options);
  }

  // Estado para confirmación de cancelación
  let showCancelConfirmation = false;
  let bookingToCancel: Booking | null = null;

  // Función para mostrar diálogo de confirmación
  function showCancelDialog(booking: Booking) {
    bookingToCancel = booking;
    showCancelConfirmation = true;
  }

  // Función para cancelar reserva
  async function cancelBooking() {
    if (!bookingToCancel) return;

    try {
      const token = localStorage.getItem('token');
      if (!token) {
        throw new Error('No hay sesión activa');
      }

      const response = await fetch(`${API_URL}/bookings/${bookingToCancel.id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });

      if (!response.ok) {
        throw new Error(`Error al cancelar reserva: ${response.status}`);
      }

      // Actualizar la lista de reservas
      bookings = bookings.filter(b => b.id !== bookingToCancel.id);
      showCancelConfirmation = false;
      bookingToCancel = null;
    } catch (err) {
      console.error('Error al cancelar reserva:', err);
      alert(`Error al cancelar reserva: ${err instanceof Error ? err.message : String(err)}`);
    }
  }

  // Función para cerrar diálogo de confirmación
  function closeCancelDialog() {
    showCancelConfirmation = false;
    bookingToCancel = null;
  }
</script>

<!-- Hero Banner con imagen de fondo para las reservas -->
<HeroBanner 
  title="Mis Reservas"
  subtitle="Gestiona tus reservas de películas y proyecciones"
  imageUrl="https://source.unsplash.com/random/1920x1080/?tickets,cinema,popcorn"
  on:error={(e) => {
    if (e.target) {
      e.target.src = DEFAULT_IMAGE_BASE64;
      e.target.onerror = null;
    }
  }}
/>

<!-- Contenido principal -->
<div class="container py-5">
  <!-- Mensaje de carga -->
  {#if loading}
    <div class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>
  {:else if error}
    <div class="alert alert-danger">
      {error}
    </div>
  {:else if bookings.length === 0}
    <div class="text-center py-5">
      <i class="bi bi-ticket-perforated fs-1 text-muted mb-3"></i>
      <h4>No tienes reservas</h4>
      <p class="text-muted">¡Empieza a reservar tus películas favoritas!</p>
      <a href="/movies" class="btn btn-primary">
        <i class="bi bi-film me-2"></i>
        Ver Películas
      </a>
    </div>
  {/if}

  <!-- Filtros -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-gradient">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white">Filtros</h5>
            <button class="btn btn-sm btn-outline-light" on:click={() => {
              searchQuery = '';
              sortBy = 'date';
            }}>
              <i class="bi bi-funnel me-1"></i>
              Limpiar filtros
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-search"></i>
                </span>
                <input 
                  type="text" 
                  class="form-control" 
                  placeholder="Buscar película o cine..."
                  bind:value={searchQuery}
                />
              </div>
            </div>
            <div class="col-12 col-md-6">
              <select 
                class="form-select" 
                bind:value={sortBy}
              >
                <option value="date">Fecha de proyección</option>
                <option value="movie">Película</option>
                <option value="cinema">Cine</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Lista de reservas -->
  <div class="row g-4">
    {#each filteredBookings as booking}
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="position-relative">
            <img 
              src={getImageUrl(booking.movie.photo_url)}
              alt={booking.movie.title}
              class="card-img-top"
              on:error={handleImageError}
            />
            <div class="position-absolute top-0 start-0 p-3">
              <span class="badge bg-primary">
                {booking.movie.genre}
              </span>
            </div>
          </div>
          <div class="card-body">
            <h5 class="card-title mb-1">{booking.movie.title}</h5>
            <small class="text-muted">
              <i class="bi bi-clock me-1"></i>
              {formatDate(booking.date)}
            </small>
            <p class="card-text mt-3 mb-4">
              <strong>Cine:</strong> {booking.cinema.name}<br>
              <strong>Sala:</strong> {booking.room.name}<br>
              <strong>Asientos:</strong> {booking.seats.join(', ')}
            </p>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="badge bg-success">
                  <i class="bi bi-check-circle me-1"></i>
                  Reserva confirmada
                </span>
              </div>
              <div class="dropdown">
                <button 
                  class="btn btn-sm btn-outline-danger" 
                  type="button"
                  on:click={() => showCancelDialog(booking)}
                >
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    {/each}
  </div>
</div>

<!-- Modal de confirmación de cancelación -->
{#if showCancelConfirmation}
  <div class="modal fade show" tabindex="-1" style="display: block;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>
            Cancelar Reserva
          </h5>
          <button 
            type="button" 
            class="btn-close" 
            on:click={closeCancelDialog}
          ></button>
        </div>
        <div class="modal-body">
          <p>
            ¿Estás seguro de que quieres cancelar la reserva para 
            <strong>{bookingToCancel?.movie.title}</strong>?
          </p>
          <p class="text-muted">
            Esta acción no se puede deshacer.
          </p>
        </div>
        <div class="modal-footer">
          <button 
            type="button" 
            class="btn btn-secondary"
            on:click={closeCancelDialog}
          >
            Cancelar
          </button>
          <button 
            type="button" 
            class="btn btn-danger"
            on:click={cancelBooking}
          >
            Sí, cancelar reserva
          </button>
        </div>
      </div>
    </div>
  </div>
{/if}

<style>
  /* Variables de colores personalizadas */
  :root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --primary-light: #3b82f6;
    --secondary-color: #1e40af;
    --accent-color: #10b981;
    --accent-hover: #059669;
  }

  .bg-gradient {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
  }

  .card {
    transition: transform 0.3s ease;
  }

  .card:hover {
    transform: translateY(-5px);
  }

  .card-img-top {
    height: 200px;
    object-fit: cover;
  }

  .badge {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
  }

  .badge.bg-primary {
    background-color: var(--primary-color);
  }

  .badge:hover {
    background-color: var(--primary-hover);
  }

  .btn-outline-danger {
    color: var(--accent-color);
    border-color: var(--accent-color);
  }

  .btn-outline-danger:hover {
    color: white;
    background-color: var(--accent-color);
  }
</style>