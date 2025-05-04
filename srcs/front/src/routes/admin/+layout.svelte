<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { page } from '$app/stores';
  import { API_URL } from '$lib/config';

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
        // Verificar si es administrador
        isAdmin = data.user.role === 'admin';
        
        if (!isAdmin) {
          // Redireccionar si no es admin
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

{#if loading}
  <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Cargando...</span>
    </div>
  </div>
{:else if isAdmin}
  <div class="admin-layout">
    <!-- Sidebar -->
    <div class="admin-sidebar {sidebarOpen ? 'open' : 'closed'}">
      <div class="sidebar-header">
        <div class="logo">
          <img src="/logo.png" alt="Kaizen" width="40" height="40">
          {#if sidebarOpen}
            <span class="logo-text">Kaizen Admin</span>
          {/if}
        </div>
        <button class="btn-toggle" on:click={toggleSidebar}>
          <i class="bi bi-chevron-{sidebarOpen ? 'left' : 'right'}"></i>
        </button>
      </div>
      
      <div class="sidebar-content">
        {#each adminMenu as section}
          <div class="menu-section">
            {#if sidebarOpen}
              <h6 class="section-title">{section.title}</h6>
            {:else}
              <hr class="divider">
            {/if}
            
            <ul class="nav flex-column">
              {#each section.items as item}
                <li class="nav-item">
                  <a 
                    class="nav-link {$page.url.pathname === item.url ? 'active' : ''}" 
                    href={item.action ? 'javascript:void(0)' : item.url}
                    on:click={() => item.action && handleMenuAction(item.action)}
                  >
                    <i class="bi bi-{item.icon}"></i>
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
    
    <!-- Main Content -->
    <div class="admin-content">
      <header class="admin-header">
        <div class="header-left">
          <button class="btn-toggle d-md-none" on:click={toggleSidebar}>
            <i class="bi bi-list"></i>
          </button>
          <h4 class="page-title">
            {$page.url.pathname.split('/').pop().charAt(0).toUpperCase() + $page.url.pathname.split('/').pop().slice(1)}
          </h4>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <div class="avatar">
              <img src="https://ui-avatars.com/api/?name={user?.name || 'Admin'}&background=random" alt="User">
            </div>
            <span class="username">{user?.name || user?.username || 'Administrador'}</span>
          </div>
        </div>
      </header>
      
      <main class="content-wrapper">
        <slot />
      </main>
    </div>
  </div>
{/if}

<style>
  .admin-layout {
    display: flex;
    height: 100vh;
    width: 100%;
    background-color: #f5f5f9;
  }
  
  .admin-sidebar {
    background-color: #2a3042;
    color: #a6b0cf;
    height: 100%;
    transition: width 0.3s ease;
    width: 250px;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    overflow-y: auto;
  }
  
  .admin-sidebar.closed {
    width: 70px;
  }
  
  .sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .logo {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .logo-text {
    font-weight: 600;
    color: white;
  }
  
  .btn-toggle {
    background: transparent;
    border: none;
    color: white;
    cursor: pointer;
  }
  
  .sidebar-content {
    padding: 15px 0;
  }
  
  .menu-section {
    margin-bottom: 25px;
  }
  
  .section-title {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0 15px;
    margin-bottom: 10px;
    color: #6a7187;
  }
  
  .divider {
    margin: 10px 15px;
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  .nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    color: #a6b0cf;
    transition: all 0.3s;
    position: relative;
  }
  
  .nav-link:hover, .nav-link.active {
    color: white;
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  .nav-link i {
    font-size: 18px;
    min-width: 18px;
  }
  
  .admin-content {
    flex-grow: 1;
    margin-left: 250px;
    transition: margin-left 0.3s ease;
  }
  
  .admin-sidebar.closed + .admin-content {
    margin-left: 70px;
  }
  
  .admin-header {
    background-color: white;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }
  
  .header-left {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  
  .page-title {
    margin-bottom: 0;
    font-weight: 500;
  }
  
  .header-right {
    display: flex;
    align-items: center;
    gap: 20px;
  }
  
  .user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
  }
  
  .avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .username {
    font-weight: 500;
  }
  
  .content-wrapper {
    padding: 25px;
    height: calc(100vh - 70px);
    overflow-y: auto;
  }
  
  @media (max-width: 768px) {
    .admin-sidebar {
      transform: translateX(-100%);
      width: 240px;
    }
    
    .admin-sidebar.open {
      transform: translateX(0);
    }
    
    .admin-content {
      margin-left: 0;
    }
    
    .admin-sidebar.closed + .admin-content {
      margin-left: 0;
    }
  }
</style> 