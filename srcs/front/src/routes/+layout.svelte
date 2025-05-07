<script lang="ts">
  import Navbar from '$lib/components/Navbar.svelte';
  import Footer from '$lib/components/Footer.svelte';
  import { page } from '$app/stores';
  
  // Importar Tailwind CSS y Bootstrap Icons
  import '$lib/styles/index.css';
  import 'bootstrap-icons/font/bootstrap-icons.css';
  
  // Verificar si estamos en rutas de admin
  $: isAdminRoute = $page.url.pathname.startsWith('/admin');
</script>

{#if isAdminRoute}
  <!-- Para rutas admin, solo pasa el contenido sin estilos ni componentes adicionales -->
  <slot />
{:else}
  <!-- Layout principal con clases Tailwind -->
  <div class="app-wrapper flex flex-col min-h-screen">
    <!-- Barra de navegación -->
    <Navbar />
    
    <!-- Contenido principal -->
    <main class="container mx-auto py-3 mt-2 flex-grow">
      <slot />
    </main>
    
    <!-- Footer -->
    <Footer />
  </div>

  <div class="decorative-blob blob-1"></div>
  <div class="decorative-blob blob-2"></div>
{/if}

<style>
  /* Estos estilos SOLO se aplican fuera de /admin */
  :global(body) {
    background-color: #121212;
    color: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  :global(.card) {
    background-color: #212529;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  :global(.btn-primary) {
    background-color: #6d28d9;
    border-color: #6d28d9;
  }
  
  :global(.btn-primary:hover) {
    background-color: #5b21b6;
    border-color: #5b21b6;
  }
  
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
    background: linear-gradient(to right, #6d28d9, transparent);
    border-radius: 2px;
  }
</style> 