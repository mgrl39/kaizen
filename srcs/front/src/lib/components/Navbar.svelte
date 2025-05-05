<script lang="ts">
  import { onMount, tick } from 'svelte';
  import { page } from '$app/stores';
  import { fly, fade } from 'svelte/transition';
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';
  
  // Importar el sistema de internacionalización
  import { t } from '$lib/i18n';
  import LanguageSelector from '$lib/components/LanguageSelector.svelte';

  // Tipado mejorado
  interface NavItem {
    url: string;
    icon: string;
    text: string;
    i18nKey: string; // Clave para traducción
    divider?: boolean;
    action?: string;
    children?: NavItem[];
  }

  const navItems: NavItem[] = [
    {url: '/cinemas', icon: 'building', text: 'Cines', i18nKey: 'cinemas'},
    {url: '/movies', icon: 'film', text: 'Películas', i18nKey: 'movies'}
  ];

  const userMenu: NavItem[] = [
    {url: '/profile', icon: 'person', text: 'Mi Perfil', i18nKey: 'profile'},
    {url: '/bookings', icon: 'ticket', text: 'Mis Reservas', i18nKey: 'bookings'},
    {divider: true, url: '', icon: '', text: '', i18nKey: ''},
    {url: 'javascript:void(0)', icon: 'box-arrow-right', text: 'Cerrar Sesión', i18nKey: 'logout', action: 'logout'}
  ];

  // Opción Admin para usuarios con rol de administrador
  const adminOption: NavItem = {
    url: '/admin/dashboard', icon: 'speedometer2', text: 'Panel Admin', i18nKey: 'adminPanel'
  };

  // Estado
  let isAuthenticated: boolean = false;
  let isAdmin: boolean = false;
  let userName: string = 'Usuario';
  let userAvatar: string | null = null;
  let scrolled = false;
  let loadingProfile: boolean = true;
  let mobileMenuOpen = false;
  let dropdownOpen = false;
  let notificationCount = 0;

  // Referencia al dropdown para cerrar al hacer clic fuera
  let dropdownElement: HTMLElement;

  // Acción para detectar clics fuera de un elemento
  function clickOutside(node: HTMLElement, { enabled: initialEnabled, cb }: { enabled: boolean, cb: () => void }) {
    const handleClick = (event: MouseEvent) => {
      if (!node.contains(event.target as Node) && !event.defaultPrevented) {
        cb();
      }
    };
    
    function update({ enabled }: { enabled: boolean }) {
      if (enabled) {
        document.addEventListener('click', handleClick, true);
      } else {
        document.removeEventListener('click', handleClick, true);
      }
    }
    
    update({ enabled: initialEnabled });
    
    return {
      update,
      destroy() {
        document.removeEventListener('click', handleClick, true);
      }
    };
  }

  async function fetchProfile() {
    const token = localStorage.getItem('token');
    if (!token) {
      isAuthenticated = false;
      loadingProfile = false;
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
        userName = data.user.name || data.user.username;
        userAvatar = data.user.avatar || null; // Asumiendo que el API podría devolver un avatar
        notificationCount = data.notifications?.unread || 0; // Asumiendo que el API podría devolver notificaciones
        
        // Detectar si es administrador
        isAdmin = data.user.role === 'admin';
      } else {
        isAuthenticated = false;
        localStorage.removeItem('token');
      }
    } catch {
      isAuthenticated = false;
      localStorage.removeItem('token');
    } finally {
      loadingProfile = false;
    }
  }

  async function handleLogout(event: Event) {
    event.preventDefault();
    const token = localStorage.getItem('token');
    if (token) {
      try {
        const response = await fetch(`${API_URL}/logout`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        if (!response.ok) {
          throw new Error('Error al cerrar sesión');
        }
      } catch (e) {
        console.error('Error en logout:', e);
      } finally {
        localStorage.removeItem('token');
        isAuthenticated = false;
        userName = 'Usuario';
        await goto('/login');
      }
    }
  }

  function toggleDropdown() {
    dropdownOpen = !dropdownOpen;
  }

  function closeDropdown() {
    dropdownOpen = false;
  }

  function toggleMobileMenu() {
    mobileMenuOpen = !mobileMenuOpen;
  }

  function isActive(path: string): boolean {
    if (path === '/') return $page.url.pathname === '/';
    return $page.url.pathname.startsWith(path);
  }

  onMount(() => {
    fetchProfile();

    const handleScroll = () => {
      scrolled = window.scrollY > 20;
    };
    
    window.addEventListener('scroll', handleScroll);
    
    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  });

  // Función para cerrar menú móvil al cambiar de ruta
  $: if ($page) {
    mobileMenuOpen = false;
    dropdownOpen = false;
  }
</script>

<nav class="navbar navbar-expand-md navbar-dark fixed-top {scrolled ? 'scrolled' : ''}" aria-label="Navegación principal">
  <div class="container">
    <a href="/" class="navbar-brand d-flex align-items-center" aria-label="Inicio">
      <span class="logo-text">Kaizen</span>
    </a>

    <!-- Botón de menú móvil -->
    <button 
      class="navbar-toggler" 
      type="button" 
      aria-controls="navbarContent"
      aria-expanded={mobileMenuOpen} 
      aria-label="Alternar navegación"
      on:click={toggleMobileMenu}
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div 
      class="collapse navbar-collapse {mobileMenuOpen ? 'show' : ''}" 
      id="navbarContent"
      transition:fly={{ y: -10, duration: 200, opacity: 0.95 }}
    >
      <ul class="navbar-nav ms-auto align-items-center">
        {#each navItems as item}
          <li class="nav-item">
            <a 
              href={item.url} 
              class="nav-link {isActive(item.url) ? 'active' : ''}"
              aria-current={isActive(item.url) ? 'page' : undefined}
            >
              <i class="bi bi-{item.icon} me-1" aria-hidden="true"></i>
              <span>{$t(item.i18nKey)}</span>
            </a>
          </li>
        {/each}
        
        {#if isAdmin}
          <li class="nav-item">
            <a 
              href={adminOption.url} 
              class="nav-link {isActive(adminOption.url) ? 'active' : ''}"
              aria-current={isActive(adminOption.url) ? 'page' : undefined}
            >
              <i class="bi bi-{adminOption.icon} me-1" aria-hidden="true"></i>
              <span>{$t(adminOption.i18nKey)}</span>
            </a>
          </li>
        {/if}
        
        <!-- Selector de idioma -->
        <li class="nav-item me-2">
          <LanguageSelector />
        </li>
        
        {#if loadingProfile}
          <li class="nav-item ms-2">
            <span class="navbar-text loading-indicator">
              <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              <span>{$t('loading')}</span>
            </span>
          </li>
        {:else}
          {#if isAuthenticated}
            <!-- Botón de notificaciones -->
            <li class="nav-item me-2">
              <a href="/notifications" class="nav-link position-relative">
                <i class="bi bi-bell" aria-hidden="true"></i>
                {#if notificationCount > 0}
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                    transition:fade={{ duration: 200 }}>
                    {notificationCount}
                    <span class="visually-hidden">{$t('unreadNotifications')}</span>
                  </span>
                {/if}
              </a>
            </li>
            
            <!-- Menú de usuario -->
            <li class="nav-item dropdown">
              <div class="dropdown-wrapper" bind:this={dropdownElement} use:clickOutside={{ enabled: dropdownOpen, cb: closeDropdown }}>
                <button 
                  class="nav-link dropdown-toggle user-menu-button" 
                  aria-expanded={dropdownOpen}
                  aria-haspopup="true"
                  type="button"
                  on:click={toggleDropdown}
                >
                  {#if userAvatar}
                    <img src={userAvatar} alt="" class="avatar me-1" width="24" height="24" />
                  {:else}
                    <i class="bi bi-person-circle me-1" aria-hidden="true"></i>
                  {/if}
                  <span>{userName}</span>
                </button>
                
                {#if dropdownOpen}
                  <ul 
                    class="dropdown-menu dropdown-menu-end show shadow-sm" 
                    transition:fly={{ y: 5, duration: 200, opacity: 0.98 }}
                  >
                    {#each userMenu as item}
                      {#if item.divider}
                        <li><hr class="dropdown-divider"></li>
                      {:else if item.action === 'logout'}
                        <li>
                          <button 
                            class="dropdown-item" 
                            type="button" 
                            on:click={handleLogout}
                          >
                            <i class="bi bi-{item.icon} me-1" aria-hidden="true"></i>
                            <span>{$t(item.i18nKey)}</span>
                          </button>
                        </li>
                      {:else}
                        <li>
                          <a 
                            class="dropdown-item {isActive(item.url) ? 'active' : ''}" 
                            href={item.url}
                            aria-current={isActive(item.url) ? 'page' : undefined}
                          >
                            <i class="bi bi-{item.icon} me-1" aria-hidden="true"></i>
                            <span>{$t(item.i18nKey)}</span>
                          </a>
                        </li>
                      {/if}
                    {/each}
                  </ul>
                {/if}
              </div>
            </li>
          {:else}
            <li class="nav-item ms-2 d-flex">
              <a href="/register" class="btn btn-outline-light me-2 d-none d-sm-inline-block">
                {$t('register')}
              </a>
              <a href="/login" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>
                <span>{$t('login')}</span>
              </a>
            </li>
          {/if}
        {/if}
      </ul>
    </div>
  </div>
</nav>

<style>
  .navbar {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
    transition: all 0.3s ease;
    background-color: rgba(18, 18, 18, 0.85);
    backdrop-filter: blur(10px);
  }
  
  .navbar.scrolled {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    background-color: rgba(18, 18, 18, 0.95);
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
  }
  
  .logo-text {
    font-weight: 700;
    font-size: 1.4rem;
    background: linear-gradient(to right, #fff, #a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    transition: all 0.3s ease;
  }
  
  .nav-link {
    position: relative;
    padding: 0.5rem 0.8rem;
    font-weight: 500;
    transition: color 0.2s;
  }
  
  .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0.8rem;
    right: 0.8rem;
    height: 2px;
    background: linear-gradient(to right, #6d28d9, transparent);
    border-radius: 2px;
  }
  
  .user-menu-button {
    display: flex;
    align-items: center;
    background: transparent;
    border: none;
    color: white;
    padding: 0.5rem 0.8rem;
    font-weight: 500;
    cursor: pointer;
  }
  
  .dropdown-wrapper {
    position: relative;
  }
  
  .dropdown-menu {
    animation-fill-mode: forwards;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background-color: #212529;
  }
  
  .dropdown-item {
    color: #f8f9fa;
    padding: 0.5rem 1.25rem;
    transition: background-color 0.2s;
  }
  
  .dropdown-item:hover {
    background-color: rgba(109, 40, 217, 0.2);
    color: #fff;
  }
  
  .dropdown-item.active {
    background-color: #6d28d9;
  }
  
  .loading-indicator {
    display: flex;
    align-items: center;
    color: rgba(255, 255, 255, 0.7);
  }
  
  .avatar {
    border-radius: 50%;
    object-fit: cover;
  }
  
  @media (max-width: 768px) {
    .navbar-nav {
      padding-top: 1rem;
      padding-bottom: 1rem;
    }
    
    .dropdown-menu {
      position: static !important;
      float: none;
      width: auto;
      margin-top: 0;
      border: 0;
      box-shadow: none;
      background-color: rgba(33, 37, 41, 0.5);
    }
    
    .nav-item {
      width: 100%;
    }
    
    .user-menu-button {
      padding: 0.75rem 0;
      justify-content: flex-start;
    }
  }
</style> 