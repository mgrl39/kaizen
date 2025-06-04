<script lang="ts">
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';
  import { t } from '$lib/i18n';

  // Estado para el perfil
  let loading = true;
  let error: string | null = null;
  let user: { username: string; name: string; email: string; created_at?: string; avatar?: string; loyaltyPoints?: number } | null = null;

  function formatDate(date: Date | string): string {
    const d = typeof date === 'string' ? new Date(date) : date;
    return new Intl.DateTimeFormat('es-ES', { 
      day: '2-digit', 
      month: '2-digit', 
      year: 'numeric' 
    }).format(d);
  }

  async function fetchProfile() {
    loading = true;
    error = '';
    
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
        error = data.message || 'Error al cargar el perfil';
        if (response.status === 401) {
          goto('/login');
        }
      }
    } catch (e) {
      error = 'Error de conexión con el servidor';
    } finally {
      loading = false;
    }
  }

  onMount(() => {
    fetchProfile();
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

    <div class="row justify-content-center">
      <!-- Columna de Perfil -->
      <div class="col-lg-8">
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
    </div>
  </div>
</div>

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

  /* Dark theme adjustments */
  :global([data-bs-theme="dark"]) {
    .card {
      background: rgba(17, 24, 39, 0.8);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .profile-info .info-item:hover {
      background-color: rgba(255, 255, 255, 0.03);
    }

    .icon-wrapper {
      background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
  }
</style>
