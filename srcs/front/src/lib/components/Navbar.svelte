<script lang="ts">
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';
  import { t } from '$lib/i18n';
  import { writable } from 'svelte/store';
  import { theme } from '$lib/theme';
  
  // Stores para estado de usuario
  const authState = writable({
    isAuthenticated: false,
    isAdmin: false,
    userName: 'Usuario',
    loading: false
  });
  
  // Estado local para UI
  let mobileMenuOpen = false;
  let scrolled = false;
  let showAdminConfirmDialog = false;
  let userMenuOpen = false;
  let languageSelectorOpen = false;
  
  // Variables locales para tema e idioma
  let currentTheme = 'light';
  let currentLang = 'es';
  let isMobile = false;
  
  $: ({ isAuthenticated, isAdmin, userName, loading } = $authState);
  
  // Definir estructura de navegación
  const navItems = [
    { url: '/cinemas', icon: 'building', text: $t('cinemas') },
    { url: '/movies', icon: 'film', text: $t('movies') }
  ];
  
  // Agregar elementos para la barra de navegación móvil
  const mobileNavItems = [
    { url: '/', icon: 'house', text: $t('home') },
    { url: '/cinemas', icon: 'building', text: $t('cinemas') },
    { url: '/movies', icon: 'film', text: $t('movies') },
    { url: isAuthenticated ? '/profile' : '/login', icon: isAuthenticated ? 'person-circle' : 'box-arrow-in-right', text: isAuthenticated ? $t('profile') : $t('login') }
  ];
  
  // Definir elementos del menú de usuario
  const userMenu = [
    { url: '/profile', icon: 'person', text: $t('profile') },
    { url: '/bookings', icon: 'ticket', text: $t('bookings') },
    { divider: true, url: '', icon: '', text: '' },
    { url: '#logout', icon: 'box-arrow-right', text: $t('logout'), action: 'logout' }
  ];
  
  // Idiomas disponibles
  const availableLanguages = ['es', 'en'];
  
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
  
  // Función para cambiar el tema manualmente
  function handleThemeToggle() {
    const newTheme = $theme === 'dark' ? 'light' : 'dark';
    theme.set(newTheme);
    document.documentElement.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
  }
  
  // Función para cambiar el idioma manualmente
  function changeLanguage(lang: string) {
    if (availableLanguages.includes(lang)) {
      currentLang = lang;
      localStorage.setItem('language', lang);
      // Recargar la página para aplicar el nuevo idioma
      window.location.reload();
    }
  }
  
  function handleClickOutside(event: MouseEvent) {
    const userMenu = document.getElementById('user-menu-dropdown');
    const userButton = document.getElementById('user-menu-button');
    const langSelector = document.getElementById('language-selector-dropdown');
    const langButton = document.getElementById('language-selector-button');
    
    if (userMenu && userButton && !userMenu.contains(event.target as Node) && !userButton.contains(event.target as Node)) {
      userMenuOpen = false;
    }
    
    if (langSelector && langButton && !langSelector.contains(event.target as Node) && !langButton.contains(event.target as Node)) {
      languageSelectorOpen = false;
    }
  }
  
  onMount(() => {
    // Inicializar tema desde localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
      theme.set(savedTheme);
      document.documentElement.setAttribute('data-bs-theme', savedTheme);
    } else {
      // Detectar preferencia del sistema
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const initialTheme = prefersDark ? 'dark' : 'light';
      theme.set(initialTheme);
      document.documentElement.setAttribute('data-bs-theme', initialTheme);
    }
    
    // Inicializar idioma desde localStorage
    const savedLanguage = localStorage.getItem('language');
    if (savedLanguage && availableLanguages.includes(savedLanguage)) {
      currentLang = savedLanguage;
    }
    
    // Cargar estado guardado
    const cachedState = localStorage.getItem('authState');
    if (cachedState) {
      try {
        const parsed = JSON.parse(cachedState);
        authState.set({ ...parsed, loading: false });
      } catch (e) {
        // Ignorar errores de parsing
      }
    }
    
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
      // Intentar cargar el perfil con timeout
      const profilePromise = fetchProfile();
      
      // Establecer un timeout de 3 segundos
      const timeoutPromise = new Promise((resolve) => {
        setTimeout(() => resolve({ timeout: true }), 3000);
      });
      
      // Usar Promise.race para tomar el que termine primero
      Promise.race([profilePromise, timeoutPromise])
        .then(result => {
          if ('timeout' in (result as { timeout?: boolean }) && (result as { timeout: boolean }).timeout) {
            console.warn('Tiempo de espera agotado al cargar el perfil');
            // Mostrar como no autenticado si hay timeout
            authState.set({
              isAuthenticated: false,
              isAdmin: false,
              userName: 'Usuario',
              loading: false
            });
          }
        });
    }
    
    // Detectar si es dispositivo móvil
    const checkMobile = () => {
      isMobile = window.innerWidth < 768;
    };
    
    checkMobile();
    window.addEventListener('resize', checkMobile);
    
    return () => {
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('resize', checkMobile);
      document.removeEventListener('click', handleClickOutside);
    };
  });
  
  function handleAdminClick(event : Event) {
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
      return { success: false };
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
          userName: data.user.name || data.user.username || 'Usuario',
          loading: false
        };
        
        authState.set(newState);
        localStorage.setItem('authState', JSON.stringify(newState));
        return { success: true };
      } else {
        authState.set({
          isAuthenticated: false,
          isAdmin: false,
          userName: 'Usuario',
          loading: false
        });
        localStorage.removeItem('token');
        localStorage.removeItem('authState');
        return { success: false };
      }
    } catch (err) {
      console.error("Error al cargar perfil:", err);
      authState.set({
        isAuthenticated: false,
        isAdmin: false,
        userName: 'Usuario',
        loading: false
      });
      localStorage.removeItem('token');
      localStorage.removeItem('authState');
      return { success: false };
    }
  }
  
  async function handleLogout(event : Event | null) {
    if (event) event.preventDefault();
    
    const token = localStorage.getItem('token');
    if (token) {
      // Actualizar el estado inmediatamente para mejor UX
      authState.set({
        isAuthenticated: false,
        isAdmin: false,
        userName: 'Usuario',
        loading: false
      });
      
      localStorage.removeItem('token');
      localStorage.removeItem('authState');
      
      // Intentar hacer logout en el servidor, pero no esperar por ello
      fetch(`${API_URL}/logout`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'
        }
      }).catch(e => console.error("Error en logout:", e));
      
      goto('/login');
    }
  }

  function isActive(path: string) {
    if (path === '/') return $page.url.pathname === '/';
    return $page.url.pathname.startsWith(path);
  }
  
  function handleNavItemClick(item: { action?: string }, event: Event) {
    if (item.action === 'logout') {
      handleLogout(event);
    }
  }
</script>

<!-- Navbar con Bootstrap estándar para desktop -->
<nav class="navbar navbar-expand-lg fixed-top {scrolled ? 'shadow-sm' : ''}" data-bs-theme={$theme}>
  <div class="container">
    <!-- Logo y navegación principal agrupados -->
    <div class="d-flex align-items-center">
      <!-- Logo -->
      <a href="/" class="navbar-brand me-4">
        <span class="brand-text">Kaizen</span>
        <span class="cinema-text">Cinema</span>
      </a>
      
      <!-- Elementos de navegación principal -->
      <ul class="navbar-nav d-none d-lg-flex">
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
              <span>Panel Admin</span>
            </a>
          </li>
        {/if}
      </ul>
    </div>
    
    <!-- Elementos de la derecha -->
    <div class="d-none d-lg-flex align-items-center gap-3">
      <!-- Selector de tema -->
      <button 
        class="btn btn-sm btn-icon {$theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
        on:click={handleThemeToggle}
        aria-label="Toggle theme"
      >
        <i class="bi bi-{$theme === 'dark' ? 'sun' : 'moon'}"></i>
      </button>
      
      <!-- Selector de idioma con dropdown -->
      <div class="dropdown">
        <button 
          id="language-selector-button"
          class="btn btn-sm btn-icon {$theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
          type="button"
          data-bs-toggle="dropdown" 
          aria-expanded="false"
          aria-label={$t('selectLanguage')}
        >
          <i class="bi bi-globe2"></i>
        </button>
        
        <ul 
          id="language-selector-dropdown"
          class="dropdown-menu dropdown-menu-end py-1"
          aria-labelledby="language-selector-button"
        >
          <li>
            <button 
              class="dropdown-item d-flex align-items-center {currentLang === 'es' ? 'active' : ''}"
              on:click={() => changeLanguage('es')}
            >
              <span class="fi fi-es me-2"></span>
              <span>Español</span>
            </button>
          </li>
          <li>
            <button 
              class="dropdown-item d-flex align-items-center {currentLang === 'en' ? 'active' : ''}"
              on:click={() => changeLanguage('en')}
            >
              <span class="fi fi-gb me-2"></span>
              <span>English</span>
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
            class="btn btn-gradient" 
            type="button"
            data-bs-toggle="dropdown" 
            aria-expanded="false"
          >
            <i class="bi bi-person-circle me-1"></i>
            <span class="d-none d-lg-inline">{userName}</span>
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
        <div class="d-flex gap-2">
          <a href="/login" class="btn btn-gradient">
            <i class="bi bi-box-arrow-in-right me-1 d-lg-none"></i>
            <span>{$t('login')}</span>
          </a>
          <a href="/register" class="btn btn-outline-gradient d-none d-sm-inline-block">
            <span>{$t('register')}</span>
          </a>
        </div>
      {/if}
    </div>
  </div>
</nav>

<!-- Navbar móvil -->
{#if isMobile}
  <!-- Header móvil -->
  <div class="mobile-header fixed-top d-lg-none" data-bs-theme={$theme}>
    <div class="container-fluid px-3 d-flex justify-content-between align-items-center">
      <a href="/" class="navbar-brand py-2 m-0">
        <span class="brand-text">Kaizen</span>
        <span class="cinema-text">Cinema</span>
      </a>
      
      <div class="d-flex align-items-center gap-2">
        <button 
          class="btn btn-sm btn-icon {$theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}"
          aria-label="Toggle theme"
          on:click={handleThemeToggle}
        >
          <i class="bi bi-{$theme === 'dark' ? 'sun' : 'moon'} fs-5"></i>
        </button>
      </div>
    </div>
  </div>
  
  <!-- Navegación inferior móvil -->
  <nav class="mobile-nav fixed-bottom d-lg-none" data-bs-theme={$theme}>
    <div class="container-fluid px-0">
      <div class="mobile-nav-content">
        <a 
          href="/" 
          class="mobile-nav-item {isActive('/') ? 'active' : ''}"
        >
          <i class="bi bi-house"></i>
          <span>{$t('home')}</span>
        </a>
        
        <a 
          href="/movies" 
          class="mobile-nav-item {isActive('/movies') ? 'active' : ''}"
        >
          <i class="bi bi-film"></i>
          <span>{$t('movies')}</span>
        </a>
        
        <a 
          href="/cinemas" 
          class="mobile-nav-item {isActive('/cinemas') ? 'active' : ''}"
        >
          <i class="bi bi-building"></i>
          <span>{$t('cinemas')}</span>
        </a>
        
        <a 
          href={isAuthenticated ? '/profile' : '/login'} 
          class="mobile-nav-item {isActive(isAuthenticated ? '/profile' : '/login') ? 'active' : ''}"
        >
          <i class="bi bi-{isAuthenticated ? 'person-circle' : 'box-arrow-in-right'}"></i>
          <span>{isAuthenticated ? $t('profile') : $t('login')}</span>
        </a>
      </div>
    </div>
  </nav>
  
  <!-- Espaciador para el contenido -->
  <div class="mobile-spacer d-lg-none"></div>
{/if}

<style>
  /* Variables de colores personalizadas */
  :root {
    /* Colores principales */
    --primary-color: #6366f1;
    --primary-hover: #4f46e5;
    --primary-light: #818cf8;
    --secondary-color: #a855f7;
    --secondary-hover: #9333ea;
    
    /* Dimensiones */
    --navbar-height: 64px;
    --mobile-nav-height: 56px;
    --mobile-bottom-nav-height: 48px;
    
    /* Transiciones */
    --transition-base: all 0.2s ease;
    --transform-hover: translateY(-1px);
  }

  /* Estilos base de la navbar */
  .navbar {
    height: var(--navbar-height);
    background-color: var(--bs-body-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--bs-border-color);
    transition: var(--transition-base);
  }

  /* Logo y marca */
  .navbar-brand {
    font-weight: 700;
    letter-spacing: -0.5px;
    display: flex;
    align-items: baseline;
    gap: 4px;
  }

  .brand-text {
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .cinema-text {
    font-weight: 300;
    font-size: 0.95em;
    letter-spacing: 0.5px;
    color: var(--bs-body-color);
    opacity: 0.9;
    position: relative;
    padding-left: 0.5em;
  }

  .cinema-text::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    height: 0.8em;
    width: 1px;
    background: currentColor;
    transform: translateY(-50%);
    opacity: 0.3;
  }

  /* Enlaces de navegación */
  .nav-link {
    position: relative;
    transition: var(--transition-base);
    padding: 0.5rem 1rem;
    color: var(--bs-body-color);
    opacity: 0.7;
  }

  .nav-link:hover {
    opacity: 1;
    color: var(--primary-color);
  }

  .nav-link.active {
    opacity: 1;
    color: var(--primary-color);
    font-weight: 600;
  }

  .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 1rem;
    right: 1rem;
    height: 2px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 2px;
  }

  /* Botones y controles */
  .btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 1.1rem;
    transition: var(--transition-base);
  }

  .btn-icon:hover {
    transform: var(--transform-hover);
  }

  .btn-gradient {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    color: white;
    padding: 0.375rem 1rem;
    transition: var(--transition-base);
  }

  .btn-gradient:hover {
    background: linear-gradient(135deg, var(--primary-hover), var(--secondary-hover));
    transform: var(--transform-hover);
  }

  .btn-outline-gradient {
    background: transparent;
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
    padding: 0.375rem 1rem;
    position: relative;
    transition: var(--transition-base);
    z-index: 1;
  }

  .btn-outline-gradient::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: inherit;
    opacity: 0;
    z-index: -1;
    transition: var(--transition-base);
  }

  .btn-outline-gradient:hover {
    color: white;
    border-color: transparent;
  }

  .btn-outline-gradient:hover::before {
    opacity: 1;
  }

  /* Dropdowns */
  .dropdown-menu {
    background-color: var(--bs-body-bg);
    border: 1px solid var(--bs-border-color);
    border-radius: 0.75rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: var(--transition-base);
  }

  .dropdown-item {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: var(--transition-base);
    color: var(--bs-body-color);
  }

  .dropdown-item:hover {
    background-color: var(--bs-body-bg);
    color: var(--primary-color);
    transform: translateX(5px);
  }

  .dropdown-item.active {
    background-color: var(--primary-color);
    color: white;
    transform: none;
  }

  .dropdown-item.active:hover {
    background-color: var(--primary-hover);
  }

  .dropdown-divider {
    border-top: 1px solid var(--bs-border-color);
  }

  .dropdown-header {
    color: var(--bs-body-color);
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-bottom: 1px solid var(--bs-border-color);
  }

  /* Estilos móviles */
  .mobile-header {
    height: var(--mobile-nav-height);
    background-color: var(--bs-body-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--bs-border-color);
    transition: var(--transition-base);
  }

  .mobile-nav {
    height: var(--mobile-bottom-nav-height);
    background-color: var(--bs-body-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-top: 1px solid var(--bs-border-color);
    transition: var(--transition-base);
  }

  .mobile-nav-content {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    height: 100%;
  }

  .mobile-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--bs-body-color);
    text-decoration: none;
    gap: 2px;
    position: relative;
    transition: var(--transition-base);
    opacity: 0.7;
  }

  .mobile-nav-item i {
    font-size: 1.1rem;
  }

  .mobile-nav-item span {
    font-size: 0.7rem;
    font-weight: 500;
  }

  .mobile-nav-item.active {
    opacity: 1;
    color: var(--primary-color);
  }

  .mobile-nav-item.active::after {
    content: '';
    position: absolute;
    top: 0;
    left: 25%;
    right: 25%;
    height: 2px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 2px;
  }

  /* Ajustes para tema oscuro */
  :global([data-bs-theme="dark"]) {
    .navbar,
    .mobile-header,
    .mobile-nav {
      background-color: rgba(17, 24, 39, 0.8);
      border-color: rgba(255, 255, 255, 0.1);
    }

    .dropdown-menu {
      background-color: rgba(17, 24, 39, 0.95);
      border-color: rgba(255, 255, 255, 0.1);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 
                  0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .dropdown-item {
      color: rgba(255, 255, 255, 0.8);
    }

    .dropdown-item:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
    }

    .btn-outline-gradient {
      border-color: rgba(99, 102, 241, 0.5);
      color: rgba(255, 255, 255, 0.9);
    }

    .btn-outline-gradient:hover {
      border-color: transparent;
      color: white;
    }

    .btn-outline-dark {
      --bs-btn-color: rgba(255, 255, 255, 0.8);
      --bs-btn-border-color: rgba(255, 255, 255, 0.2);
      --bs-btn-hover-color: white;
      --bs-btn-hover-border-color: rgba(255, 255, 255, 0.3);
      --bs-btn-hover-bg: rgba(255, 255, 255, 0.1);
    }
  }

  /* Espaciador para contenido móvil */
  .mobile-spacer {
    height: 0px;
  }

  /* Spinner */
  .spinner-border {
    width: 1.25rem;
    height: 1.25rem;
    border-color: var(--primary-color) transparent transparent;
  }

  .spinner-border.text-primary {
    border-color: var(--primary-color) transparent transparent;
  }
</style>

<!-- Modal de confirmación para acceso al panel de administrador -->
{#if showAdminConfirmDialog}
  <div class="modal fade show" id="adminConfirmModal" tabindex="-1" aria-modal="true" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-shield-lock me-2"></i>
            {$t('adminAccessTitle')}
          </h5>
          <button type="button" class="btn-close" on:click={cancelAdminAccess} aria-label={$t('close')}></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {$t('adminAccessWarning')}
          </div>
          <p>{$t('continueQuestion')}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" on:click={cancelAdminAccess}>
            <i class="bi bi-x-circle me-1"></i>
            {$t('cancel')}
          </button>
          <button type="button" class="btn btn-primary" on:click={confirmAdminAccess}>
            <i class="bi bi-check-circle me-1"></i>
            {$t('continue')}
          </button>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade show"></div>
  </div>
{/if} 