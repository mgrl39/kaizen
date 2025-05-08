<script lang="ts">
  import { t } from '$lib/i18n';
  import { page } from '$app/stores';
  import { onMount } from 'svelte';
  
  // Estado para el sidebar
  let sidebarOpen = false;
  let openSubmenus = [];
  
  // Estructura del menú de navegación
  const menuItems = [
    { id: 'dashboard', name: $t('dashboard'), icon: 'speedometer2', path: '/admin' },
    { 
      id: 'movies', 
      name: $t('movies'), 
      icon: 'film', 
      submenu: [
        { name: $t('allMovies'), path: '/admin/movies' },
        { name: $t('addMovie'), path: '/admin/movies/add' }
      ]
    },
    { 
      id: 'cinemas', 
      name: $t('cinemas'), 
      icon: 'building', 
      submenu: [
        { name: $t('allCinemas'), path: '/admin/cinemas' },
        { name: $t('addCinema'), path: '/admin/cinemas/add' }
      ]
    },
    { 
      id: 'users', 
      name: $t('users'), 
      icon: 'people', 
      submenu: [
        { name: $t('allUsers'), path: '/admin/users' },
        { name: $t('addUser'), path: '/admin/users/add' }
      ]
    },
    { 
      id: 'bookings', 
      name: $t('bookings'), 
      icon: 'ticket', 
      path: '/admin/bookings' 
    },
    { 
      id: 'reports', 
      name: $t('reports'), 
      icon: 'bar-chart', 
      path: '/admin/reports' 
    },
    { 
      id: 'settings', 
      name: $t('settings'), 
      icon: 'gear', 
      path: '/admin/settings' 
    }
  ];
  
  // Función para alternar submenús
  function toggleSubmenu(id) {
    if (openSubmenus.includes(id)) {
      openSubmenus = openSubmenus.filter(item => item !== id);
    } else {
      openSubmenus = [...openSubmenus, id];
    }
  }
  
  // Función para verificar si una ruta está activa
  function isActive(path) {
    if (path === '/admin') {
      return $page.url.pathname === '/admin' || $page.url.pathname === '/admin/';
    }
    return $page.url.pathname.startsWith(path);
  }
  
  // Cerrar sidebar en pantallas pequeñas cuando cambia la ruta
  $: if ($page && window.innerWidth < 768) {
    sidebarOpen = false;
  }
  
  // Abrir automáticamente los submenús de la ruta actual
  onMount(() => {
    menuItems.forEach(item => {
      if (item.submenu && item.submenu.some(subitem => isActive(subitem.path))) {
        if (!openSubmenus.includes(item.id)) {
          openSubmenus = [...openSubmenus, item.id];
        }
      }
    });
  });
</script>

<div class="admin-layout">
  <!-- Sidebar -->
  <aside class="sidebar {sidebarOpen ? 'sidebar-open' : ''}" id="admin-sidebar">
    <div class="sidebar-header">
      <h1 class="sidebar-title">Admin Panel</h1>
      <button 
        class="sidebar-toggle"
        on:click={() => (sidebarOpen = !sidebarOpen)}
        aria-label={sidebarOpen ? "Cerrar menú lateral" : "Abrir menú lateral"}
      >
        <i class="bi bi-{sidebarOpen ? 'x' : 'list'}"></i>
      </button>
    </div>
    
    <nav class="sidebar-nav">
      <ul class="sidebar-menu">
        {#each menuItems as item}
          <li class="sidebar-item {isActive(item.path || '') ? 'active' : ''}">
            {#if item.submenu}
              <button
                class="sidebar-link has-submenu"
                on:click={() => toggleSubmenu(item.id)}
                on:keydown={(e) => {
                  if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleSubmenu(item.id);
                  }
                }}
                aria-expanded={openSubmenus.includes(item.id)}
                aria-controls={`submenu-${item.id}`}
              >
                <i class="bi bi-{item.icon}"></i>
                <span>{item.name}</span>
                <i class="bi bi-chevron-{openSubmenus.includes(item.id) ? 'down' : 'right'} submenu-icon"></i>
              </button>
              
              {#if openSubmenus.includes(item.id)}
                <ul class="submenu" id={`submenu-${item.id}`}>
                  {#each item.submenu as subitem}
                    <li class="submenu-item {isActive(subitem.path) ? 'active' : ''}">
                      <a href={subitem.path} class="submenu-link">
                        {subitem.name}
                      </a>
                    </li>
                  {/each}
                </ul>
              {/if}
            {:else}
              <a href={item.path} class="sidebar-link {isActive(item.path) ? 'active' : ''}">
                <i class="bi bi-{item.icon}"></i>
                <span>{item.name}</span>
              </a>
            {/if}
          </li>
        {/each}
      </ul>
    </nav>
  </aside>
  
  <!-- Contenido principal -->
  <div class="main-content">
    <header class="admin-header">
      <button 
        class="mobile-menu-btn"
        on:click={() => (sidebarOpen = !sidebarOpen)}
        aria-label={sidebarOpen ? "Cerrar menú" : "Abrir menú"}
      >
        <i class="bi bi-{sidebarOpen ? 'x' : 'list'}"></i>
      </button>
      
      <div class="header-title">
        {#each menuItems as item}
          {#if isActive(item.path || '')}
            <h1>{item.name}</h1>
          {:else if item.submenu && item.submenu.some(subitem => isActive(subitem.path))}
            <h1>{item.name} / {item.submenu.find(subitem => isActive(subitem.path)).name}</h1>
          {/if}
        {/each}
      </div>
    </header>
    
    <main class="admin-main">
      <slot></slot>
    </main>
  </div>
</div>

<style>
  /* Estilos básicos y limpios */
  .admin-layout {
    display: flex;
    min-height: 100vh;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #f5f5f5;
  }
  
  /* Sidebar */
  .sidebar {
    width: 250px;
    background-color: #333;
    color: white;
    height: 100vh;
    overflow-y: auto;
    transition: transform 0.3s ease;
  }
  
  .sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #444;
  }
  
  .sidebar-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
  }
  
  .sidebar-toggle {
    display: none;
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    font-size: 1.25rem;
  }
  
  .sidebar-nav {
    padding: 1rem 0;
  }
  
  .sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .sidebar-item {
    margin-bottom: 0.25rem;
  }
  
  .sidebar-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #ccc;
    text-decoration: none;
    transition: background-color 0.2s;
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
  }
  
  .sidebar-link:hover, .submenu-link:hover {
    background-color: #444;
    color: white;
  }
  
  .sidebar-link.active {
    background-color: #555;
    color: white;
    border-left: 3px solid #007bff;
  }
  
  .sidebar-link i {
    margin-right: 0.75rem;
    width: 20px;
    text-align: center;
  }
  
  .has-submenu {
    position: relative;
  }
  
  .submenu-icon {
    margin-left: auto;
  }
  
  .submenu {
    list-style: none;
    padding: 0;
    margin: 0;
    background-color: #2a2a2a;
  }
  
  .submenu-link {
    display: block;
    padding: 0.5rem 1rem 0.5rem 3rem;
    color: #bbb;
    text-decoration: none;
    transition: background-color 0.2s;
  }
  
  .submenu-item.active .submenu-link {
    color: white;
    background-color: #444;
    border-left: 3px solid #007bff;
  }
  
  /* Contenido principal */
  .main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
  }
  
  .admin-header {
    background-color: white;
    border-bottom: 1px solid #ddd;
    padding: 1rem;
    display: flex;
    align-items: center;
    height: 60px;
  }
  
  .mobile-menu-btn {
    display: none;
    background: none;
    border: none;
    font-size: 1.25rem;
    margin-right: 1rem;
    cursor: pointer;
  }
  
  .header-title h1 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 500;
  }
  
  .admin-main {
    flex: 1;
    padding: 1.5rem;
    overflow-y: auto;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .sidebar {
      position: fixed;
      z-index: 1000;
      transform: translateX(-100%);
    }
    
    .sidebar-open {
      transform: translateX(0);
    }
    
    .sidebar-toggle {
      display: block;
    }
    
    .mobile-menu-btn {
      display: block;
    }
  }
</style>