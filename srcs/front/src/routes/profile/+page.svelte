<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';

  function formatDate(date: Date | string): string {
    const d = typeof date === 'string' ? new Date(date) : date;
    return new Intl.DateTimeFormat('es-ES', { 
      day: '2-digit', 
      month: '2-digit', 
      year: 'numeric' 
    }).format(d);
  }

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

  onMount(fetchProfile);
</script>

<div class="profile-container">
  <!-- Header Section -->
  <div class="profile-header mb-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-auto">
          <div class="avatar-container">
            <img 
              src={user?.avatar || 'https://ui-avatars.com/api/?name=' + (user?.name || 'User')} 
              alt="Profile" 
              class="profile-avatar"
            />
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
              class="btn btn-outline-primary btn-lg {editing ? 'active' : ''}" 
              on:click={() => editing = !editing}
            >
              <i class="bi bi-pencil-square me-2"></i>
              {editing ? 'Cancelar' : 'Editar Perfil'}
            </button>
          </div>
        {/if}
      </div>
    </div>
  </div>

  <div class="container">
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

    {#if editing && user}
      <div class="row g-4">
        <div class="col-md-6">
          <div class="card card-hover h-100">
            <div class="card-body">
              <h4 class="card-title d-flex align-items-center mb-4">
                <i class="bi bi-person-circle me-2"></i>
                Información Personal
              </h4>
              <form>
                <div class="form-floating mb-3">
                  <input 
                    type="text" 
                    bind:value={formData.name} 
                    class="form-control" 
                    id="name" 
                    placeholder="Nombre"
                  />
                  <label for="name">Nombre</label>
                </div>
                <div class="form-floating mb-3">
                  <input 
                    type="text" 
                    bind:value={formData.username} 
                    class="form-control" 
                    id="username" 
                    placeholder="Username"
                  />
                  <label for="username">Username</label>
                </div>
                <div class="form-floating mb-3">
                  <input 
                    type="email" 
                    bind:value={formData.email} 
                    class="form-control" 
                    id="email" 
                    placeholder="Email"
                  />
                  <label for="email">Email</label>
                </div>
                <button 
                  type="button" 
                  class="btn btn-primary w-100" 
                  on:click={updateProfile}
                >
                  <i class="bi bi-check2-circle me-2"></i>
                  Guardar Cambios
                </button>
              </form>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card card-hover h-100">
            <div class="card-body">
              <h4 class="card-title d-flex align-items-center mb-4">
                <i class="bi bi-shield-lock me-2"></i>
                Cambiar Contraseña
              </h4>
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
                <button type="submit" class="btn btn-primary w-100">
                  <i class="bi bi-key me-2"></i>
                  Cambiar Contraseña
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    {:else if user}
      <div class="card card-hover">
        <div class="card-body">
          <div class="row g-4">
            <div class="col-md-6">
              <h4 class="d-flex align-items-center mb-4">
                <i class="bi bi-person-circle me-2"></i>
                Información Personal
              </h4>
              <div class="profile-info">
                <div class="info-item">
                  <span class="info-label">Nombre</span>
                  <span class="info-value">{user.name}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Username</span>
                  <span class="info-value">@{user.username}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Email</span>
                  <span class="info-value">{user.email}</span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <h4 class="d-flex align-items-center mb-4">
                <i class="bi bi-graph-up me-2"></i>
                Estadísticas
              </h4>
              <div class="profile-info">
                <div class="info-item">
                  <span class="info-label">Fecha de Registro</span>
                  <span class="info-value">{formatDate(new Date(user.created_at || Date.now()))}</span>
                </div>
                {#if user.loyaltyPoints}
                  <div class="info-item">
                    <span class="info-label">Puntos de Fidelidad</span>
                    <span class="info-value">{user.loyaltyPoints} puntos</span>
                  </div>
                {/if}
              </div>
            </div>
          </div>
        </div>
      </div>
    {/if}
  </div>
</div>

<style>
  .profile-container {
    padding-top: 2rem;
    padding-bottom: 4rem;
  }

  .profile-header {
    background: linear-gradient(to right, #6366f1, #8b5cf6);
    padding: 3rem 0;
    color: white;
    margin-top: -2rem;
    margin-bottom: 3rem;
  }

  .avatar-container {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .profile-avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .card-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .profile-info .info-item {
    padding: 1rem;
    border-bottom: 1px solid var(--bs-border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
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
  }

  :global([data-bs-theme="dark"]) .profile-header {
    background: linear-gradient(to right, #4f46e5, #7c3aed);
  }

  :global([data-bs-theme="dark"]) .card-hover {
    background-color: rgba(17, 24, 39, 0.8);
    border-color: rgba(255, 255, 255, 0.1);
  }

  :global([data-bs-theme="dark"]) .info-label {
    color: rgba(255, 255, 255, 0.6);
  }

  :global([data-bs-theme="dark"]) .info-value {
    color: rgba(255, 255, 255, 0.9);
  }

  :global([data-bs-theme="dark"]) .form-control,
  :global([data-bs-theme="dark"]) .form-select {
    background-color: rgba(17, 24, 39, 0.8);
    border-color: rgba(255, 255, 255, 0.1);
    color: white;
  }

  :global([data-bs-theme="dark"]) .form-control:focus,
  :global([data-bs-theme="dark"]) .form-select:focus {
    background-color: rgba(17, 24, 39, 1);
    border-color: #6366f1;
    box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
  }
</style>
