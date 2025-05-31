<!-- Definición de tipos para Tawk.to -->
<script lang="ts" context="module">
  // Definición del tipo para Tawk_API
  interface TawkAPI {
    maximize: () => void;
    hideWidget: () => void;
    toggle: () => void;
    [key: string]: any;
  }
  
  // Añadir declaración para el objeto global
  declare global {
    interface Window {
      Tawk_API?: TawkAPI;
      Tawk_LoadStart?: Date;
    }
  }
</script>

<script lang="ts">
  import { onMount, onDestroy } from 'svelte';
  import { page } from '$app/stores';
  import { browser } from '$app/environment';
  import pkg from 'svelte-countup';
  import { t } from '$lib/i18n';
  const { CountUp } = pkg;
  
  let countersVisible = false;
  let statsSection: HTMLElement;
  let animatedNumbers: { [key: string]: number } = {};
  
  // Variables para metadatos de la página
  const pageTitle = $t('about', 'Sobre Nosotros | Kaizen Cinema');
  const pageDescription = $t('aboutDescription', 'Conoce más sobre Kaizen Cinema, nuestra historia, misión y el equipo detrás de tu experiencia cinematográfica favorita.');
  
  // Datos del equipo
  const team = [
    {
      name: "mgrl39",
      role: $t('leadDeveloper', 'Desarrollador Principal'),
      image: "https://github.com/mgrl39.png",
      bio: $t('leadDeveloperBio', 'Desarrollador Full Stack y creador principal de Kaizen Cinema')
    },
    {
      name: "mgrbl",
      role: $t('collaborator', 'Desarrollador Colaborador'),
      image: "https://github.com/mgrbl.png",
      bio: $t('collaboratorBio', 'Desarrollador y colaborador clave en el proyecto Kaizen Cinema')
    }
  ];
  
  // Estadísticas de la empresa con valores numéricos
  const stats = [
    { id: 'movies', number: 50, label: $t('moviesInTheater', 'Películas en Cartelera'), suffix: "+" },
    { id: 'cinemas', number: 1, label: $t('cinemas', 'Cines') },
    { id: 'clients', number: 50000, label: $t('satisfiedClients', 'Clientes Satisfechos'), suffix: "+" },
    { id: 'support', number: 24, label: $t('support247', 'Soporte 24/7') }
  ];
  
  // Variable para guardar estado
  let tawkLoaded = false;
  let tawkScript: HTMLScriptElement | null = null;
  
  // Función para manejar el click del chat
  function handleChatClick(): void {
    if (browser && window.Tawk_API) {
      window.Tawk_API.maximize();
    }
  }
  
  // Función para limpiar Tawk completamente
  function cleanupTawk(): void {
    if (!browser) return;

    // Eliminar el script
    if (tawkScript && tawkScript.parentNode) {
      tawkScript.parentNode.removeChild(tawkScript);
    }

    // Eliminar todos los elementos de Tawk del DOM
    const tawkElements = document.querySelectorAll('[id^="tawk-"]');
    tawkElements.forEach(element => {
      if (element.parentNode) {
        element.parentNode.removeChild(element);
      }
    });

    // Limpiar las variables globales de Tawk
    if (window.Tawk_API) {
      window.Tawk_API = undefined;
    }
    if (window.Tawk_LoadStart) {
      window.Tawk_LoadStart = undefined;
    }

    // Eliminar cualquier estilo de Tawk
    const tawkStyles = document.querySelectorAll('style[id^="tawk-"]');
    tawkStyles.forEach(style => {
      if (style.parentNode) {
        style.parentNode.removeChild(style);
      }
    });

    tawkLoaded = false;
  }
  
  // Función que carga el script de Tawk de manera global una sola vez
  function loadTawk(): void {
    if (!browser || tawkLoaded) return;
    
    window.Tawk_API = window.Tawk_API || {} as TawkAPI;
    window.Tawk_LoadStart = new Date();
    
    tawkScript = document.createElement("script");
    tawkScript.async = true;
    tawkScript.src = 'https://embed.tawk.to/682d391da55be4190a7c5bab/1iroae6sn';
    tawkScript.charset = 'UTF-8';
    tawkScript.setAttribute('crossorigin', '*');
    tawkScript.id = 'tawk-script';
    document.head.appendChild(tawkScript);
    
    tawkLoaded = true;
    
    // Definir un manejador para ocultar el widget cuando navegamos fuera de /about
    const handleNavigation = () => {
      if (window.location.pathname !== '/about' && window.Tawk_API && window.Tawk_API.hideWidget) {
        try {
          window.Tawk_API.hideWidget();
          
          // Configurar un atributo CSS para ocultar todos los elementos de Tawk
          const style = document.createElement('style');
          style.id = 'tawk-hide-style';
          style.innerHTML = '[id^="tawk-"] { display: none !important; visibility: hidden !important; }';
          document.head.appendChild(style);
        } catch (e) {
          console.error('Error ocultando Tawk widget:', e);
        }
      } else if (window.location.pathname === '/about') {
        // Estamos en /about, mostrar el widget si está oculto
        const hideStyle = document.getElementById('tawk-hide-style');
        if (hideStyle && hideStyle.parentNode) {
          hideStyle.parentNode.removeChild(hideStyle);
        }
        
        if (window.Tawk_API && window.Tawk_API.toggle) {
          try {
            // Intentar mostrar el widget
            setTimeout(() => {
              window.Tawk_API?.toggle();
            }, 500);
          } catch (e) {
            console.error('Error mostrando Tawk widget:', e);
          }
        }
      }
    };
    
    // Observar cambios en la URL
    let lastUrl = window.location.href;
    
    // Comprobar inmediatamente después de cargar
    handleNavigation();
    
    // Observar cambios en la URL
    const observer = new MutationObserver(() => {
      if (lastUrl !== window.location.href) {
        lastUrl = window.location.href;
        setTimeout(handleNavigation, 100);
      }
    });
    
    observer.observe(document, { subtree: true, childList: true });
    
    // Interceptar cambios de historia
    const originalPushState = history.pushState;
    const originalReplaceState = history.replaceState;
    
    history.pushState = function(...args) {
      const result = originalPushState.apply(this, args);
      setTimeout(handleNavigation, 100);
      return result;
    };
    
    history.replaceState = function(...args) {
      const result = originalReplaceState.apply(this, args);
      setTimeout(handleNavigation, 100);
      return result;
    };
    
    // Manejar eventos popstate (navegación hacia atrás/adelante)
    window.addEventListener('popstate', () => {
      setTimeout(handleNavigation, 100);
    });
  }
  
  function formatNumber(num: number): string {
    return new Intl.NumberFormat('es-ES').format(num);
  }

  function animateValue(id: string, start: number, end: number, duration: number) {
    if (!browser) return;
    
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    function updateNumber() {
      current += increment;
      if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
        animatedNumbers[id] = end;
      } else {
        animatedNumbers[id] = Math.round(current);
        requestAnimationFrame(updateNumber);
      }
      animatedNumbers = { ...animatedNumbers }; // Trigger Svelte update
    }
    
    updateNumber();
  }

  onMount(() => {
    if (!browser) return;
    
    window.scrollTo(0, 0);
    countersVisible = true;
    
    // Inicializar los números en 0
    stats.forEach(stat => {
      animatedNumbers[stat.id] = 0;
    });
    
    // Configurar el observer para la animación
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          stats.forEach(stat => {
            animateValue(stat.id, 0, stat.number, 2000);
          });
          observer.disconnect();
        }
      });
    }, { threshold: 0.1 });
    
    if (statsSection) {
      observer.observe(statsSection);
    }
    
    // Cargar Tawk
    loadTawk();
    
    // Verificar que estamos en la página About
    if (window.location.pathname === '/about') {
      // Si ya se cargó el widget, asegurarnos de que esté visible
      const hideStyle = document.getElementById('tawk-hide-style');
      if (hideStyle && hideStyle.parentNode) {
        hideStyle.parentNode.removeChild(hideStyle);
      }
    } else {
      // Si no estamos en /about, asegurarnos de que el widget esté oculto
      if (window.Tawk_API && window.Tawk_API.hideWidget) {
        window.Tawk_API.hideWidget();
      }
    }
  });

  onDestroy(() => {
    cleanupTawk();
  });
</script>

<svelte:head>
  <title>{pageTitle}</title>
  <meta name="description" content={pageDescription} />
</svelte:head>

<div class="container pt-2">
  <!-- Hero Section -->
  <section class="text-center mb-5">
    <h1 class="display-4 fw-bold mb-4">{$t('aboutTitle')}</h1>
    <p class="lead text-muted">
      {$t('aboutSubtitle')}
    </p>
  </section>
  
  <!-- Historia y Misión -->
  <section class="row align-items-center mb-5">
    <div class="col-md-6 mb-4 mb-md-0">
      <h2 class="h3 mb-4">{$t('ourHistory')}</h2>
      <p class="text-muted">
        {$t('historyText1')}
      </p>
      <p class="text-muted">
        {$t('historyText2')}
      </p>
    </div>
    <div class="col-md-6">
      <img 
        src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
        alt={$t('ourHistory')}
        class="img-fluid rounded shadow"
      />
    </div>
  </section>
  
  <!-- Misión y Visión -->
  <section class="mb-5">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="p-4 rounded-3 bg-light h-100 border-start border-5 border-primary">
          <h3 class="h4 mb-3">
            <i class="bi bi-bullseye text-primary me-2"></i>
            {$t('ourMission')}
          </h3>
          <p class="text-muted mb-0">
            {$t('missionText')}
          </p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="p-4 rounded-3 bg-light h-100 border-start border-5 border-primary">
          <h3 class="h4 mb-3">
            <i class="bi bi-eye text-primary me-2"></i>
            {$t('ourVision')}
          </h3>
          <p class="text-muted mb-0">
            {$t('visionText')}
          </p>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Estadísticas -->
  <section class="mb-5" bind:this={statsSection}>
    <div class="row text-center">
      {#each stats as stat}
        <div class="col-6 col-md-3 mb-4">
          <div class="p-3">
            <h3 class="h2 fw-bold text-primary mb-2">
              {formatNumber(animatedNumbers[stat.id] || 0)}
              {#if stat.suffix}{stat.suffix}{/if}
              {#if stat.label === "Soporte 24/7"}<span>/7</span>{/if}
            </h3>
            <p class="text-muted mb-0">{stat.label}</p>
          </div>
        </div>
      {/each}
    </div>
  </section>
  
  <!-- Equipo -->
  <section class="mb-5">
    <h2 class="h3 text-center mb-4">Nuestro Equipo</h2>
    <div class="row justify-content-center">
      {#each team as member}
        <div class="col-md-5 mb-4">
          <div class="card h-100 border-0 shadow-sm">
            <img 
              src={member.image} 
              alt={member.name}
              class="card-img-top"
              style="height: 300px; object-fit: cover;"
            />
            <div class="card-body text-center">
              <h3 class="h5 mb-1">{member.name}</h3>
              <p class="text-primary mb-3">{member.role}</p>
              <p class="text-muted mb-0">{member.bio}</p>
              <a href="https://github.com/{member.name}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary mt-3">
                <i class="bi bi-github me-2"></i>Ver Perfil
              </a>
            </div>
          </div>
        </div>
      {/each}
    </div>
  </section>
  
  <!-- Valores -->
  <section class="mb-5">
    <h2 class="h3 text-center mb-4">Nuestros Valores</h2>
    <div class="row">
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="bi bi-heart-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">Pasión</h3>
          <p class="text-muted">Amamos el cine y todo lo que representa</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="bi bi-lightning-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">Innovación</h3>
          <p class="text-muted">Buscamos constantemente mejorar</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="bi bi-people-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">Comunidad</h3>
          <p class="text-muted">Construimos experiencias juntos</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center">
          <i class="bi bi-star-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">Excelencia</h3>
          <p class="text-muted">Nos esforzamos por la perfección</p>
        </div>
      </div>
    </div>
  </section>
  
  <!-- CTA -->
  <section class="text-center mb-5">
    <div class="py-5 px-4 rounded-3 bg-gradient" style="background: linear-gradient(45deg, var(--bs-primary) 0%, #8b5cf6 100%)">
      <div class="py-4">
        <h2 class="h3 mb-4 text-white">{$t('readyForKaizen')}</h2>
        <a href="/movies" class="btn btn-light btn-lg">
          <i class="bi bi-film me-2"></i>
          {$t('viewMoviesButton')}
        </a>
      </div>
    </div>
  </section>
</div>

<!-- Botón de Chat -->
{#if browser && window.location.pathname === '/about'}
<button 
  class="btn btn-primary rounded-circle position-fixed bottom-0 end-0 m-4 shadow-lg chat-button"
  style="width: 60px; height: 60px; z-index: 1000;"
  on:click={handleChatClick}
  aria-label="Abrir chat de soporte"
>
  <i class="bi bi-chat-dots fs-4"></i>
</button>
{/if}

<style>
  .chat-button {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .chat-button:hover {
    transform: scale(1.1);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
  }
  
  /* Estilo para ocultar elementos de Tawk cuando no estamos en /about */
  :global(.not-about [id^="tawk-"]) {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    width: 0 !important;
    position: absolute !important;
    left: -9999px !important;
  }
  
  /* Estilos para las cards de Misión y Visión */
  .border-start.border-5 {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .border-start.border-5:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }
</style>