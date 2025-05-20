<script>
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';
  import { t } from '$lib/i18n';
  import { writable } from 'svelte/store';
  
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
    { url: '/cinemas', icon: 'building', text: 'Cines' },
    { url: '/movies', icon: 'film', text: 'Películas' }
  ];
  
  // Agregar elementos para la barra de navegación móvil
  const mobileNavItems = [
    { url: '/', icon: 'house', text: 'Inicio' },
    { url: '/cinemas', icon: 'building', text: 'Cines' },
    { url: '/movies', icon: 'film', text: 'Películas' },
    { url: '/search', icon: 'search', text: 'Buscar' },
    { url: isAuthenticated ? '/profile' : '/login', icon: isAuthenticated ? 'person-circle' : 'box-arrow-in-right', text: isAuthenticated ? 'Perfil' : 'Login' }
  ];
  
  // Definir elementos del menú de usuario
  const userMenu = [
    { url: '/profile', icon: 'person', text: 'Mi Perfil' },
    { url: '/bookings', icon: 'ticket', text: 'Mis Reservas' },
    { divider: true, url: '', icon: '', text: '' },
    { url: '#logout', icon: 'box-arrow-right', text: 'Cerrar Sesión', action: 'logout' }
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
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    currentTheme = newTheme;
    document.documentElement.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
  }
  
  // Función para cambiar el idioma manualmente
  function changeLanguage(lang) {
    if (availableLanguages.includes(lang)) {
      currentLang = lang;
      localStorage.setItem('language', lang);
      // Recargar la página para aplicar el nuevo idioma
      window.location.reload();
    }
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
    // Inicializar tema desde localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
      currentTheme = savedTheme;
      document.documentElement.setAttribute('data-bs-theme', savedTheme);
    } else {
      // Detectar preferencia del sistema
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      currentTheme = prefersDark ? 'dark' : 'light';
      document.documentElement.setAttribute('data-bs-theme', currentTheme);
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
          if (result && result.timeout) {
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
  
  async function handleLogout(event) {
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

  function isActive(path) {
    if (path === '/') return $page.url.pathname === '/';
    return $page.url.pathname.startsWith(path);
  }
  
  function handleNavItemClick(item, event) {
    if (item.action === 'logout') {
      handleLogout(event);
    }
  }
</script>

<!-- Navbar con Bootstrap estándar para desktop -->
<nav class="navbar navbar-expand-lg fixed-top {scrolled ? 'shadow-sm' : ''} {isMobile ? 'd-none d-md-block' : ''}" data-bs-theme={currentTheme}>
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
              <span>Panel Admin</span>
            </a>
          </li>
        {/if}
      </ul>
      
      <!-- Elementos de la derecha -->
      <div class="d-flex align-items-center gap-2">
        <!-- Selector de tema -->
        <button 
          class="btn btn-sm {currentTheme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
          on:click={handleThemeToggle}
          aria-label="Toggle theme"
        >
          <i class="bi bi-{currentTheme === 'dark' ? 'sun' : 'moon'}"></i>
        </button>
        
        <!-- Selector de idioma con dropdown -->
        <div class="dropdown">
          <button 
            id="language-selector-button"
            class="btn btn-sm {currentTheme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
            type="button"
            data-bs-toggle="dropdown" 
            aria-expanded="false"
          >
            <i class="bi bi-globe me-1"></i>
            <span class="d-none d-lg-inline">{currentLang.toUpperCase()}</span>
          </button>
          
          <ul 
            id="language-selector-dropdown"
            class="dropdown-menu dropdown-menu-end"
            aria-labelledby="language-selector-button"
          >
            <li>
              <button 
                class="dropdown-item {currentLang === 'es' ? 'active' : ''}"
                on:click={() => changeLanguage('es')}
              >
                <span class="me-2">🇪🇸</span>Español
              </button>
            </li>
            <li>
              <button 
                class="dropdown-item {currentLang === 'en' ? 'active' : ''}"
                on:click={() => changeLanguage('en')}
              >
                <span class="me-2">🇬🇧</span>English
              </button>
            </li>
          </ul>
        </div>
        
        {#if loading}
          <!-- Spinner durante carga (ahora casi nunca se mostrará) -->
          <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        {:else if isAuthenticated}
          <!-- Menú de usuario -->
          <div class="dropdown">
            <button 
              id="user-menu-button"
              class="btn btn-sm {currentTheme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
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
            <span>Login</span>
          </a>
          <a href="/register" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-block">
            <span>Registro</span>
          </a>
        {/if}
      </div>
    </div>
  </div>
</nav>

<!-- Navbar estilo Instagram para móviles -->
{#if isMobile}
  <div class="mobile-header fixed-top d-md-none" data-bs-theme={currentTheme}>
    <div class="container d-flex justify-content-between align-items-center py-2">
      <a href="/" class="navbar-brand m-0">
        Kaizen
        <span class="badge bg-primary ms-1">Cinema</span>
      </a>
      <div class="d-flex gap-2">
        <!-- Selector de tema -->
        <button 
          class="btn btn-sm {currentTheme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
          on:click={handleThemeToggle}
          aria-label="Toggle theme"
        >
          <i class="bi bi-{currentTheme === 'dark' ? 'sun' : 'moon'}"></i>
        </button>
        
        <!-- Selector de idioma -->
        <div class="dropdown">
          <button 
            id="language-selector-button"
            class="btn btn-sm {currentTheme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
            type="button"
            data-bs-toggle="dropdown" 
            aria-expanded="false"
          >
            <i class="bi bi-globe"></i>
          </button>
          
          <ul 
            id="language-selector-dropdown"
            class="dropdown-menu dropdown-menu-end"
            aria-labelledby="language-selector-button"
          >
            <li>
              <button 
                class="dropdown-item {currentLang === 'es' ? 'active' : ''}"
                on:click={() => changeLanguage('es')}
              >
                <span class="me-2">🇪🇸</span>Español
              </button>
            </li>
            <li>
              <button 
                class="dropdown-item {currentLang === 'en' ? 'active' : ''}"
                on:click={() => changeLanguage('en')}
              >
                <span class="me-2">🇬🇧</span>English
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Barra de navegación inferior estilo Instagram -->
  <nav class="mobile-nav fixed-bottom d-md-none" data-bs-theme={currentTheme}>
    <div class="container">
      <div class="row g-0">
        {#each mobileNavItems as item}
          <div class="col text-center">
            <a 
              href={item.url} 
              class="nav-link py-3 {isActive(item.url) ? 'active' : ''}"
              aria-current={isActive(item.url) ? 'page' : undefined}
            >
              <i class="bi bi-{item.icon} d-block fs-5"></i>
              <small class="d-block mt-1">{item.text}</small>
            </a>
          </div>
        {/each}
        
        {#if isAdmin}
          <div class="col text-center">
            <a 
              href="/admin" 
              on:click={handleAdminClick}
              class="nav-link py-3 {isActive('/admin') ? 'active' : ''}"
              aria-current={isActive('/admin') ? 'page' : undefined}
            >
              <i class="bi bi-speedometer2 d-block fs-5"></i>
              <small class="d-block mt-1">Admin</small>
            </a>
          </div>
        {/if}
      </div>
    </div>
  </nav>
  
  <!-- Espaciador para evitar que el contenido quede debajo de la barra inferior -->
  <div class="mobile-nav-spacer d-md-none"></div>
{/if}

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