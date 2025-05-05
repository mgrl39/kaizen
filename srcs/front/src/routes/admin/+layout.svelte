 <script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { page } from '$app/stores';
  import { API_URL } from '$lib/config';
  
  // Importa solo lo necesario
  import 'bootstrap/dist/css/bootstrap.min.css';
  import 'bootstrap-icons/font/bootstrap-icons.css';

  export let data;

  let isAdmin = false;
  let user = null;
  let loading = true;
  let sidebarOpen = true;

  // Menú de administración
  const adminMenu = [
    { 
      title: 'General',
      items: [
        { name: 'Dashboard', icon: 'house', url: '/admin/dashboard' },
      ]
    },
    {
      title: 'Gestión',
      items: [
        { name: 'Cines', icon: 'building', url: '/admin/cinemas' },
        { name: 'Películas', icon: 'film', url: '/admin/movies' },
        { name: 'Usuarios', icon: 'people', url: '/admin/users' },
      ]
    },
    {
      title: 'Configuración',
      items: [
        { name: 'Ajustes', icon: 'gear', url: '/admin/settings' },
        { name: 'Mi Perfil', icon: 'person', url: '/admin/profile' },
        { name: 'Salir', icon: 'box-arrow-left', url: '/admin/logout', action: 'logout' }
      ]
    }
  ];

  // Verificación de autorización
  async function checkAuth() {
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
        console.log("User data:", data.user);
        isAdmin = data.user && data.user.role === 'admin';
        
        if (!isAdmin) {
          goto('/');
        }
      } else {
        localStorage.removeItem('token');
        goto('/login');
      }
    } catch (e) {
      localStorage.removeItem('token');
      goto('/login');
    } finally {
      loading = false;
    }
  }

  // Manejar logout
  async function handleLogout() {
    const token = localStorage.getItem('token');
    if (token) {
      try {
        await fetch(`${API_URL}/logout`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
      } catch (e) {
        // Ignorar errores de red
      }
      localStorage.removeItem('token');
      goto('/login');
    }
  }

  function toggleSidebar() {
    sidebarOpen = !sidebarOpen;
  }

  function handleMenuAction(action) {
    if (action === 'logout') {
      handleLogout();
    }
  }

  // Comprobar autorización al cargar
  onMount(() => {
    checkAuth();
  });
</script>

<svelte:head>
  <style>
    body {
      background-color: #f5f8fa !important;
      margin: 0 !important;
      padding: 0 !important;
      overflow-x: hidden !important;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    }
    
    .decorative-blob {
      display: none !important;
    }
  </style>
</svelte:head>

{#if loading}
  <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Cargando...</span>
    </div>
  </div>
{:else if isAdmin}
  <div class="admin-wrapper d-flex">
    <!-- Sidebar fija -->
    <div class="sidebar bg-dark text-white" style="width: {sidebarOpen ? '250px' : '70px'}; transition: width 0.3s ease;">
      <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-secondary">
        <div class="d-flex align-items-center">
          <img src="/logo.png" alt="Kaizen" width="40" height="40" class="me-2">
          {#if sidebarOpen}
            <span class="fw-bold">Kaizen Admin</span>
          {/if}
        </div>
        <button class="btn btn-sm text-white" on:click={toggleSidebar}>
          <i class="bi bi-chevron-{sidebarOpen ? 'left' : 'right'}"></i>
        </button>
      </div>
      
      <div class="p-2">
        {#each adminMenu as section}
          <div class="mb-3">
            {#if sidebarOpen}
              <div class="text-uppercase small text-muted ms-3 mt-3 mb-2">{section.title}</div>
            {:else}
              <hr class="my-2 opacity-25">
            {/if}
            
            <ul class="nav flex-column">
              {#each section.items as item}
                <li class="nav-item">
                  <a 
                    class="nav-link rounded d-flex align-items-center {sidebarOpen ? 'px-3' : 'justify-content-center'} py-2 {$page.url.pathname === item.url || $page.url.pathname.startsWith(item.url + '/') ? 'active bg-primary bg-opacity-25 text-white' : 'text-light'}" 
                    href={item.action ? 'javascript:void(0)' : item.url}
                    on:click={() => item.action && handleMenuAction(item.action)}
                  >
                    <i class="bi bi-{item.icon} {sidebarOpen ? 'me-3' : ''}"></i>
                    {#if sidebarOpen}
                      <span>{item.name}</span>
                    {/if}
                  </a>
                </li>
              {/each}
            </ul>
          </div>
        {/each}
      </div>
    </div>
    
    <!-- Contenido principal -->
    <div class="content-wrapper" style="flex: 1; margin-left: {sidebarOpen ? '250px' : '70px'}; transition: margin-left 0.3s ease;">
      <!-- Barra superior -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm sticky-top">
        <div class="container-fluid px-4">
          <button class="navbar-toggler border-0 d-lg-none" type="button" on:click={toggleSidebar}>
            <i class="bi bi-list"></i>
          </button>
          
          <div class="d-flex align-items-center">
            <span class="fw-bold text-uppercase text-primary me-4">
              {$page.url.pathname.split('/').pop().charAt(0).toUpperCase() + $page.url.pathname.split('/').pop().slice(1)}
            </span>
            
            <div class="d-none d-md-flex">
              <a href="/admin/cinemas" class="me-3 nav-link px-2">Cines</a>
              <a href="/admin/movies" class="me-3 nav-link px-2">Películas</a>
              <a href="/admin/users" class="nav-link px-2">Usuarios</a>
            </div>
          </div>
          
          <div class="d-flex align-items-center">
            <div class="dropdown">
              <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                  {user?.name?.charAt(0) || user?.username?.charAt(0) || 'A'}
                </div>
                <span class="d-none d-md-inline">{user?.name || user?.username || 'Admin'}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="/admin/profile"><i class="bi bi-person me-2"></i>Perfil</a></li>
                <li><a class="dropdown-item" href="/admin/settings"><i class="bi bi-gear me-2"></i>Ajustes</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" on:click={handleLogout}><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
              </ul>
            </div>
          </div>
        </div>
      </nav>
      
      <!-- Contenido de la página -->
      <div class="container-fluid p-4">
        <slot />
      </div>
    </div>
  </div>
{/if}

<style>
  .sidebar {
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
    z-index: 1030;
  }
  
  .content-wrapper {
    min-height: 100vh;
  }
  
  /* Estilos para que el dashboard no necesite scroll horizontal */
  :global(.dashboard-container) {
    max-width: 100%;
    overflow-x: hidden;
  }
  
  :global(.dashboard-container .row) {
    margin-right: -10px;
    margin-left: -10px;
  }
  
  :global(.dashboard-container [class*="col-"]) {
    padding-right: 10px;
    padding-left: 10px;
  }
  
  @media (max-width: 992px) {
    .sidebar {
      transform: translateX(-100%);
      transition: transform 0.3s ease;
    }
    
    .sidebar.open {
      transform: translateX(0);
    }
    
    .content-wrapper {
      margin-left: 0 !important;
    }
  }
</style>