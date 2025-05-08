<script lang="ts">
  import { t } from '$lib/i18n';
  import { page } from '$app/stores';
  
  // Ítems del menú lateral con colores y nombres de ruta consistentes
  const menuItems = [
    { name: 'dashboard', path: '/admin', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'movies', path: '/admin/movies', icon: 'M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z' },
    { name: 'cinema', path: '/admin/cinemas', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { name: 'users', path: '/admin/users', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
    { name: 'bookings', path: '/admin/bookings', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
    { name: 'reports', path: '/admin/reports', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'settings', path: '/admin/settings', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' },
  ];

  // Toggle para menú móvil
  let showMobileMenu = false;
  
  // Color primario consistente
  const primaryColor = "indigo";
</script>

<div class="flex h-screen w-full overflow-hidden" style="margin-top: 0; padding-top: 0">
  <!-- Sidebar - versión escritorio -->
  <aside class="hidden md:block w-64 bg-gray-900 flex-shrink-0 h-full">
    <div class="p-4 border-b border-gray-800 flex items-center">
      <svg class="w-8 h-8 text-{primaryColor}-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
      </svg>
      <h1 class="text-xl font-bold text-white">{$t('adminPanel')}</h1>
    </div>
    
    <nav class="py-4 overflow-y-auto" style="height: calc(100% - 74px)">
      <ul class="px-2 space-y-1">
        {#each menuItems as item}
          <li>
            <a 
              href={item.path} 
              class="flex items-center px-4 py-2.5 rounded-lg {$page.url.pathname === item.path ? `bg-${primaryColor}-600 text-white` : 'text-gray-300 hover:bg-gray-800 hover:text-white'} transition-colors duration-200"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d={item.icon} />
              </svg>
              <span>{$t(item.name)}</span>
            </a>
          </li>
        {/each}
      </ul>
    </nav>
    
    <div class="absolute bottom-0 w-64 p-4 border-t border-gray-800">
      <a href="/" class="flex items-center text-gray-400 hover:text-white transition-colors duration-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>{$t('backToSite')}</span>
      </a>
    </div>
  </aside>
  
  <!-- Contenido principal -->
  <div class="flex flex-col flex-1 h-full overflow-hidden">
    <!-- Header móvil -->
    <header class="bg-white border-b border-gray-200 z-10 p-4 md:hidden flex items-center justify-between">
      <button 
        class="text-gray-600 focus:outline-none" 
        on:click={() => showMobileMenu = !showMobileMenu}
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <div class="flex items-center">
        <svg class="w-7 h-7 text-{primaryColor}-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
        </svg>
        <h1 class="text-xl font-bold text-gray-800">{$t('adminPanel')}</h1>
      </div>
      <div class="w-6"></div> <!-- Espaciador para centrar el título -->
    </header>
    
    <!-- Menú móvil -->
    {#if showMobileMenu}
      <div class="fixed inset-0 z-40 md:hidden" on:click={() => showMobileMenu = false}>
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity"></div>
        <div class="fixed top-0 left-0 bottom-0 w-64 bg-gray-900 pt-0 transform transition ease-in-out duration-300">
          <div class="p-4 border-b border-gray-800 flex items-center">
            <svg class="w-8 h-8 text-{primaryColor}-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
            </svg>
            <h1 class="text-xl font-bold text-white">{$t('adminPanel')}</h1>
          </div>
          <nav class="p-4">
            <ul class="space-y-1">
              {#each menuItems as item}
                <li>
                  <a 
                    href={item.path} 
                    class="flex items-center px-4 py-2.5 rounded-lg {$page.url.pathname === item.path ? `bg-${primaryColor}-600 text-white` : 'text-gray-300 hover:bg-gray-800 hover:text-white'} transition-colors duration-200"
                  >
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d={item.icon} />
                    </svg>
                    <span>{$t(item.name)}</span>
                  </a>
                </li>
              {/each}
            </ul>
          </nav>
          
          <div class="absolute bottom-0 w-full p-4 border-t border-gray-800">
            <a href="/" class="flex items-center text-gray-400 hover:text-white transition-colors duration-200">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              <span>{$t('backToSite')}</span>
            </a>
          </div>
        </div>
      </div>
    {/if}
    
    <!-- Contenido de la página -->
    <main class="flex-1 overflow-auto bg-gray-100">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 md:px-8">
        <slot />
      </div>
    </main>
  </div>
</div>

<style>
  /* Estilos específicos para la sección de admin */
  :global(body.admin-route) {
    margin: 0 !important;
    padding: 0 !important;
    height: 100vh;
    overflow: hidden;
    background-color: #f9fafb;
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
  }
  
  /* Asegurarse de que el admin ocupe toda la pantalla sin márgenes */
  :global(body.admin-route #svelte) {
    height: 100vh;
    margin: 0;
    padding: 0;
  }
</style>