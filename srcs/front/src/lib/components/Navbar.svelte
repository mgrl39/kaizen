<script>
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';
  import { t, currentLanguage, languages } from '$lib/i18n';
  import { writable, get } from 'svelte/store';
  import { theme, toggleTheme, initTheme } from '$lib/theme';
  
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
  
  // Definir user como una prop con un valor por defecto
  export let user = { name: '', email: '', role: '' };
  
  $: ({ isAuthenticated, isAdmin, userName, loading } = $authState);
  $: currentTheme = $theme || 'light';
  
  // Definir estructura de navegación
  const navItems = [
    { url: '/cinemas', icon: 'building', text: 'Cines' },
    { url: '/movies', icon: 'film', text: 'Películas' }
  ];
  
  // Definir elementos del menú de usuario
  const userMenu = [
    { url: '/profile', icon: 'person', text: 'Mi Perfil' },
    { url: '/bookings', icon: 'ticket', text: 'Mis Reservas' },
    { divider: true, url: '', icon: '', text: '' },
    { url: '#logout', icon: 'box-arrow-right', text: 'Cerrar Sesión', action: 'logout' }
  ];
  
  function setupDefaultLanguage() {
    if (!languages.includes(get(currentLanguage))) {
      currentLanguage.set('es');
    }
  }
  
  function toggleMobileMenu() {
    mobileMenuOpen = !mobileMenuOpen;
  }
  
  function toggleUserMenu() {
    userMenuOpen = !userMenuOpen;
    if (userMenuOpen) languageSelectorOpen = false;
  }
  
  function toggleLanguageSelector() {
    languageSelectorOpen = !languageSelectorOpen;
    if (languageSelectorOpen) userMenuOpen = false;
  }
  
  function handleThemeToggle() {
    toggleTheme();
  }
  
  function handleClickOutside(event) {
    const userMenu = document.getElementById('user-menu-dropdown');
    const userButton = document.getElementById('user-menu-button');
    const langSelector = document.getElementById('language-selector-dropdown');
    const langButton = document.getElementById('language-selector-button');
    
    if (userMenu && userButton && !userMenu.contains(event.target) && !userButton.contains(event.target)) {
      userMenuOpen = false;
    }
    
    if (langSelector && langButton && !langSelector.contains(event.target) && !langButton.contains(event.target)) {
      languageSelectorOpen = false;
    }
  }
  
  onMount(() => {
    // Inicializar Bootstrap dropdown manualmente
    import('bootstrap/js/dist/dropdown').then(module => {
      // Bootstrap dropdowns ya están inicializados con atributos data-bs
    });
    
    // Inicializar el tema
    initTheme();
    
    // Configurar idioma predeterminado
    setupDefaultLanguage();
    
    // Cargar estado guardado
    const cachedState = localStorage.getItem('authState');
    if (cachedState) {
      try {
        const parsed = JSON.parse(cachedState);
        authState.set({ ...parsed, loading: true });
      } catch (e) {
        // Ignorar errores de parsing
      }
    }
    
    fetchProfile();
    
    // Manejar scroll para efectos visuales
    const handleScroll = () => {
      scrolled = window.scrollY > 20;
    };
    window.addEventListener('scroll', handleScroll);
    
    // Cerrar menús al hacer clic fuera
    document.addEventListener('click', handleClickOutside);
    
    // Verificar si hay un token en localStorage
    const token = localStorage.getItem('token');
    if (token) {
      loadUserProfile();
    }
    
    return () => {
      window.removeEventListener('scroll', handleScroll);
      document.removeEventListener('click', handleClickOutside);
    };
  });
  
  function handleAdminClick(event) {
    event.preventDefault();
    showAdminConfirmDialog = true;
  }
  
  function confirmAdminAccess() {
    showAdminConfirmDialog = false;
    goto('/admin');
  }
  
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
        
        authState.set(newState);
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
  
  async function handleLogout(event) {
    if (event) event.preventDefault();
    
    const token = localStorage.getItem('token');
    if (token) {
      authState.set({
        isAuthenticated: false,
        isAdmin: false,
        userName: 'Usuario',
        loading: false
      });
      
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

  function isActive(path) {
    if (path === '/') return $page.url.pathname === '/';
    return $page.url.pathname.startsWith(path);
  }
  
  function handleNavItemClick(item, event) {
    if (item.action === 'logout') {
      handleLogout(event);
    }
  }

  // Si tienes una función para cargar el usuario, asegúrate de inicializar user correctamente
  async function loadUserProfile() {
    try {
      // Tu código para cargar el perfil de usuario
      const response = await fetch('/api/user/profile');
      if (response.ok) {
        user = await response.json();
      }
    } catch (error) {
      console.error('Error cargando perfil:', error);
    }
  }
</script>

<!-- Navbar con Bootstrap estándar -->
<nav class="navbar navbar-expand-lg fixed-top {scrolled ? 'shadow-sm' : ''}" data-bs-theme={currentTheme}>
  <div class="container">
    <!-- Logo -->
    <a href="/" class="navbar-brand">
      Kaizen
      <span class="badge bg-primary ms-1">Cinema</span>
    </a>
    
    <!-- Botón de hamburguesa para móviles -->
    <button 
      class="navbar-toggler" 
      type="button" 
      data-bs-toggle="collapse" 
      data-bs-target="#navbarContent" 
      aria-controls="navbarContent" 
      aria-expanded="false" 
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Contenido colapsable -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <!-- Elementos de navegación principal -->
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        {#each navItems as item}
          <li class="nav-item">
            <a 
              href={item.url} 
              class="nav-link {isActive(item.url) ? 'active fw-bold' : ''}"
              aria-current={isActive(item.url) ? 'page' : undefined}
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
              class="nav-link {isActive('/admin') ? 'active fw-bold' : ''}"
              aria-current={isActive('/admin') ? 'page' : undefined}
            >
              <i class="bi bi-speedometer2 me-1"></i>
              <span>{$t('adminPanel')}</span>
            </a>
          </li>
        {/if}
      </ul>
      
      <!-- Elementos de la derecha -->
      <div class="d-flex align-items-center gap-2">
        <!-- Selector de tema -->
        <button 
          class="btn btn-sm {$theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
          on:click={handleThemeToggle}
          aria-label="Toggle theme"
        >
          <i class="bi bi-{$theme === 'dark' ? 'sun' : 'moon'}"></i>
        </button>
        
        <!-- Selector de idioma con dropdown -->
        <div class="dropdown">
          <button 
            id="language-selector-button"
            class="btn btn-sm {$theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
            type="button"
            data-bs-toggle="dropdown" 
            aria-expanded="false"
          >
            <i class="bi bi-globe me-1"></i>
            <span class="d-none d-lg-inline">{user?.name?.toUpperCase() || ''}</span>
          </button>
          
          <ul 
            id="language-selector-dropdown"
            class="dropdown-menu dropdown-menu-end"
            aria-labelledby="language-selector-button"
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
          <!-- Spinner durante carga -->
          <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        {:else if isAuthenticated}
          <!-- Menú de usuario -->
          <div class="dropdown">
            <button 
              id="user-menu-button"
              class="btn btn-sm {$theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
              type="button"
              data-bs-toggle="dropdown" 
              aria-expanded="false"
            >
              <span class="d-none d-lg-inline me-1">{userName}</span>
              <i class="bi bi-person-circle"></i>
            </button>
            
            <ul 
              id="user-menu-dropdown"
              class="dropdown-menu dropdown-menu-end"
              aria-labelledby="user-menu-button"
            >
              <li><h6 class="dropdown-header">{userName}</h6></li>
              
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
          <a href="/login" class="btn btn-sm btn-primary">
            <i class="bi bi-box-arrow-in-right me-1 d-lg-none"></i>
            <span>{$t('login')}</span>
          </a>
          <a href="/register" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-block">
            <span>{$t('register')}</span>
          </a>
        {/if}
      </div>
    </div>
  </div>
</nav>

<!-- Modal de confirmación para acceso al panel de administrador -->
{#if showAdminConfirmDialog}
  <div class="modal fade show" id="adminConfirmModal" tabindex="-1" aria-modal="true" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-shield-lock me-2"></i>
            Acceso a Panel de Administrador
          </h5>
          <button type="button" class="btn-close" on:click={cancelAdminAccess} aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Está a punto de acceder al panel de administración. Esta área está restringida a personal autorizado.
          </div>
          <p>¿Desea continuar?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" on:click={cancelAdminAccess}>
            <i class="bi bi-x-circle me-1"></i>
            Cancelar
          </button>
          <button type="button" class="btn btn-primary" on:click={confirmAdminAccess}>
            <i class="bi bi-check-circle me-1"></i>
            Continuar
          </button>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade show"></div>
  </div>
{/if} 