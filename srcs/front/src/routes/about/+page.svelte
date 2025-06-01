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
        <div class="p-4 rounded-3 bg-light h-100 border-start border-5 border-custom-primary">
          <h3 class="h4 mb-3">
            <i class="bi bi-bullseye text-custom-primary me-2"></i>
            {$t('ourMission')}
          </h3>
          <p class="text-muted mb-0">
            {$t('missionText')}
          </p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="p-4 rounded-3 bg-light h-100 border-start border-5 border-custom-primary">
          <h3 class="h4 mb-3">
            <i class="bi bi-eye text-custom-primary me-2"></i>
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
    <div class="py-5 px-4 rounded-3 bg-custom-gradient">
      <div class="py-4">
        <h2 class="h3 mb-4 text-white">{$t('readyForKaizen')}</h2>
        <a href="/movies" class="btn btn-light btn-lg hover-scale">
          <i class="bi bi-film me-2"></i>
          {$t('viewMoviesButton')}
        </a>
      </div>
    </div>
  </section>
</div>

<style>
  /* Estilos para las cards de Misión y Visión */
  .border-start.border-5 {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .border-start.border-5:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }

  /* Color primario personalizado */
  .border-custom-primary {
    border-color: #6d28d9 !important;
  }

  .text-custom-primary {
    color: #6d28d9 !important;
  }

  /* Gradiente personalizado */
  .bg-custom-gradient {
    background: linear-gradient(135deg, #6d28d9 0%, #4c1d95 100%);
    box-shadow: 0 10px 20px rgba(109, 40, 217, 0.2);
  }

  /* Efecto hover para el botón */
  .hover-scale {
    transition: transform 0.3s ease;
  }

  .hover-scale:hover {
    transform: scale(1.05);
  }
</style>