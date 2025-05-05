<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { page } from '$app/stores';
  import { API_URL } from '$lib/config';

  // Importaciones de Carbon Components (sin los iconos problemáticos)
  import {
    Header,
    HeaderUtilities,
    HeaderGlobalAction,
    HeaderNav,
    HeaderNavItem,
    SideNav,
    SideNavItems,
    SideNavMenu,
    SideNavMenuItem,
    SideNavLink,
    SkipToContent,
    Content,
    Theme,
    Button,
    Loading
  } from "carbon-components-svelte";
  
  // Seguimos usando Bootstrap Icons
  import 'bootstrap-icons/font/bootstrap-icons.css';

  export let data;

  let isAdmin = false;
  let user = null;
  let loading = true;
  let isSideNavExpanded = true;

  // Menú de administración con iconos de Bootstrap
  const adminMenus = [
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
        console.log("User data:", data.user);
        isAdmin = data.user && data.user.role === 'admin';
        
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

  function toggleSideNav() {
    isSideNavExpanded = !isSideNavExpanded;
  }

  function handleNavigation(url) {
    goto(url);
  }

  // Comprobar autorización al cargar
  onMount(() => {
    checkAuth();
  });

  // Obtener el título de la página actual
  $: currentPageTitle = $page.url.pathname.split('/').pop().charAt(0).toUpperCase() + $page.url.pathname.split('/').pop().slice(1);
</script>

<!-- Importar los estilos base de Carbon -->
<svelte:head>
  <link rel="stylesheet" href="https://unpkg.com/carbon-components/css/carbon-components.min.css" />
  <style>
    :global(body) {
      background-color: #f4f4f4 !important;
      margin: 0 !important;
      padding: 0 !important;
      height: 100vh !important;
      overflow: hidden !important;
    }
    
    :global(.bx--side-nav) {
      position: fixed !important;
      height: 100% !important;
    }
    
    :global(.bx--content) {
      margin-left: 16rem !important;
      transition: margin-left 0.3s ease !important;
      height: calc(100vh - 3rem) !important;
      overflow-y: auto !important;
      padding: 1.5rem !important;
    }
    
    :global(.bx--side-nav--collapsed ~ .bx--content) {
      margin-left: 3rem !important;
    }
    
    /* Estilo para los iconos de Bootstrap dentro de Carbon */
    :global(.carbon-icon-wrapper) {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 16px;
      height: 16px;
      margin-right: 8px;
    }
  </style>
</svelte:head>

<Theme theme="g90">
  {#if loading}
    <Loading withOverlay description="Cargando..." />
  {:else if isAdmin}
    <Header platformName="Kaizen Admin" bind:isSideNavExpanded on:toggle={toggleSideNav}>
      <HeaderNav>
        <HeaderNavItem text="Cines" href="/admin/cinemas" />
        <HeaderNavItem text="Películas" href="/admin/movies" />
        <HeaderNavItem text="Usuarios" href="/admin/users" />
      </HeaderNav>
      <HeaderUtilities>
        <HeaderGlobalAction tooltipAlignment="center" aria-label="Perfil">
          <div class="carbon-icon-wrapper">
            <i class="bi bi-person"></i>
          </div>
        </HeaderGlobalAction>
        <HeaderGlobalAction tooltipAlignment="center" aria-label="Ajustes">
          <div class="carbon-icon-wrapper">
            <i class="bi bi-gear"></i>
          </div>
        </HeaderGlobalAction>
        <HeaderGlobalAction tooltipAlignment="center" aria-label="Cerrar sesión" on:click={handleLogout}>
          <div class="carbon-icon-wrapper">
            <i class="bi bi-box-arrow-right"></i>
          </div>
        </HeaderGlobalAction>
      </HeaderUtilities>
    </Header>

    <SideNav bind:expanded={isSideNavExpanded} rail>
      <SideNavItems>
        {#each adminMenus as section}
          <SideNavMenu text={section.title}>
            {#each section.items as item}
              <SideNavMenuItem 
                text={item.name}
                href={item.url}
                on:click={() => handleNavigation(item.url)}
              >
                <div class="carbon-icon-wrapper" slot="icon">
                  <i class="bi bi-{item.icon}"></i>
                </div>
              </SideNavMenuItem>
            {/each}
          </SideNavMenu>
        {/each}
        <SideNavMenu text="Configuración">
          <SideNavMenuItem 
            text="Mi Perfil"
            href="/admin/profile"
            on:click={() => handleNavigation('/admin/profile')}
          >
            <div class="carbon-icon-wrapper" slot="icon">
              <i class="bi bi-person"></i>
            </div>
          </SideNavMenuItem>
          <SideNavMenuItem 
            text="Ajustes"
            href="/admin/settings"
            on:click={() => handleNavigation('/admin/settings')}
          >
            <div class="carbon-icon-wrapper" slot="icon">
              <i class="bi bi-gear"></i>
            </div>
          </SideNavMenuItem>
          <SideNavMenuItem 
            text="Cerrar Sesión"
            on:click={handleLogout}
          >
            <div class="carbon-icon-wrapper" slot="icon">
              <i class="bi bi-box-arrow-right"></i>
            </div>
          </SideNavMenuItem>
        </SideNavMenu>
      </SideNavItems>
    </SideNav>

    <Content>
      <div class="bx--grid">
        <div class="bx--row">
          <div class="bx--col">
            <h2 class="bx--type-productive-heading-04" style="margin-bottom: 1.5rem;">
              {currentPageTitle}
            </h2>
            <slot />
          </div>
        </div>
      </div>
    </Content>
  {/if}
</Theme>