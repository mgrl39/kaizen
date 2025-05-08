<script lang="ts">
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';
  import { t } from '$lib/i18n';
  import LanguageSelector from '$lib/components/LanguageSelector.svelte';
  import { writable } from 'svelte/store';
  
  // Stores para estado de usuario
  const authState = writable({
    isAuthenticated: false,
    isAdmin: false,
    userName: 'Usuario',
    notificationCount: 0,
    loading: true
  });
  
  // Estado local para UI
  let mobileMenuOpen = false;
  let scrolled = false;
  let showAdminConfirmDialog = false;
  
  $: ({ isAuthenticated, isAdmin, userName, notificationCount, loading } = $authState);
  
  // Definir estructura de navegación
  const navItems = [
    { 
      path: '/cinemas', 
      icon: 'building', 
      label: 'cinemas'
    },
    { 
      path: '/movies', 
      icon: 'film', 
      label: 'movies'
    }
  ];
  
  // Definir elementos del menú de usuario
  const userMenuItems = [
    { 
      path: '/profile', 
      icon: 'person', 
      label: 'profile'
    },
    { 
      path: '/bookings', 
      icon: 'ticket', 
      label: 'bookings'
    }
  ];
  
  onMount(() => {
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
    
    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  });
  
  function toggleMobileMenu() {
    mobileMenuOpen = !mobileMenuOpen;
  }
  
  // Función para manejar el clic en el enlace de administrador
  function handleAdminClick(event) {
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
        notificationCount: 0,
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
          notificationCount: data.notifications?.unread || 0,
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
          notificationCount: 0,
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
        notificationCount: 0,
        loading: false
      });
      localStorage.removeItem('token');
      localStorage.removeItem('authState');
    }
  }
  
  async function handleLogout() {
    const token = localStorage.getItem('token');
    if (token) {
      // Actualizar UI inmediatamente para mejorar percepción de velocidad
      authState.set({
        isAuthenticated: false,
        isAdmin: false,
        userName: 'Usuario',
        notificationCount: 0,
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
</script>

<nav class="fixed top-0 left-0 right-0 z-50 bg-dark/90 {scrolled ? 'py-2 shadow-lg' : 'py-3'} backdrop-blur-md transition-all">
  <div class="container mx-auto px-4">
    <!-- Navbar interior con mejor manejo de espacio -->
    <div class="flex items-center justify-between h-12">
      
      <!-- Logo - siempre visible -->
      <a href="/" class="text-xl font-bold text-white whitespace-nowrap">
        Kaizen
      </a>
      
      <!-- Elementos principales de navegación - visible en pantallas lg y superior -->
      <div class="hidden lg:flex items-center space-x-4 ml-4 flex-grow">
        {#each navItems as item}
          <a 
            href={item.path} 
            class="text-white hover:text-purple-300 px-3 py-2 {isActive(item.path) ? 'border-b-2 border-purple-500' : ''}"
          >
            <i class="bi bi-{item.icon} mr-2"></i>
            <span>{$t(item.label)}</span>
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
            href={item.path} 
            class="text-white hover:text-purple-300 px-2 py-2 {isActive(item.path) ? 'border-b-2 border-purple-500' : ''}"
            aria-label={$t(item.label)}
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
          <LanguageSelector />
        </div>
        
        {#if loading}
          <!-- Placeholder durante carga -->
          <div class="ml-2 flex items-center gap-2">
            <div class="w-8 h-8 bg-white/10 rounded-full animate-pulse"></div>
          </div>
        {:else if isAuthenticated}
          <div class="flex items-center">
            <!-- Sólo iconos en md, texto + iconos en lg -->
            <a 
              href="/notifications" 
              class="relative px-2 lg:px-3 py-2 text-white hover:text-purple-300 hover:bg-white/5 rounded-md"
              aria-label={notificationCount > 0 ? `${$t('notifications')} (${notificationCount})` : $t('notifications')}
            >
              <i class="bi bi-bell"></i>
              <span class="hidden lg:inline ml-2">Notificaciones</span>
              {#if notificationCount > 0}
                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">
                  {notificationCount}
                </span>
              {/if}
            </a>
            
            <div class="relative group ml-1">
              <button class="flex items-center text-white hover:text-purple-300 px-2 lg:px-3 py-2"
                aria-label={$t('userMenu')}>
                <i class="bi bi-person-circle"></i>
                <span class="hidden lg:inline ml-2">{userName}</span>
                <i class="bi bi-chevron-down text-xs ml-1"></i>
              </button>
              
              <div class="absolute right-0 mt-1 w-48 bg-card border border-white/10 rounded-md shadow-lg hidden group-hover:block">
                {#each userMenuItems as item}
                  <a href={item.path} class="block px-4 py-2 text-white hover:bg-purple-900/20">
                    <i class="bi bi-{item.icon} mr-2"></i>
                    {$t(item.label)}
                  </a>
                {/each}
                
                <hr class="border-white/10 my-1">
                <button 
                  class="w-full text-left px-4 py-2 text-white hover:bg-purple-900/20"
                  on:click={handleLogout}
                  aria-label={$t('logout')}
                >
                  <i class="bi bi-box-arrow-right mr-2"></i>
                  {$t('logout')}
                </button>
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
        class="md:hidden p-2 text-white hover:bg-white/10 rounded-md"
        on:click={toggleMobileMenu}
        aria-label={$t('toggleMenu')}
      >
        <i class="bi bi-{mobileMenuOpen ? 'x' : 'list'} text-xl"></i>
      </button>
    </div>
    
    <!-- Menú móvil - solo visible en pantallas pequeñas cuando está abierto -->
    {#if mobileMenuOpen}
      <div class="md:hidden py-3 bg-dark border-t border-white/10 mt-2">
        <div class="flex flex-col space-y-2">
          {#each navItems as item}
            <a 
              href={item.path} 
              class="text-white hover:bg-white/10 px-3 py-3 rounded-md flex items-center {isActive(item.path) ? 'bg-white/10' : ''}"
            >
              <i class="bi bi-{item.icon} mr-3 text-lg"></i>
              <span>{$t(item.label)}</span>
            </a>
          {/each}
          
          {#if isAdmin}
            <a 
              href="/admin" 
              class="text-white hover:bg-white/10 px-3 py-3 rounded-md flex items-center {isActive('/admin') ? 'bg-white/10' : ''}"
            >
              <i class="bi bi-speedometer2 mr-3 text-lg"></i>
              <span>{$t('adminPanel')}</span>
            </a>
          {/if}
          
          <!-- Selector de idioma en móvil -->
          <div class="px-3 py-2">
            <LanguageSelector />
          </div>
          
          {#if loading}
            <!-- Placeholder durante carga -->
            <div class="mt-2 pt-2 border-t border-white/10 px-3">
              <div class="h-10 bg-white/10 rounded animate-pulse my-2"></div>
              <div class="h-10 bg-white/10 rounded animate-pulse my-2"></div>
            </div>
          {:else if isAuthenticated}
            <div class="mt-2 pt-2 border-t border-white/10">
              <div class="px-3 py-2 flex items-center">
                <i class="bi bi-person-circle mr-2 text-lg"></i>
                <span class="font-medium">{userName}</span>
              </div>
              
              {#each userMenuItems as item}
                <a href={item.path} class="text-white hover:bg-white/10 px-3 py-3 rounded-md flex items-center">
                  <i class="bi bi-{item.icon} mr-3 text-lg"></i>
                  <span>{$t(item.label)}</span>
                </a>
              {/each}
              
              <a href="/notifications" class="text-white hover:bg-white/10 px-3 py-3 rounded-md flex items-center">
                <i class="bi bi-bell mr-3 text-lg"></i>
                <span>Notificaciones</span>
                {#if notificationCount > 0}
                  <span class="ml-2 bg-red-600 text-white text-xs px-2 py-1 rounded-full">
                    {notificationCount}
                  </span>
                {/if}
              </a>
              
              <button 
                class="w-full text-left px-3 py-3 text-red-300 hover:bg-red-900/20 rounded-md flex items-center mt-2"
                on:click={handleLogout}
                aria-label={$t('logout')}
              >
                <i class="bi bi-box-arrow-right mr-3 text-lg"></i>
                <span>{$t('logout')}</span>
              </button>
            </div>
          {:else}
            <div class="mt-2 pt-2 border-t border-white/10 px-3 space-y-2">
              <a href="/login" class="flex items-center justify-center bg-purple-800 text-white p-3 rounded-md">
                <i class="bi bi-box-arrow-in-right mr-2"></i>
                <span>{$t('login')}</span>
              </a>
              
              <a href="/register" class="flex items-center justify-center border border-white text-white p-3 rounded-md hover:bg-white/10">
                <span>{$t('register')}</span>
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
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-[1000] backdrop-blur-sm">
    <div class="bg-card border border-white/10 rounded-lg shadow-lg p-6 max-w-md mx-4">
      <h3 class="text-xl font-semibold text-white mb-2">Acceso a Panel de Administrador</h3>
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
    -webkit-text-fill-color: transparent;
    transition: all 0.3s ease;
  }
  
  /* Eliminamos todos los selectores no utilizados */
  /* Si necesitas estos estilos en el futuro, puedes convertirlos a :global() o añadirlos de nuevo */
</style> 