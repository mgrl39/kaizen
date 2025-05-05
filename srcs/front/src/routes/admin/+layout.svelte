<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { page } from '$app/stores';
  import { API_URL } from '$lib/config';

  // Importaciones de Carbon Components
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
    SkipToContent,
    Content,
    Theme,
    Loading
  } from "carbon-components-svelte";
  
  // Iconos de Bootstrap
  import 'bootstrap-icons/font/bootstrap-icons.css';

  // Opción 1: Usar export const para datos de referencia externa
  export const data: any = {}; // Cambio de 'export let' a 'export const'
  
  // O, opción 2: Mantener 'export let' pero silenciar el error con un comentario
  // @ts-ignore - Requerido por SvelteKit pero no usado
  // export let data: any;

  let isAdmin = false;
  let user: any = null;
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

  // Añadimos tipo para url
  function handleNavigation(url: string) {
    goto(url);
  }

  // Comprobar autorización al cargar
  onMount(() => {
    checkAuth();
  });

  // Usando nullish coalescing para evitar undefined
  $: currentPageTitle = $page.url.pathname.split('/')
    .pop()
    ?.charAt(0)
    .toUpperCase() + 
    ($page.url.pathname.split('/').pop()?.slice(1) || '');
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
    <Header platformName="Kaizen Admin">
      <svelte:fragment slot="skip-to-content">
        <SkipToContent />
      </svelte:fragment>
      
      <HeaderNav>
        <HeaderNavItem text="Cines" href="/admin/cinemas" />
        <HeaderNavItem text="Películas" href="/admin/movies" />
        <HeaderNavItem text="Usuarios" href="/admin/users" />
      </HeaderNav>
      
      <HeaderUtilities>
        <HeaderGlobalAction aria-label="Perfil" on:click={() => goto('/admin/profile')}>
          <div class="carbon-icon-wrapper">
            <i class="bi bi-person"></i>
          </div>
        </HeaderGlobalAction>
        <HeaderGlobalAction aria-label="Ajustes" on:click={() => goto('/admin/settings')}>
          <div class="carbon-icon-wrapper">
            <i class="bi bi-gear"></i>
          </div>
        </HeaderGlobalAction>
        <HeaderGlobalAction aria-label="Cerrar sesión" on:click={handleLogout}>
          <div class="carbon-icon-wrapper">
            <i class="bi bi-box-arrow-right"></i>
          </div>
        </HeaderGlobalAction>
      </HeaderUtilities>
    </Header>

    <SideNav isOpen={isSideNavExpanded} rail>
      <SideNavItems>
        {#each adminMenus as section}
          <SideNavMenu title={section.title}>
            {#each section.items as item}
              <SideNavMenuItem 
                href={item.url}
                on:click={() => handleNavigation(item.url)}
              >
                <div class="carbon-icon-wrapper">
                  <i class="bi bi-{item.icon}"></i>
                </div>
                {item.name}
              </SideNavMenuItem>
            {/each}
          </SideNavMenu>
        {/each}
        <SideNavMenu title="Configuración">
          <SideNavMenuItem 
            href="/admin/profile"
            on:click={() => handleNavigation('/admin/profile')}
          >
            <div class="carbon-icon-wrapper">
              <i class="bi bi-person"></i>
            </div>
            Mi Perfil
          </SideNavMenuItem>
          <SideNavMenuItem 
            href="/admin/settings"
            on:click={() => handleNavigation('/admin/settings')}
          >
            <div class="carbon-icon-wrapper">
              <i class="bi bi-gear"></i>
            </div>
            Ajustes
          </SideNavMenuItem>
          <SideNavMenuItem 
            on:click={handleLogout}
          >
            <div class="carbon-icon-wrapper">
              <i class="bi bi-box-arrow-right"></i>
            </div>
            Cerrar Sesión
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