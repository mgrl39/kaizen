<script lang="ts">
  import { t } from '$lib/i18n';
  import { page } from '$app/stores';
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  
  // Estado para el sidebar
  let sidebarCollapsed = false;
  let isBrowser = false;

  
  // Estructura del menú de navegación simplificada
  const menuItems = [
    { id: 'dashboard', name: 'Dashboard', icon: 'speedometer2', path: '/admin' },
    { id: 'movies', name: 'Películas', icon: 'film', path: '/admin/movies' },
    { id: 'cinemas', name: 'Cines', icon: 'building', path: '/admin/cinemas' },
    { id: 'users', name: 'Usuarios', icon: 'people', path: '/admin/users' },
    { id: 'bookings', name: $t('bookings'), icon: 'ticket-perforated', path: '/admin/bookings' },
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
  
  // Función para salir del panel de administración
  function exitAdminPanel() {
    goto('/');
  }
  
  onMount(() => {
    isBrowser = true;
    
    // Ensure body has correct styles immediately
    document.body.classList.add('admin-route');
    document.body.style.margin = '0';
    document.body.style.padding = '0';
  });
</script>

<svelte:head>
  <style>
    body {
      margin: 0 !important;
      padding: 0 !important;
    }
  </style>
</svelte:head>

<div class="admin-layout d-flex vh-100">
  <!-- Sidebar -->
  <div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark sidebar" style="width: {sidebarCollapsed ? '4.5rem' : '280px'}; transition: width 0.3s;">
    <a href="/admin" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      {#if !sidebarCollapsed}
        <span class="fs-4">Admin Panel</span>
      {:else}
        <i class="bi bi-speedometer2"></i>
      {/if}
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      {#each menuItems as item}
        <li class="nav-item">
          <a href={item.path} class="nav-link {isActive(item.path) ? 'active' : 'text-white'} d-flex align-items-center">
            <i class="bi bi-{item.icon} me-2"></i>
            {#if !sidebarCollapsed}
              <span>{item.name}</span>
            {/if}
          </a>
        </li>
      {/each}
    </ul>
    <hr>
    <div class="dropdown">
      <button class="d-flex align-items-center text-white text-decoration-none border-0 bg-transparent p-0" 
              on:click={exitAdminPanel}>
        <i class="bi bi-box-arrow-left me-2"></i>
        {#if !sidebarCollapsed}
          <span>Salir</span>
        {/if}
      </button>
    </div>
  </div>

  <!-- Main content -->
  <div class="admin-content flex-grow-1 overflow-auto">
    <div class="px-3 py-2 border-bottom bg-light">
      <div class="container-fluid d-flex align-items-center">
        <button class="btn btn-sm" on:click={() => sidebarCollapsed = !sidebarCollapsed}>
          <i class="bi bi-list"></i>
        </button>
        
        <h1 class="h5 mb-0 ms-3">
          {#each menuItems as item}
            {#if isActive(item.path)}
              {item.name}
            {/if}
          {/each}
        </h1>
      </div>
    </div>
    
    <main class="container-fluid py-3">
      <slot></slot>
    </main>
  </div>
</div>

<style>
  :global(body) {
    overflow: hidden;
    margin: 0 !important;
    padding: 0 !important;
  }

  :global(body.admin-route) {
    margin-top: 0 !important;
    padding-top: 0 !important;
  }

  .admin-layout {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
  }
  
  .sidebar {
    height: 100%;
    overflow-y: auto;
  }
  
  .admin-content {
    height: 100%;
  }
</style>