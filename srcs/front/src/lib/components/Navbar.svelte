<script lang="ts">
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';
  import { t, currentLanguage, languages } from '$lib/i18n';
  import LanguageSelector from '$lib/components/LanguageSelector.svelte';
  import { writable, get } from 'svelte/store';
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

<nav class="fixed top-0 left-0 right-0 z-50 bg-dark/90 {scrolled ? 'py-2 shadow-lg' : 'py-3'} backdrop-blur-md transition-all">
  <div class="container mx-auto px-4">
    <!-- Navbar interior con mejor manejo de espacio -->
    <div class="flex items-center justify-between h-12">
      
      <!-- Logo - siempre visible -->
      <a href="/" class="text-xl font-bold text-white whitespace-nowrap logo-text">
        Kaizen
      </a>
      
      <!-- Elementos principales de navegación - visible en pantallas lg y superior -->
      <div class="hidden lg:flex items-center space-x-4 ml-4 flex-grow">
        {#each navItems as item}
          <a 
            href={item.url} 
            class="text-white hover:text-purple-300 px-3 py-2 {isActive(item.url) ? 'border-b-2 border-purple-500' : ''}"
          >
            <i class="bi bi-{item.icon} mr-2"></i>
            <span>{item.text}</span>
          </a>
        {/each}
        
        {#if isAdmin}
          <a 
            href="/admin" 
            on:click={handleAdminClick}
            class="text-white hover:text-purple-300 px-3 py-2 {isActive('/admin') ? 'border-b-2 border-purple-500' : ''}"
          >
            <i class="bi bi-speedometer2 mr-2"></i>
            <span>{$t('adminPanel')}</span>
          </a>
        {/if}
      </div>
      
      <!-- Elementos secundarios - Versión compacta con iconos en pantallas md pero no lg -->
      <div class="hidden md:flex lg:hidden items-center space-x-2">
        {#each navItems as item}
          <a 
            href={item.url} 
            class="text-white hover:text-purple-300 px-2 py-2 {isActive(item.url) ? 'border-b-2 border-purple-500' : ''}"
            aria-label={item.text}
          >
            <i class="bi bi-{item.icon}"></i>
          </a>
        {/each}
        
        {#if isAdmin}
          <a 
            href="/admin" 
            class="text-white hover:text-purple-300 px-2 py-2 {isActive('/admin') ? 'border-b-2 border-purple-500' : ''}"
            aria-label={$t('adminPanel')}
          >
            <i class="bi bi-speedometer2"></i>
          </a>
        {/if}
      </div>
      
      <!-- Acciones de usuario - ajustadas para ser más compactas en breakpoints intermedios -->
      <div class="hidden md:flex items-center ml-auto">
        <!-- Selector de idioma - versión compacta en md -->
        <div class="mr-2">
          <LanguageSelector isOpen={languageSelectorOpen} toggleMenu={toggleLanguageSelector} />
        </div>
        
        {#if loading}
          <!-- Placeholder durante carga -->
          <div class="ml-2 flex items-center gap-2">
            <div class="w-8 h-8 bg-white/10 rounded-full animate-pulse"></div>
          </div>
        {:else if isAuthenticated}
          <div class="flex items-center">
            <div class="relative ml-1">
              <button 
                id="user-menu-button"
                class="flex items-center text-white hover:text-purple-300 px-2 lg:px-3 py-2"
                aria-label={$t('userMenu')}
                on:click|stopPropagation={toggleUserMenu}
              >
                <i class="bi bi-person-circle"></i>
                <span class="hidden lg:inline ml-2">{userName}</span>
                <i class="bi bi-chevron-down text-xs ml-1"></i>
              </button>
              
              <div 
                id="user-menu-dropdown"
                class="absolute right-0 mt-1 w-48 bg-card border border-white/10 rounded-md shadow-lg {userMenuOpen ? 'block' : 'hidden'}"
              >
                {#each userMenu as item}
                  {#if item.divider}
                    <hr class="border-white/10 my-1">
                  {:else}
                    <a 
                      href={item.url} 
                      class="block px-4 py-2 text-white hover:bg-purple-900/20"
                      on:click={(e) => handleNavItemClick(item, e)}
                    >
                      <i class="bi bi-{item.icon} mr-2"></i>
                      {item.text}
                    </a>
                  {/if}
                {/each}
              </div>
            </div>
          </div>
        {:else}
          <div class="flex items-center ml-1">
            <a href="/login" class="bg-purple-800 text-white px-3 py-1.5 rounded-md hover:bg-purple-700 flex items-center">
              <i class="bi bi-box-arrow-in-right mr-0 lg:mr-2"></i>
              <span class="hidden lg:inline">{$t('login')}</span>
            </a>
            <a href="/register" class="border border-white text-white px-3 py-1.5 rounded-md ml-2 hover:bg-white/10 hidden sm:flex">
              <span>{$t('register')}</span>
            </a>
          </div>
        {/if}
      </div>
      
      <!-- Botón de menú móvil - solo visible en pantallas pequeñas -->
      <button 
        class="md:hidden p-2 text-white hover:bg-purple-700/30 rounded-md transition-colors"
        on:click={toggleMobileMenu}
        aria-label={$t('toggleMenu')}
      >
        <i class="bi bi-{mobileMenuOpen ? 'x' : 'list'} text-xl"></i>
      </button>
    </div>
    
    <!-- Menú móvil - solo visible en pantallas pequeñas cuando está abierto -->
    {#if mobileMenuOpen}
      <div class="md:hidden py-3 bg-dark/95 border-t border-purple-500/20 mt-2 backdrop-blur-lg rounded-b-lg shadow-lg">
        <div class="flex flex-col">
          {#each navItems as item}
            <a 
              href={item.url} 
              class="text-white hover:bg-purple-700/20 px-4 py-3 flex items-center transition-colors {isActive(item.url) ? 'bg-purple-700/30 border-l-4 border-purple-500' : ''}"
            >
              <i class="bi bi-{item.icon} mr-3 text-lg {isActive(item.url) ? 'text-purple-400' : 'text-gray-400'}"></i>
              <span class="font-medium">{item.text}</span>
            </a>
          {/each}
          
          {#if isAdmin}
            <a 
              href="/admin" 
              class="text-white hover:bg-purple-700/20 px-4 py-3 flex items-center transition-colors {isActive('/admin') ? 'bg-purple-700/30 border-l-4 border-purple-500' : ''}"
            >
              <i class="bi bi-speedometer2 mr-3 text-lg {isActive('/admin') ? 'text-purple-400' : 'text-gray-400'}"></i>
              <span class="font-medium">{$t('adminPanel')}</span>
            </a>
          {/if}
          
          <!-- Selector de idioma en móvil -->
          <div class="border-t border-white/10 mt-2">
            <div class="flex justify-between items-center px-4 py-3">
              <p class="text-sm font-medium text-white">Idioma</p>
              <div class="flex space-x-2">
                <button 
                  class="px-3 py-1.5 rounded-md flex items-center justify-center {$currentLanguage === 'es' ? 'bg-purple-700 text-white' : 'bg-dark/50 text-gray-400'}"
                  on:click={() => { currentLanguage.set('es'); }}
                >
                  <span class="mr-1">🇪🇸</span>
                  <span class="font-medium">ES</span>
                </button>
                <button 
                  class="px-3 py-1.5 rounded-md flex items-center justify-center {$currentLanguage === 'en' ? 'bg-purple-700 text-white' : 'bg-dark/50 text-gray-400'}"
                  on:click={() => { currentLanguage.set('en'); }}
                >
                  <span class="mr-1">🇬🇧</span>
                  <span class="font-medium">EN</span>
                </button>
              </div>
            </div>
          </div>
          
          {#if loading}
            <!-- Placeholder durante carga -->
            <div class="mt-2 pt-2 border-t border-white/10 px-4">
              <div class="h-10 bg-white/10 rounded-md animate-pulse my-2"></div>
              <div class="h-10 bg-white/10 rounded-md animate-pulse my-2"></div>
            </div>
          {:else if isAuthenticated}
            <div class="mt-2 pt-2 border-t border-white/10">
              <div class="px-4 py-3 flex items-center">
                <div class="w-8 h-8 rounded-full bg-purple-700/30 flex items-center justify-center mr-3">
                  <i class="bi bi-person-circle text-lg text-purple-300"></i>
                </div>
                <div>
                  <p class="font-medium">{userName}</p>
                  <p class="text-xs text-gray-400">Usuario autenticado</p>
                </div>
              </div>
              
              {#each userMenu as item}
                {#if !item.divider}
                  <a 
                    href={item.url} 
                    class="text-white hover:bg-purple-700/20 px-4 py-3 flex items-center transition-colors {item.action === 'logout' ? 'text-red-300 hover:bg-red-900/20' : ''}"
                    on:click={(e) => handleNavItemClick(item, e)}
                  >
                    <i class="bi bi-{item.icon} mr-3 text-lg"></i>
                    <span>{item.text}</span>
                  </a>
                {/if}
              {/each}
            </div>
          {:else}
            <div class="mt-2 pt-3 border-t border-white/10 px-4 space-y-3">
              <a href="/login" class="flex items-center justify-center bg-purple-800 text-white p-3 rounded-md hover:bg-purple-700 transition-colors">
                <i class="bi bi-box-arrow-in-right mr-2"></i>
                <span class="font-medium">{$t('login')}</span>
              </a>
              
              <a href="/register" class="flex items-center justify-center border border-purple-500/50 text-white p-3 rounded-md hover:bg-purple-700/20 transition-colors">
                <span class="font-medium">{$t('register')}</span>
              </a>
            </div>
          {/if}
        </div>
      </div>
    {/if}
  </div>
</nav>

<!-- Modal de confirmación para acceso al panel de administrador -->
{#if showAdminConfirmDialog}
  <div 
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-[1000] backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    aria-labelledby="admin-dialog-title"
  >
    <div class="bg-card border border-white/10 rounded-lg shadow-lg p-6 max-w-md mx-4">
      <h3 id="admin-dialog-title" class="text-xl font-semibold text-white mb-2">Acceso a Panel de Administrador</h3>
      <p class="text-gray-300 mb-6">Está a punto de acceder al panel de administración. ¿Desea continuar?</p>
      
      <div class="flex justify-end gap-3">
        <button 
          class="px-4 py-2 border border-white/20 text-white rounded hover:bg-white/10 transition-colors"
          on:click={cancelAdminAccess}
        >
          Cancelar
        </button>
        <button 
          class="px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-600 transition-colors"
          on:click={confirmAdminAccess}
        >
          Continuar
        </button>
      </div>
    </div>
  </div>
{/if}

<style>
  /* Mantenemos solo los selectores globales que ya están funcionando */
  :global(.navbar) {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
    transition: all 0.3s ease;
    background-color: rgba(18, 18, 18, 0.85);
    backdrop-filter: blur(10px);
  }
  
  :global(.navbar.scrolled) {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    background-color: rgba(18, 18, 18, 0.95);
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
  }
  
  :global(.logo-text) {
    font-weight: 700;
    font-size: 1.4rem;
    background: linear-gradient(to right, #fff, #a78bfa);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    transition: all 0.3s ease;
  }
</style> 