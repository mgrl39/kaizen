<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';

  let loading = true;
  let error: string | null = null;
  let user: { username: string; name: string; email: string } | null = null;

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
          {:else if user}
            <div class="d-flex flex-column align-items-center">
              <div class="mb-3">
                <i class="bi bi-person-circle" style="font-size: 4rem; color: var(--bs-primary);"></i>
              </div>
              <div class="mb-2">
                <span class="badge bg-primary fs-6">{user.username}</span>
              </div>
              <h4 class="mb-1">{user.name}</h4>
              <p class="mb-4 text-muted">{user.email}</p>
              <div>
                <a href="/bookings" class="btn btn-outline-primary me-2">
                  <i class="bi bi-ticket me-1"></i> Mis Reservas
                </a>
                <a href="/logout" class="btn btn-outline-danger">
                  <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                </a>
              </div>
            </div>
          {/if}
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Reutiliza estilos inspirados en movies */
  .movie-card {
    border-radius: 18px;
    background: var(--bs-light, #f8f9fa);
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
    padding: 2rem 1.5rem;
  }
  .section-title {
    font-weight: 700;
    letter-spacing: 0.02em;
    color: var(--bs-primary, #0d6efd);
  }
</style> 