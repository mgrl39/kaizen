<script lang="ts">
  import { page } from '$app/stores';
  import Navbar from '$lib/components/Navbar.svelte';
  import Footer from '$lib/components/Footer.svelte';
  
  // Importar Tailwind CSS y Bootstrap Icons
  import '$lib/styles/index.css';
  import 'bootstrap-icons/font/bootstrap-icons.css';
  
  // List of routes where navbar should be hidden
  const routesWithoutNavbar = [];
  
  // List of routes where footer should be hidden
  const routesWithoutFooter = ['/login', '/register'];
  
  // Check if current route should have navbar/footer
  $: showNavbar = !routesWithoutNavbar.includes($page.url.pathname);
  $: showFooter = !routesWithoutFooter.includes($page.url.pathname);
  
  // Verificar si estamos en rutas de admin
  $: isAdminRoute = $page.url.pathname.startsWith('/admin');
  
  // Verificar si estamos en rutas de login/register
  $: isAuthRoute = routesWithoutFooter.includes($page.url.pathname);
</script>

<!-- Añadimos la etiqueta para gestionar clases del body -->
<svelte:head>
  {#if isAdminRoute}
    <script>
      document.body.classList.add('admin-route');
    </script>
  {:else}
    <script>
      document.body.classList.remove('admin-route');
    </script>
  {/if}
</svelte:head>

{#if showNavbar}
  <Navbar />
{/if}

{#if isAdminRoute}
  <!-- Para rutas admin, SOLO se pasa el contenido sin nada más -->
  <slot />
{:else if isAuthRoute}
  <!-- Para rutas de autenticación, sin footer -->
  <div class="app-wrapper flex flex-col min-h-screen">
    <!-- Contenido principal -->
    <main class="container mx-auto py-3 mt-2 flex-grow">
      <slot />
    </main>
  </div>
{:else}
  <!-- Layout principal con clases Tailwind -->
  <div class="app-wrapper flex flex-col min-h-screen">
    <!-- Contenido principal -->
    <main class="container mx-auto py-3 mt-2 flex-grow">
      <slot />
    </main>
    
    <!-- Footer -->
    {#if showFooter}
      <Footer />
    {/if}
  </div>

  <div class="decorative-blob blob-1"></div>
  <div class="decorative-blob blob-2"></div>
{/if}

<style>
  /* Aplicamos estilos solo a body que NO sea admin-route */
  :global(body:not(.admin-route)) {
    background-color: #121212;
    color: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  :global(body:not(.admin-route) .card) {
    background-color: #212529;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  :global(body:not(.admin-route) .btn-primary) {
    background-color: #6d28d9;
    border-color: #6d28d9;
  }
  
  :global(body:not(.admin-route) .btn-primary:hover) {
    background-color: #5b21b6;
    border-color: #5b21b6;
  }
  
  :global(body:not(.admin-route) .section-title) {
    font-weight: 600;
    margin-bottom: 2rem;
    position: relative;
    display: inline-block;
  }
  
  :global(body:not(.admin-route) .section-title::after) {
    content: '';
    position: absolute;
    left: 0;
    bottom: -8px;
    width: 60%;
    height: 3px;
    background: linear-gradient(to right, #6d28d9, transparent);
    border-radius: 2px;
  }
  
  /* Muy importante: ocultar la navbar fija en rutas admin */
  :global(body.admin-route nav.fixed) {
    display: none !important;
  }
  
  /* Eliminar espacios en admin */
  :global(body.admin-route) {
    margin: 0;
    padding: 0;
    overflow: hidden;
  }
</style>