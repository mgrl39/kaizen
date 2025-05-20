<script lang="ts">
  import { page } from '$app/stores';
  import Navbar from '$lib/components/Navbar.svelte';
  import Footer from '$lib/components/Footer.svelte';
  
  // Importar Bootstrap 5 y Bootstrap Icons
  import 'bootstrap/dist/css/bootstrap.min.css';
  import 'bootstrap-icons/font/bootstrap-icons.css';
  import '$lib/styles/index.css'; // Estilos personalizados
  
  // Importar JavaScript de Bootstrap (opcional, solo si necesitas componentes interactivos)
  import { onMount } from 'svelte';
  import { initTheme, theme } from '$lib/theme';
  import '$lib/styles/custom-bootstrap.css';
  
  // Crear un archivo CSS personalizado si no existe
  let customCssCreated = false;
  
  onMount(() => {
    // Inicializar tema
    initTheme();
    
    // Importar dinámicamente Bootstrap JS
    import('bootstrap/dist/js/bootstrap.bundle.min.js');
    
    // Inicializar tooltips de Bootstrap
    if (typeof bootstrap !== 'undefined') {
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }
    
    // Aplicar el tema al elemento HTML
    const updateThemeAttribute = () => {
      document.documentElement.setAttribute('data-bs-theme', $theme);
    };
    
    // Aplicar tema inicial
    updateThemeAttribute();
    
    // Suscribirse a cambios en el tema
    const unsubscribe = theme.subscribe(value => {
      updateThemeAttribute();
    });
    
    return () => {
      unsubscribe();
    };
  });
  
  // List of routes where navbar should be hidden
  const routesWithoutNavbar = [];
  
  // List of routes where footer should be hidden
  const routesWithoutFooter = ['/login', '/register'];
  
  // Check if current route should have navbar/footer
  $: showNavbar = !routesWithoutNavbar.includes($page.url.pathname);
  $: showFooter = !routesWithoutFooter.includes($page.url.pathname) && !$page.error;
  
  // Verificar si estamos en rutas de admin
  $: isAdminRoute = $page.url.pathname.startsWith('/admin');
  
  // Verificar si estamos en rutas de login/register
  $: isAuthRoute = routesWithoutFooter.includes($page.url.pathname);
  
  // Verificar si estamos en una página de error
  $: isErrorPage = !!$page.error;
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
{:else if isAuthRoute || isErrorPage}
  <!-- Para rutas de autenticación y páginas de error, sin footer -->
  <div class="app-wrapper">
    <!-- Contenido principal con padding-top para la navbar -->
    <main class="container py-4 mt-5">
      <slot />
    </main>
  </div>
{:else}
  <!-- Layout principal con clases Bootstrap -->
  <div class="app-wrapper d-flex flex-column min-vh-100">
    <!-- Contenido principal con padding-top para la navbar -->
    <main class="container py-3 mt-5 flex-grow-1">
      <slot />
    </main>
    
    <!-- Footer -->
    {#if showFooter}
      <Footer />
    {/if}
  </div>

  <!-- Efectos de fondo con CSS personalizado -->
  <div class="decorative-blob blob-1"></div>
  <div class="decorative-blob blob-2"></div>
{/if}

<style>
  /* Variables CSS para temas */
  :global(:root) {
    --app-bg-light: #f8f9fa;
    --app-bg-dark: #121212;
    --app-text-light: #212529;
    --app-text-dark: #f8f9fa;
    --app-card-bg-light: #ffffff;
    --app-card-bg-dark: #212529;
    --app-border-light: rgba(0, 0, 0, 0.1);
    --app-border-dark: rgba(255, 255, 255, 0.1);
    --app-primary: #6d28d9;
    --app-primary-hover: #5b21b6;
  }
  
  /* Estilos para tema claro */
  :global([data-bs-theme="light"]) {
    --app-bg: var(--app-bg-light);
    --app-text: var(--app-text-light);
    --app-card-bg: var(--app-card-bg-light);
    --app-border: var(--app-border-light);
  }
  
  /* Estilos para tema oscuro */
  :global([data-bs-theme="dark"]) {
    --app-bg: var(--app-bg-dark);
    --app-text: var(--app-text-dark);
    --app-card-bg: var(--app-card-bg-dark);
    --app-border: var(--app-border-dark);
  }
  
  /* Aplicamos estilos generales */
  :global(body) {
    background-color: var(--app-bg);
    color: var(--app-text);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  /* Estilos para cards */
  :global(.card) {
    background-color: var(--app-card-bg);
    border: 1px solid var(--app-border);
  }
  
  /* Personalización de botones primarios */
  :global(.btn-primary) {
    background-color: var(--app-primary);
    border-color: var(--app-primary);
  }
  
  :global(.btn-primary:hover) {
    background-color: var(--app-primary-hover);
    border-color: var(--app-primary-hover);
  }
  
  /* Estilos para títulos de sección */
  :global(.section-title) {
    font-weight: 600;
    margin-bottom: 2rem;
    position: relative;
    display: inline-block;
  }
  
  :global(.section-title::after) {
    content: '';
    position: absolute;
    left: 0;
    bottom: -8px;
    width: 60%;
    height: 3px;
    background: linear-gradient(to right, var(--app-primary), transparent);
    border-radius: 2px;
  }
  
  /* Ocultar la navbar fija en rutas admin */
  :global(body.admin-route nav.fixed-top) {
    display: none !important;
  }
  
  /* Eliminar espacios en admin */
  :global(body.admin-route) {
    margin: 0;
    padding: 0;
    overflow: hidden;
  }
  
  /* Estilos para los blobs decorativos */
  .decorative-blob {
    position: fixed;
    z-index: -1;
    border-radius: 50%;
    filter: blur(70px);
    opacity: 0.4;
  }
  
  .blob-1 {
    top: 30%;
    left: 25%;
    width: 500px;
    height: 500px;
    background-color: rgba(109, 40, 217, 0.2);
  }
  
  .blob-2 {
    bottom: 25%;
    right: 25%;
    width: 500px;
    height: 500px;
    background-color: rgba(109, 40, 217, 0.1);
  }
</style>