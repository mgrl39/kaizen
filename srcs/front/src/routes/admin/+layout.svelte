<script lang="ts">
  import { t } from '$lib/i18n';
  import { page } from '$app/stores';
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  
  // Estado para el sidebar
  let sidebarOpen = false;
  let isBrowser = false;
  let showExitConfirmation = false;
  let cancelButtonRef: HTMLButtonElement;
  
  // Estructura del menú de navegación simplificada (sin submenu)
  const menuItems = [
    { id: 'dashboard', name: 'Dashboard', icon: 'speedometer2', path: '/admin' },
    { id: 'movies', name: 'Películas', icon: 'film', path: '/admin/movies' },
    { id: 'cinemas', name: 'Cines', icon: 'building', path: '/admin/cinemas' },
    { id: 'users', name: 'Usuarios', icon: 'people', path: '/admin/users' },
    { id: 'bookings', name: $t('bookings'), icon: 'ticket', path: '/admin/bookings' },
    { id: 'reports', name: 'Informes', icon: 'bar-chart', path: '/admin/reports' },
    { id: 'settings', name: 'Configuración', icon: 'gear', path: '/admin/settings' }
  ];
  
  // Función para verificar si una ruta está activa
  function isActive(path: string) {
    if (path === '/admin') {
      return $page.url.pathname === '/admin' || $page.url.pathname === '/admin/';
    }
    return $page.url.pathname.startsWith(path);
  }
  
  // Función para mostrar confirmación antes de salir
  function promptExitAdminPanel() {
    showExitConfirmation = true;
    
    // Enfocar el botón de cancelar después de que el modal sea visible
    setTimeout(() => {
      if (cancelButtonRef) {
        cancelButtonRef.focus();
      }
    }, 50);
  }
  
  // Función para salir del panel de administración
  function exitAdminPanel() {
    showExitConfirmation = false;
    goto('/');
  }
  
  // Función para cancelar la salida
  function cancelExit() {
    showExitConfirmation = false;
  }
  
  onMount(() => {
    isBrowser = true;
    
    // Manejar cambio de ruta para cerrar sidebar en pantallas pequeñas
    if (isBrowser && window.innerWidth < 768) {
      sidebarOpen = false;
    }
  });
  
  // Reacción al cambio de ruta
  $: if ($page && isBrowser && window.innerWidth < 768) {
    sidebarOpen = false;
  }
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
          <li class="sidebar-item {isActive(item.path) ? 'active' : ''}">
            <a href={item.path} class="sidebar-link {isActive(item.path) ? 'active' : ''}">
              <i class="bi bi-{item.icon}"></i>
              <span>{item.name}</span>
            </a>
          </li>
        {/each}
        
        <!-- Botón para salir del panel de administración -->
        <li class="sidebar-item exit-admin">
          <button on:click={promptExitAdminPanel} class="sidebar-link exit-button">
            <i class="bi bi-box-arrow-left"></i>
            <span>Salir del panel</span>
          </button>
        </li>
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
          {#if isActive(item.path)}
            <h1>{item.name}</h1>
          {/if}
        {/each}
      </div>
      
      <!-- Botón para salir en la cabecera (visible en pantallas grandes) -->
      <div class="ml-auto">
        <button on:click={promptExitAdminPanel} class="exit-header-button">
          <i class="bi bi-box-arrow-left mr-2"></i>
          <span>Volver al sitio</span>
        </button>
      </div>
    </header>
    
    <main class="admin-main">
      <slot></slot>
    </main>
  </div>
  
  <!-- Modal de confirmación para salir -->
  {#if showExitConfirmation}
    <div 
      class="modal-backdrop"
      role="presentation"
    >
      <div 
        class="modal-content" 
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-title"
      >
        <div class="modal-header">
          <h3 id="modal-title">¿Salir del panel de administración?</h3>
          <button 
            class="close-button" 
            on:click={cancelExit}
            aria-label="Cerrar diálogo"
          >
            <i class="bi bi-x-lg" aria-hidden="true"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>¿Estás seguro de que deseas salir del panel de administración y volver al sitio principal?</p>
        </div>
        <div class="modal-footer">
          <button 
            class="cancel-button" 
            on:click={cancelExit} 
            bind:this={cancelButtonRef}
          >
            Cancelar
          </button>
          <button class="confirm-button" on:click={exitAdminPanel}>Salir</button>
        </div>
      </div>
      
      <!-- Capa invisible para cerrar el modal al hacer clic fuera -->
      <button 
        class="backdrop-button"
        on:click={cancelExit}
        aria-label="Cerrar diálogo"
      ></button>
    </div>
  {/if}
</div>

<style>
  .admin-layout {
    display: flex;
    min-height: 100vh;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #121212;
    color: #f8f9fa;
  }
  
  /* Sidebar */
  .sidebar {
    width: 250px;
    background-color: #212529;
    color: white;
    height: 100vh;
    overflow-y: auto;
    transition: transform 0.3s ease;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    flex-direction: column;
  }
  
  .sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .sidebar-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    background: linear-gradient(to right, #fff, #a78bfa);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
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
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }
  
  .sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }
  
  .sidebar-item {
    margin-bottom: 0.25rem;
  }
  
  .sidebar-item.exit-admin {
    margin-top: auto;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 0.5rem;
    margin-top: 1rem;
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
  
  .sidebar-link:hover {
    background-color: rgba(109, 40, 217, 0.2);
    color: white;
  }
  
  .sidebar-link.active {
    background-color: rgba(109, 40, 217, 0.3);
    color: white;
    border-left: 3px solid #6d28d9;
  }
  
  .sidebar-link i {
    margin-right: 0.75rem;
    width: 20px;
    text-align: center;
  }
  
  .exit-button {
    color: #ff6b6b;
  }
  
  .exit-button:hover {
    background-color: rgba(255, 107, 107, 0.1);
  }
  
  /* Contenido principal */
  .main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
    background-color: #121212;
  }
  
  .admin-header {
    background-color: #1e1e1e;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
    color: white;
  }
  
  .header-title h1 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 500;
    color: white;
  }
  
  .exit-header-button {
    background: none;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .exit-header-button:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  .ml-auto {
    margin-left: auto;
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
    
    .exit-header-button span {
      display: none;
    }
    
    .exit-header-button {
      padding: 0.5rem;
    }
    
    .exit-header-button i {
      margin-right: 0;
    }
  }
  
  /* Estilos para el modal de confirmación */
  .modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(3px);
  }
  
  .modal-content {
    background-color: #212529;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
  }
  
  .modal-header {
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: white;
  }
  
  .close-button {
    background: none;
    border: none;
    color: #aaa;
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
  }
  
  .close-button:hover {
    color: white;
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  .modal-body {
    padding: 1.5rem;
  }
  
  .modal-body p {
    margin: 0;
    color: #ccc;
    line-height: 1.5;
  }
  
  .modal-footer {
    padding: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
  }
  
  .cancel-button {
    background-color: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .cancel-button:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  .confirm-button {
    background-color: #ff6b6b;
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .confirm-button:hover {
    background-color: #ff5252;
  }
  
  /* Botón invisible que cubre toda la pantalla excepto el modal */
  .backdrop-button {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: transparent;
    border: none;
    cursor: pointer;
    z-index: -1; /* Colocarlo detrás del contenido del modal */
  }
</style>