<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';
  import { t } from '$lib/i18n';

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

<div class="container my-2">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1>{user?.name || 'Cargando...'}</h1>
      <p class="text-muted">Miembro desde {formatDate(new Date(user?.created_at || Date.now()))}</p>
    </div>
    {#if user}
      <div>
        <button class="btn btn-outline-secondary me-2" on:click={() => editing = !editing}>
          {editing ? 'Cancelar' : 'Editar Perfil'}
        </button>
        <button class="btn btn-primary" on:click={() => editing ? updateProfile() : null} disabled={!editing}>
          Guardar Cambios
        </button>
      </div>
    {/if}
  </div>

  {#if error}
    <div class="alert alert-danger">{error}</div>
  {/if}
  {#if success}
    <div class="alert alert-success">{success}</div>
  {/if}

  {#if editing && user}
    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card p-4">
          <h4>Información Personal</h4>
          <form>
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" bind:value={formData.name} class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" bind:value={formData.username} class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" bind:value={formData.email} class="form-control" />
            </div>
          </form>
        </div>
      </div>

      <div class="col-md-6 mb-4">
        <div class="card p-4">
          <h4>Cambiar Contraseña</h4>
          <form on:submit|preventDefault={changePassword}>
            <div class="mb-3">
              <label class="form-label">Contraseña Actual</label>
              <input type="password" bind:value={formData.current_password} class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label">Nueva Contraseña</label>
              <input type="password" bind:value={formData.new_password} class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label">Confirmar Nueva Contraseña</label>
              <input type="password" bind:value={formData.new_password_confirmation} class="form-control" />
            </div>
            <button type="submit" class="btn btn-primary w-100">Cambiar Contraseña</button>
          </form>
        </div>
      </div>
    </div>
  {/if}

  {#if !editing && user}
    <div class="card p-4">
      <div class="row">
        <div class="col-md-6 mb-3">
          <h4>Información Personal</h4>
          <p><strong>Nombre:</strong> {user.name}</p>
          <p><strong>Username:</strong> {user.username}</p>
          <p><strong>Email:</strong> {user.email}</p>
        </div>
        <div class="col-md-6 mb-3">
          <h4>Estadísticas</h4>
          <p><strong>Puntos de Lealtad:</strong> {user.loyaltyPoints || 0}</p>
          <p><strong>Fecha de Registro:</strong> {formatDate(new Date(user.created_at || Date.now()))}</p>
        </div>
      </div>
    </div>
  {/if}
</div>
