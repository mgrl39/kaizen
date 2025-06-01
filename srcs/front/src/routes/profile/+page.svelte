<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';
  import type { Booking } from '$lib/types';
  import { t } from '$lib/i18n';

  // Función para formatear fecha y hora
  function formatDateTime(dateString) {
    const options = { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('es-ES', options);
  }

  function formatDate(date: Date | string): string {
    const d = typeof date === 'string' ? new Date(date) : date;
    return new Intl.DateTimeFormat('es-ES', { 
      day: '2-digit', 
      month: '2-digit', 
      year: 'numeric' 
    }).format(d);
  }

  // Estado para el perfil
  let loading = true;
  let error: string | null = null;
  let success: string | null = null;
  let user: { username: string; name: string; email: string; created_at?: string; avatar?: string; loyaltyPoints?: number } | null = null;
  let editing = false;
  let formData = {
    username: '',
    name: '',
    email: '',
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
  };

  // Estado para las reservas
  let bookings: Booking[] = [];
  let loadingBookings = true;
  let bookingsError: string | null = null;
  let searchQuery = '';
  let sortBy = 'date';
  let showCancelConfirmation = false;
  let bookingToCancel: Booking | null = null;

  // Imagen por defecto
  const DEFAULT_IMAGE_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

  async function fetchProfile() {
    const token = localStorage.getItem('token');
    if (!token) {
      goto('/login');
      return;
    }
    try {
      const response = await fetch(`${API_URL}/profile`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
      const data = await response.json();
      if (response.ok && data.success) {
        user = data.user;
        formData.username = data.user.username;
        formData.name = data.user.name;
        formData.email = data.user.email;
      } else {
        error = data.message || 'No autorizado';
        localStorage.removeItem('token');
        goto('/login');
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
      localStorage.removeItem('token');
      goto('/login');
    } finally {
      loading = false;
    }
  }

  async function updateProfile() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
      const response = await fetch(`${API_URL}/profile`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          username: formData.username,
          name: formData.name,
          email: formData.email
        })
      });

      const data = await response.json();
      if (response.ok && data.success) {
        user = data.user;
        success = data.message;
        editing = false;
      } else {
        error = data.message || 'Error al actualizar el perfil';
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
    }
  }

  async function changePassword() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
      const response = await fetch(`${API_URL}/profile/password`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          current_password: formData.current_password,
          new_password: formData.new_password,
          new_password_confirmation: formData.new_password_confirmation
        })
      });

      const data = await response.json();
      if (response.ok && data.success) {
        success = data.message;
        formData.current_password = '';
        formData.new_password = '';
        formData.new_password_confirmation = '';
      } else {
        error = data.message || 'Error al cambiar la contraseña';
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
    }
  }

  // Función para obtener las reservas
  async function fetchBookings() {
    try {
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
      } else {
        throw new Error('Formato de respuesta API inesperado');
      }
    } catch (err) {
      console.error('Error cargando reservas:', err);
      bookingsError = `Error cargando reservas: ` + (err instanceof Error ? err.message : String(err));
    } finally {
      loadingBookings = false;
    }
  }

  // Función para mostrar diálogo de cancelación
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
      error = `Error al cancelar reserva: ${err instanceof Error ? err.message : String(err)}`;
    }
  }

  // Función para cerrar diálogo de confirmación
  function closeCancelDialog() {
    showCancelConfirmation = false;
    bookingToCancel = null;
  }

  // Función para obtener URL de imagen
  function getImageUrl(photoUrl: string) {
    if (!photoUrl) return DEFAULT_IMAGE_BASE64;
    
    if (photoUrl.startsWith('http')) {
      return photoUrl;
    }
    
    if (!photoUrl.includes('/')) {
      return `${API_URL}/images/${photoUrl}`;
    }
    
    return `${API_URL}/images/${photoUrl}`;
  }

  // Función para manejar errores de imagen
  function handleImageError(event: Event) {
    if (event.target) {
      (event.target as HTMLImageElement).src = DEFAULT_IMAGE_BASE64;
      (event.target as HTMLImageElement).onerror = null;
    }
  }

  // Reservas filtradas
  $: filteredBookings = bookings
    .filter(booking => {
      const searchLower = searchQuery.toLowerCase();
      return !searchQuery || 
             booking.movie.title.toLowerCase().includes(searchLower) || 
             booking.cinema.name.toLowerCase().includes(searchLower);
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

  onMount(() => {
    fetchProfile();
    fetchBookings();
  });
</script>

<div class="profile-container">
  <!-- Header Section -->
  <div class="profile-header mb-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-auto">
          <div class="header-icon-wrapper">
            <i class="bi bi-person-circle"></i>
          </div>
        </div>
        <div class="col">
          <h1 class="display-6 mb-0">{user?.name || 'Cargando...'}</h1>
          <p class="text-muted mb-2">@{user?.username}</p>
          <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary-subtle text-primary">
              <i class="bi bi-calendar3 me-1"></i>
              Miembro desde {formatDate(new Date(user?.created_at || Date.now()))}
            </span>
            {#if user?.loyaltyPoints}
              <span class="badge bg-warning-subtle text-warning">
                <i class="bi bi-star-fill me-1"></i>
                {user.loyaltyPoints} puntos
              </span>
            {/if}
          </div>
        </div>
        {#if user}
          <div class="col-auto">
            <button 
              class="btn custom-btn {editing ? 'custom-btn-active' : ''}" 
              on:click={() => editing = !editing}
            >
              <i class="bi bi-{editing ? 'x-lg' : 'pencil-square'} me-2"></i>
              {editing ? 'Cancelar' : 'Editar Perfil'}
            </button>
          </div>
        {/if}
      </div>
    </div>
  </div>

  <div class="container">
    <!-- Alertas -->
    {#if error}
      <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {error}
      </div>
    {/if}
    {#if success}
      <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {success}
      </div>
    {/if}

    <div class="row g-4">
      <!-- Columna de Perfil -->
      <div class="col-lg-4">
        <div class="profile-cards">
          <div class="card card-hover">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-4">
                <h4 class="d-flex align-items-center m-0">
                  <div class="icon-wrapper">
                    <i class="bi bi-person-circle"></i>
                  </div>
                  Información Personal
                </h4>
                {#if user}
                  <button 
                    class="btn btn-light btn-sm" 
                    on:click={() => editing = true}
                  >
                    <i class="bi bi-pencil-square me-1"></i>
                    Editar
                  </button>
                {/if}
              </div>
              <div class="profile-info">
                <div class="info-item">
                  <span class="info-label">Nombre</span>
                  <span class="info-value">{user?.name || 'Cargando...'}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Username</span>
                  <span class="info-value">@{user?.username}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Email</span>
                  <span class="info-value">{user?.email}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Miembro desde</span>
                  <span class="info-value">{formatDate(new Date(user?.created_at || Date.now()))}</span>
                </div>
                {#if user?.loyaltyPoints}
                  <div class="info-item">
                    <span class="info-label">Puntos de Fidelidad</span>
                    <span class="info-value points">
                      <i class="bi bi-star-fill"></i>
                      {user.loyaltyPoints}
                    </span>
                  </div>
                {/if}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Columna de Reservas -->
      <div class="col-lg-8">
        <div class="card card-hover h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="d-flex align-items-center m-0">
                <i class="bi bi-ticket-perforated me-2"></i>
                Mis Reservas
              </h4>
              <div class="d-flex gap-2">
                <div class="search-box">
                  <input 
                    type="text" 
                    class="form-control form-control-sm" 
                    placeholder="Buscar reserva..."
                    bind:value={searchQuery}
                  />
                </div>
                <select 
                  class="form-select form-select-sm" 
                  bind:value={sortBy}
                >
                  <option value="date">Fecha</option>
                  <option value="movie">Película</option>
                  <option value="cinema">Cine</option>
                </select>
              </div>
            </div>

            {#if loadingBookings}
              <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Cargando...</span>
                </div>
              </div>
            {:else if bookingsError}
              <div class="alert alert-danger">
                {bookingsError}
              </div>
            {:else if bookings.length === 0}
              <div class="text-center py-4">
                <i class="bi bi-ticket-perforated display-4 text-muted mb-3"></i>
                <h5>No tienes reservas</h5>
                <p class="text-muted">¡Empieza a reservar tus películas favoritas!</p>
                <a href="/movies" class="btn btn-primary">
                  <i class="bi bi-film me-2"></i>
                  Ver Películas
                </a>
              </div>
            {:else}
              <div class="bookings-grid">
                {#each filteredBookings as booking}
                  <div class="booking-card">
                    <img 
                      src={getImageUrl(booking.movie.photo_url)}
                      alt={booking.movie.title}
                      class="booking-image"
                      on:error={handleImageError}
                    />
                    <div class="booking-content">
                      <h5 class="booking-title">{booking.movie.title}</h5>
                      <div class="booking-details">
                        <span><i class="bi bi-calendar3"></i> {formatDateTime(booking.date)}</span>
                        <span><i class="bi bi-building"></i> {booking.cinema.name}</span>
                        <span><i class="bi bi-door-open"></i> Sala {booking.room.name}</span>
                        <span><i class="bi bi-chair"></i> {booking.seats.join(', ')}</span>
                      </div>
                      <div class="booking-footer">
                        <span class="status-badge">
                          <i class="bi bi-check-circle"></i>
                          Confirmada
                        </span>
                        <button class="btn-cancel" on:click={() => showCancelDialog(booking)}>
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                {/each}
              </div>
            {/if}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de confirmación -->
{#if showCancelConfirmation}
  <div class="modal fade show" tabindex="-1" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title">
            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
            Cancelar Reserva
          </h5>
          <button type="button" class="btn-close" on:click={closeCancelDialog}></button>
        </div>
        <div class="modal-body">
          <p class="mb-1">¿Estás seguro de que quieres cancelar la reserva para</p>
          <p class="fw-bold mb-3">{bookingToCancel?.movie.title}?</p>
          <p class="text-muted small mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Esta acción no se puede deshacer.
          </p>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" on:click={closeCancelDialog}>
            Mantener Reserva
          </button>
          <button type="button" class="btn btn-danger" on:click={cancelBooking}>
            <i class="bi bi-trash me-1"></i>
            Sí, Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-backdrop fade show"></div>
{/if}

<!-- Modal de Edición -->
{#if editing && user}
  <div class="modal fade show" tabindex="-1" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title d-flex align-items-center">
            <div class="icon-wrapper icon-sm">
              <i class="bi bi-person-circle"></i>
            </div>
            Editar Perfil
          </h5>
          <button type="button" class="btn-close" on:click={() => editing = false}></button>
        </div>
        <div class="modal-body">
          <div class="mb-4">
            <form>
              <div class="form-floating mb-3">
                <input type="text" bind:value={formData.name} class="form-control" id="name" placeholder="Nombre"/>
                <label for="name">Nombre</label>
              </div>
              <div class="form-floating mb-3">
                <input type="text" bind:value={formData.username} class="form-control" id="username" placeholder="Username"/>
                <label for="username">Username</label>
              </div>
              <div class="form-floating mb-3">
                <input type="email" bind:value={formData.email} class="form-control" id="email" placeholder="Email"/>
                <label for="email">Email</label>
              </div>
            </form>
          </div>

          <div class="separator">
            <span>Cambiar Contraseña</span>
          </div>

          <form on:submit|preventDefault={changePassword}>
            <div class="form-floating mb-3">
              <input 
                type="password" 
                bind:value={formData.current_password} 
                class="form-control" 
                id="current_password" 
                placeholder="Contraseña Actual"
              />
              <label for="current_password">Contraseña Actual</label>
            </div>
            <div class="form-floating mb-3">
              <input 
                type="password" 
                bind:value={formData.new_password} 
                class="form-control" 
                id="new_password" 
                placeholder="Nueva Contraseña"
              />
              <label for="new_password">Nueva Contraseña</label>
            </div>
            <div class="form-floating mb-3">
              <input 
                type="password" 
                bind:value={formData.new_password_confirmation} 
                class="form-control" 
                id="new_password_confirmation" 
                placeholder="Confirmar Nueva Contraseña"
              />
              <label for="new_password_confirmation">Confirmar Nueva Contraseña</label>
            </div>
          </form>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" on:click={() => editing = false}>
            Cancelar
          </button>
          <button type="button" class="btn btn-primary" on:click={updateProfile}>
            <i class="bi bi-check2-circle me-2"></i>
            Guardar Cambios
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-backdrop fade show"></div>
{/if}

<style>
  .profile-container {
    padding-top: 2rem;
    padding-bottom: 4rem;
  }

  .profile-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    padding: 3rem 0;
    color: white;
    margin-top: -2rem;
    margin-bottom: 3rem;
    border-radius: 0 0 2.5rem 2.5rem;
    box-shadow: 0 10px 30px -10px rgba(99, 102, 241, 0.3);
  }

  .header-icon-wrapper {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
  }

  .header-icon-wrapper i {
    font-size: 2rem;
    color: white;
  }

  .header-icon-wrapper:hover {
    transform: scale(1.05);
  }

  .avatar-container {
    display: none;
  }

  .profile-cards {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .card {
    border: none;
    border-radius: 1rem;
    background: var(--bs-body-bg);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }

  .card-hover {
    transition: all 0.3s ease;
  }

  .card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }

  .icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-dark, #4338ca) 100%);
    margin-right: 1rem;
    box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
  }

  .icon-wrapper i {
    color: white;
    font-size: 1.25rem;
  }

  .profile-info .info-item {
    padding: 1rem;
    border-bottom: 1px solid var(--bs-border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background-color 0.2s ease;
  }

  .profile-info .info-item:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.03);
  }

  .profile-info .info-item:last-child {
    border-bottom: none;
  }

  .info-label {
    color: var(--bs-secondary-color);
    font-weight: 500;
  }

  .info-value {
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .info-value.points {
    color: var(--bs-warning);
  }

  .info-value.points i {
    font-size: 0.875rem;
  }

  .form-control {
    border-radius: 0.75rem;
    border: 1px solid rgba(var(--bs-primary-rgb), 0.2);
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
  }

  .form-control:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), 0.1);
  }

  .btn {
    border-radius: 0.75rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-dark, #4338ca) 100%);
    border: none;
    box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
  }

  .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 12px -2px rgba(99, 102, 241, 0.3);
  }

  /* Ajustes para el modo oscuro */
  :global([data-bs-theme="dark"]) {
    .card {
      background: rgba(17, 24, 39, 0.8);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .profile-info .info-item:hover {
      background-color: rgba(255, 255, 255, 0.03);
    }

    .form-control {
      background-color: rgba(17, 24, 39, 0.8);
      border-color: rgba(255, 255, 255, 0.1);
      color: var(--bs-body-color);
    }

    .form-control:focus {
      background-color: rgba(17, 24, 39, 1);
      border-color: var(--bs-primary);
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
    }

    .icon-wrapper {
      background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
  }

  .bookings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
  }

  .booking-card {
    background: var(--bs-body-bg);
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
  }

  .booking-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .booking-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
  }

  .booking-content {
    padding: 1rem;
  }

  .booking-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .booking-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
  }

  .booking-details span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--bs-secondary-color);
  }

  .booking-details i {
    color: var(--bs-primary);
  }

  .booking-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.5rem;
    background: rgba(var(--bs-success-rgb), 0.1);
    color: var(--bs-success);
    border-radius: 0.25rem;
    font-size: 0.75rem;
  }

  .btn-cancel {
    background: none;
    border: none;
    color: var(--bs-danger);
    padding: 0.25rem;
    border-radius: 0.25rem;
    transition: all 0.2s ease;
  }

  .btn-cancel:hover {
    background: rgba(var(--bs-danger-rgb), 0.1);
  }

  .search-box .form-control,
  .form-select {
    border-radius: 0.375rem;
    border: 1px solid rgba(var(--bs-primary-rgb), 0.2);
    background: transparent;
  }

  .btn-light {
    background: rgba(var(--bs-primary-rgb), 0.1);
    border: none;
    color: var(--bs-primary);
  }

  .btn-light:hover {
    background: rgba(var(--bs-primary-rgb), 0.15);
    color: var(--bs-primary);
  }

  .icon-wrapper.icon-sm {
    width: 32px;
    height: 32px;
    border-radius: 10px;
  }

  .icon-wrapper.icon-sm i {
    font-size: 1rem;
  }

  .separator {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 1.5rem 0;
    color: var(--bs-secondary-color);
  }

  .separator::before,
  .separator::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid var(--bs-border-color);
  }

  .separator span {
    padding: 0 1rem;
    font-size: 0.875rem;
    font-weight: 500;
  }

  /* Modal styles */
  .modal-content {
    border: none;
    border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
  }

  .modal-header {
    padding: 1.5rem;
  }

  .modal-body {
    padding: 1.5rem;
  }

  .modal-footer {
    padding: 1.5rem;
  }

  /* Dark theme adjustments */
  :global([data-bs-theme="dark"]) {
    .btn-light {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }

    .btn-light:hover {
      background: rgba(255, 255, 255, 0.15);
      color: white;
    }

    .separator {
      color: rgba(255, 255, 255, 0.5);
    }

    .separator::before,
    .separator::after {
      border-color: rgba(255, 255, 255, 0.1);
    }

    .modal-content {
      background: rgba(17, 24, 39, 0.95);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
  }
</style>
