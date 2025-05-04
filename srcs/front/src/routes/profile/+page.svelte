<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';

  let loading = true;
  let error: string | null = null;
  let success: string | null = null;
  let user: { username: string; name: string; email: string } | null = null;
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
          password: formData.new_password,
          password_confirmation: formData.new_password_confirmation
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

  onMount(() => {
    fetchProfile();
  });
</script>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="movie-card card shadow border-0">
        <div class="card-body">
          <h2 class="mb-4 section-title text-center">Mi Perfil</h2>
          {#if loading}
            <div class="my-5 text-center">
              <span class="spinner-border text-primary" role="status" aria-hidden="true"></span>
              <div class="mt-2">Cargando...</div>
            </div>
          {:else if error}
            <div class="alert alert-danger">{error}</div>
          {:else if success}
            <div class="alert alert-success">{success}</div>
          {:else if user}
            <div class="d-flex flex-column align-items-center">
              <div class="mb-3">
                <i class="bi bi-person-circle" style="font-size: 4rem; color: var(--bs-primary);"></i>
              </div>
              
              {#if !editing}
                <div class="mb-2">
                  <span class="badge bg-primary fs-6">{user.username}</span>
                </div>
                <h4 class="mb-1">{user.name}</h4>
                <p class="mb-4 text-muted">{user.email}</p>
                <div>
                  <button class="btn btn-primary me-2" on:click={() => editing = true}>
                    <i class="bi bi-pencil me-1"></i> Editar Perfil
                  </button>
                  <a href="/bookings" class="btn btn-outline-primary me-2">
                    <i class="bi bi-ticket me-1"></i> Mis Reservas
                  </a>
                  <a href="/logout" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                  </a>
                </div>
              {:else}
                <form on:submit|preventDefault={updateProfile} class="w-100">
                  <div class="mb-3">
                    <label class="form-label">Nombre de usuario</label>
                    <input type="text" class="form-control" bind:value={formData.username} required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" class="form-control" bind:value={formData.name} required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" bind:value={formData.email} required>
                  </div>
                  <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                      <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-outline-secondary" on:click={() => editing = false}>
                      <i class="bi bi-x-lg me-1"></i> Cancelar
                    </button>
                  </div>
                </form>
              {/if}
            </div>

            <!-- Cambio de contraseña -->
            <div class="mt-5">
              <h4 class="section-subtitle mb-3">Cambiar Contraseña</h4>
              <form on:submit|preventDefault={changePassword}>
                <div class="mb-3">
                  <label class="form-label">Contraseña actual</label>
                  <input type="password" class="form-control" bind:value={formData.current_password} required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Nueva contraseña</label>
                  <input type="password" class="form-control" bind:value={formData.new_password} required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Confirmar nueva contraseña</label>
                  <input type="password" class="form-control" bind:value={formData.new_password_confirmation} required>
                </div>
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-key me-1"></i> Cambiar Contraseña
                </button>
              </form>
            </div>
          {/if}
        </div>
      </div>
    </div>
  </div>
</div>