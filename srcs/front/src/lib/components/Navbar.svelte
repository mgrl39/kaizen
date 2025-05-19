<script lang="ts">
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';
  import { t, currentLanguage, languages } from '$lib/i18n';
  import LanguageSelector from '$lib/components/LanguageSelector.svelte';
  import { writable, get } from 'svelte/store';
  import { theme, toggleTheme, initTheme } from '$lib/theme';
  import type { NavItem } from '$lib/types';
  
  // Stores para estado de usuario
  const authState = writable({
    isAuthenticated: false,
    isAdmin: false,
    userName: 'Usuario',
    loading: true
  });
  
  // Estado local para UI
  let mobileMenuOpen = false;
  let scrolled = false;
  let showAdminConfirmDialog = false;
  let userMenuOpen = false;
  let languageSelectorOpen = false;
  
  $: ({ isAuthenticated, isAdmin, userName, loading } = $authState);
  $: currentTheme = $theme; // Para acceder al tema actual
  
  // Definir estructura de navegación - importado de navbar.ts
  const navItems: NavItem[] = [
    { 
      url: '/cinemas', 
      icon: 'building', 
      text: 'Cines'
    },
    { 
      url: '/movies', 
      icon: 'film', 
      text: 'Películas'
    }
  ];
  
  // Definir elementos del menú de usuario - importado de navbar.ts
  const userMenu: NavItem[] = [
    { 
      url: '/profile', 
      icon: 'person', 
      text: 'Mi Perfil'
    },
    { 
      url: '/bookings', 
      icon: 'ticket', 
      text: 'Mis Reservas'
    },
    { 
      divider: true, 
      url: '', 
      icon: '', 
      text: '' 
    },
    { 
      url: '#logout', 
      icon: 'box-arrow-right', 
      text: 'Cerrar Sesión', 
      action: 'logout' 
    }
  ];
  
  // Verificar y establecer idioma predeterminado - importado de navbar.ts
  function setupDefaultLanguage() {
    if (!languages.includes(get(currentLanguage))) {
      // Si el idioma no es válido, usar español como predeterminado
      currentLanguage.set('es');
    }
  }
  
  function toggleMobileMenu() {
    mobileMenuOpen = !mobileMenuOpen;
  }
  
  function toggleUserMenu() {
    userMenuOpen = !userMenuOpen;
    // Cerrar el selector de idioma si está abierto
    if (userMenuOpen) {
      languageSelectorOpen = false;
    }
  }
  
  function toggleLanguageSelector() {
    languageSelectorOpen = !languageSelectorOpen;
    // Cerrar el menú de usuario si está abierto
    if (languageSelectorOpen) {
      userMenuOpen = false;
    }
  }
  
  // Función para manejar el cambio de tema
  function handleThemeToggle() {
    toggleTheme();
  }
  
  // Función para cerrar el menú de usuario cuando se hace clic fuera
  function handleClickOutside(event: MouseEvent) {
    const userMenu = document.getElementById('user-menu-dropdown');
    const userButton = document.getElementById('user-menu-button');
    const langSelector = document.getElementById('language-selector-dropdown');
    const langButton = document.getElementById('language-selector-button');
    
    // Cerrar menú de usuario si el clic fue fuera
    if (userMenu && userButton && !userMenu.contains(event.target as Node) && !userButton.contains(event.target as Node)) {
      userMenuOpen = false;
    }
    
    // Cerrar selector de idioma si el clic fue fuera
    if (langSelector && langButton && !langSelector.contains(event.target as Node) && !langButton.contains(event.target as Node)) {
      languageSelectorOpen = false;
    }
  }
  
  onMount(() => {
    // Inicializar el tema
    initTheme();
    
    // Configurar idioma predeterminado
    setupDefaultLanguage();
    
    // Cargar estado guardado inmediatamente para reducir parpadeo
    const cachedState = localStorage.getItem('authState');
    if (cachedState) {
      try {
        const parsed = JSON.parse(cachedState);
        authState.set({ 
          ...parsed, 
          loading: true
        });
      } catch (e) {
        // Ignorar errores de parsing
      }
    }
    
    fetchProfile();
    
    const handleScroll = () => {
      scrolled = window.scrollY > 20;
    };
    window.addEventListener('scroll', handleScroll);
    
    // Añadir event listener para cerrar el menú al hacer clic fuera
    document.addEventListener('click', handleClickOutside);
    
    return () => {
      window.removeEventListener('scroll', handleScroll);
      document.removeEventListener('click', handleClickOutside);
    };
  });
  
  // Función para manejar el clic en el enlace de administrador
  function handleAdminClick(event: MouseEvent) {
    // Prevenir la navegación predeterminada
    event.preventDefault();
    
    // Mostrar el diálogo de confirmación
    showAdminConfirmDialog = true;
  }
  
  // Función para confirmar y navegar al panel de administrador
  function confirmAdminAccess() {
    showAdminConfirmDialog = false;
    goto('/admin');
  }
  
  // Función para cancelar el acceso al panel de administrador
  function cancelAdminAccess() {
    showAdminConfirmDialog = false;
  }
  
  async function fetchProfile() {
    const token = localStorage.getItem('token');
    if (!token) {
      authState.set({
        isAuthenticated: false,
        isAdmin: false,
        userName: 'Usuario',
        loading: false
      });
      localStorage.removeItem('authState');
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
        const newState = {
          isAuthenticated: true,
          isAdmin: data.user.role === 'admin',
          userName: data.user.name || data.user.username,
          loading: false
        };
        
        // Actualizar store
        authState.set(newState);
        
        // Guardar en localStorage para carga rápida en futuras visitas
        localStorage.setItem('authState', JSON.stringify(newState));
      } else {
        authState.set({
          isAuthenticated: false,
          isAdmin: false,
          userName: 'Usuario',
          loading: false
        });
        localStorage.removeItem('token');
        localStorage.removeItem('authState');
      }
    } catch (err) {
      authState.set({
        isAuthenticated: false,
        isAdmin: false,
        userName: 'Usuario',
        loading: false
      });
      localStorage.removeItem('token');
      localStorage.removeItem('authState');
    }
  }
  
  async function handleLogout(event: MouseEvent | null) {
    if (event) event.preventDefault();
    
    const token = localStorage.getItem('token');
    if (token) {
      // Actualizar UI inmediatamente para mejorar percepción de velocidad
      authState.set({
        isAuthenticated: false,
        isAdmin: false,
        userName: 'Usuario',
        loading: false
      });
      
      localStorage.removeItem('token');
      localStorage.removeItem('authState');
      
      // Hacer petición de logout en segundo plano
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

  function isActive(path: string): boolean {
    if (path === '/') return $page.url.pathname === '/';
    return $page.url.pathname.startsWith(path);
  }
  
  function handleNavItemClick(item: NavItem, event: MouseEvent) {
    if (item.action === 'logout') {
      handleLogout(event);
    }
  }
</script>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top {scrolled ? 'shadow bg-opacity-95' : ''} {$theme === 'dark' ? 'bg-dark' : 'bg-primary'}">
  <div class="container">
    <!-- Logo -->
    <a href="/" class="navbar-brand">
      Kaizen
    </a>
    
    <!-- Botón de menú móvil -->
    <button 
      class="navbar-toggler" 
      type="button" 
      on:click={toggleMobileMenu}
      aria-label={$t('toggleMenu')}
      aria-expanded={mobileMenuOpen}
      aria-controls="navbarContent"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Contenido del navbar -->
    <div class="collapse navbar-collapse {mobileMenuOpen ? 'show' : ''}" id="navbarContent">
      <!-- Elementos de navegación -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        {#each navItems as item}
          <li class="nav-item">
            <a 
              href={item.url} 
              class="nav-link {isActive(item.url) ? 'active' : ''}"
            >
              <i class="bi bi-{item.icon} me-1"></i>
              <span>{item.text}</span>
            </a>
          </li>
        {/each}
        
        {#if isAdmin}
          <li class="nav-item">
            <a 
              href="/admin" 
              on:click={handleAdminClick}
              class="nav-link {isActive('/admin') ? 'active' : ''}"
            >
              <i class="bi bi-speedometer2 me-1"></i>
              <span>{$t('adminPanel')}</span>
            </a>
          </li>
        {/if}
      </ul>
      
      <!-- Acciones de usuario -->
      <div class="d-flex align-items-center">
        <!-- Selector de tema -->
        <button 
          class="btn btn-sm {$theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'} me-2" 
          on:click={handleThemeToggle}
          aria-label="Toggle theme"
        >
          {#if $theme === 'dark'}
            <i class="bi bi-sun"></i>
          {:else}
            <i class="bi bi-moon"></i>
          {/if}
        </button>
        
        <!-- Selector de idioma -->
        <div class="dropdown me-2">
          <button 
            id="language-selector-button"
            class="btn btn-sm {$theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
            on:click|stopPropagation={toggleLanguageSelector}
            aria-expanded={languageSelectorOpen}
          >
            <i class="bi bi-globe me-1"></i>
            <span class="d-none d-lg-inline">{$currentLanguage.toUpperCase()}</span>
          </button>
          
          <ul 
            id="language-selector-dropdown"
            class="dropdown-menu dropdown-menu-end {languageSelectorOpen ? 'show' : ''}"
          >
            <li>
              <button 
                class="dropdown-item {$currentLanguage === 'es' ? 'active' : ''}"
                on:click={() => { currentLanguage.set('es'); }}
              >
                <span class="me-2">🇪🇸</span>Español
              </button>
            </li>
            <li>
              <button 
                class="dropdown-item {$currentLanguage === 'en' ? 'active' : ''}"
                on:click={() => { currentLanguage.set('en'); }}
              >
                <span class="me-2">🇬🇧</span>English
              </button>
            </li>
          </ul>
        </div>
        
        {#if loading}
          <!-- Placeholder durante carga -->
          <div class="spinner-border spinner-border-sm text-light" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        {:else if isAuthenticated}
          <!-- Menú de usuario -->
          <div class="dropdown">
            <button 
              id="user-menu-button"
              class="btn btn-sm {$theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
              on:click|stopPropagation={toggleUserMenu}
              aria-expanded={userMenuOpen}
            >
              <i class="bi bi-person-circle me-1"></i>
              <span class="d-none d-lg-inline">{userName}</span>
              <i class="bi bi-chevron-down ms-1 small"></i>
            </button>
            
            <ul 
              id="user-menu-dropdown"
              class="dropdown-menu dropdown-menu-end {userMenuOpen ? 'show' : ''}"
            >
              {#each userMenu as item}
                {#if item.divider}
                  <li><hr class="dropdown-divider"></li>
                {:else}
                  <li>
                    <a 
                      href={item.url} 
                      class="dropdown-item {item.action === 'logout' ? 'text-danger' : ''}"
                      on:click={(e) => handleNavItemClick(item, e)}
                    >
                      <i class="bi bi-{item.icon} me-2"></i>
                      {item.text}
                    </a>
                  </li>
                {/if}
              {/each}
            </ul>
          </div>
        {:else}
          <!-- Botones de login/registro -->
          <a href="/login" class="btn btn-sm btn-primary me-2">
            <i class="bi bi-box-arrow-in-right me-1 d-lg-none"></i>
            <span>{$t('login')}</span>
          </a>
          <a href="/register" class="btn btn-sm btn-outline-light d-none d-sm-inline-block">
            <span>{$t('register')}</span>
          </a>
        {/if}
      </div>
    </div>
  </div>
</nav>

<!-- Modal de confirmación para acceso al panel de administrador -->
{#if showAdminConfirmDialog}
  <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="admin-dialog-title">Acceso a Panel de Administrador</h5>
          <button type="button" class="btn-close" on:click={cancelAdminAccess}></button>
        </div>
        <div class="modal-body">
          <p>Está a punto de acceder al panel de administración. ¿Desea continuar?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" on:click={cancelAdminAccess}>Cancelar</button>
          <button type="button" class="btn btn-primary" on:click={confirmAdminAccess}>Continuar</button>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade show"></div>
  </div>
{/if}

<style>
  /* Estilos adicionales para mejorar la apariencia */
  .navbar {
    transition: all 0.3s ease;
  }
  
  .navbar-brand {
    font-weight: 700;
    font-size: 1.4rem;
  }
  
  /* Ajustes para el modo oscuro/claro */
  :global([data-bs-theme="dark"]) .navbar-brand {
    color: #fff;
  }
  
  :global([data-bs-theme="light"]) .navbar-brand {
    color: #000;
  }
</style> 