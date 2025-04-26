<script lang="ts">
  import Navbar from '$lib/components/Navbar.svelte';
  import Footer from '$lib/components/Footer.svelte';
  
  // Importar Bootstrap y otros estilos
  import 'bootstrap/dist/css/bootstrap.min.css';
  import 'bootstrap-icons/font/bootstrap-icons.css';
  import '$lib/styles/index.css';
  
  // Importar Bootstrap JS
  import { onMount } from 'svelte';
  
  onMount(() => {
    // Importar Bootstrap JS dinámicamente
    import('bootstrap/dist/js/bootstrap.bundle.min.js');
  });
</script>

<!-- Layout principal de la aplicación -->
<div class="app-wrapper d-flex flex-column min-vh-100">
  <!-- Barra de navegación -->
  <Navbar />
  
  <!-- Contenido principal con padding para compensar la navbar fija -->
  <main class="container py-5 mt-5 flex-grow-1">
    <slot />
  </main>
  
  <!-- Footer -->
  <Footer />
</div>

<style>
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