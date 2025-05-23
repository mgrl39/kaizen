<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { page } from '$app/stores';
  import { API_URL } from '$lib/config';

  // Tipos
  interface AdminMenuItem {
    url: string;
    icon: string;
    text: string;
  }

  // Estado de autenticación
  let isAuthenticated = false;
  let isAdmin = false;
  let userName = '';

  // Elementos de navegación del admin
  const adminMenuItems: AdminMenuItem[] = [
    { url: '/admin', icon: 'speedometer2', text: 'Panel Principal' },
    { url: '/admin/movies', icon: 'film', text: 'Películas' },
    { url: '/admin/cinemas', icon: 'building', text: 'Cines' },
    { url: '/admin/bookings', icon: 'ticket-detailed', text: 'Reservas' },
    { url: '/admin/users', icon: 'people', text: 'Usuarios' },
    { url: '/admin/reports', icon: 'graph-up', text: 'Informes' },
    { url: '/admin/settings', icon: 'gear', text: 'Configuración' }
  ];

  onMount(async () => {
    // Forzar tema claro
    document.documentElement.setAttribute('data-bs-theme', 'light');
    
    // Verificar autenticación
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
        isAuthenticated = true;
        isAdmin = data.user.role === 'admin';
        userName = data.user.name || data.user.username;

        if (!isAdmin) {
          goto('/');
        }
      } else {
        goto('/login');
      }
    } catch (error) {
      console.error('Error al verificar perfil:', error);
      goto('/login');
    }
  });

  function isActive(path: string): boolean {
    return $page.url.pathname === path;
  }

  async function handleLogout(): Promise<void> {
    const token = localStorage.getItem('token');
    if (token) {
      localStorage.removeItem('token');
      localStorage.removeItem('authState');
      
      try {
        await fetch(`${API_URL}/logout`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json'
          }
        });
      } catch (e) {
        console.error("Error en logout:", e);
      }
      
      goto('/login');
    }
  }
</script>

<div class="admin-layout">
  <!-- Sidebar -->
  <nav class="admin-sidebar">
    <div class="sidebar-header">
      <a href="/admin" class="brand">
        Kaizen Admin
      </a>
    </div>

    <div class="sidebar-content">
      <ul class="nav flex-column">
        {#each adminMenuItems as item}
          <li class="nav-item">
            <a 
              href={item.url} 
              class="nav-link {isActive(item.url) ? 'active' : ''}"
            >
              <i class="bi bi-{item.icon} me-2"></i>
              {item.text}
            </a>
          </li>
        {/each}
      </ul>
    </div>

    <div class="sidebar-footer">
      <div class="user-info mb-2">
        <i class="bi bi-person-circle me-2"></i>
        <span>{userName}</span>
      </div>
      <button class="btn btn-outline-secondary w-100" on:click={handleLogout}>
        <i class="bi bi-box-arrow-right me-2"></i>
        Cerrar Sesión
      </button>
    </div>
  </nav>

  <!-- Main content -->
  <main class="admin-main">
    <slot />
  </main>
</div>

<style>
  .admin-layout {
    display: flex;
    min-height: 100vh;
    background-color: #f8f9fa;
  }

  .admin-sidebar {
    width: 280px;
    background: white;
    border-right: 1px solid #dee2e6;
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100vh;
    z-index: 1030;
  }

  .sidebar-header {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
  }

  .brand {
    font-size: 1.25rem;
    font-weight: bold;
    color: #212529;
    text-decoration: none;
  }

  .sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 0;
  }

  .nav-link {
    color: #495057;
    padding: 0.75rem 1rem;
    transition: all 0.2s;
  }

  .nav-link:hover {
    background-color: #f8f9fa;
    color: #0d6efd;
  }

  .nav-link.active {
    background-color: #e9ecef;
    color: #0d6efd;
    font-weight: 500;
  }

  .sidebar-footer {
    padding: 1rem;
    border-top: 1px solid #dee2e6;
  }

  .user-info {
    color: #6c757d;
    font-size: 0.875rem;
  }

  .admin-main {
    margin-left: 280px;
    padding: 2rem;
    width: calc(100% - 280px);
  }

  @media (max-width: 768px) {
    .admin-sidebar {
      width: 100%;
      height: auto;
      position: relative;
    }

    .admin-main {
      margin-left: 0;
      width: 100%;
    }

    .admin-layout {
      flex-direction: column;
    }
  }
</style>