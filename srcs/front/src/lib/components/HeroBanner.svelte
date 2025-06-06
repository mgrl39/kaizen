<script lang="ts">
  import { t } from '$lib/i18n';
  
  // Props para personalizar el banner
  export let title: string;
  export let subtitle: string;
  export let imageUrl: string;
  export let buttonText = "Ver películas";
  export let buttonUrl = "/movies";
  export let overlayOpacity: string = "50";

  // Imagen por defecto en base64 (un placeholder gris simple)
  const DEFAULT_IMAGE_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
</script>

<div class="hero-banner position-relative">
  <div class="position-absolute inset-0">
    <img 
      src={imageUrl || DEFAULT_IMAGE_BASE64} 
      alt="" 
      class="w-100 h-100 object-cover"
      on:error={(e) => {
        if (e.target) {
          (e.target as HTMLImageElement).src = DEFAULT_IMAGE_BASE64;
          (e.target as HTMLImageElement).onerror = null;
        }
      }}
    />
    <div 
      class="position-absolute inset-0" 
      style="background-color: rgba(0,0,0,{overlayOpacity}%)"
    ></div>
  </div>
  
  <div class="container position-relative py-3 text-white">
    <div class="row min-vh-25 align-items-center justify-content-center text-center">
      <div class="col-lg-8">
        <h1 class="display-5 fw-bold mb-2 mobile-title">{title}</h1>
        {#if subtitle}
          <p class="lead mb-3 mobile-subtitle">{subtitle}</p>
        {/if}
        <a href={buttonUrl} class="btn btn-primary btn-lg">
          {buttonText}
          <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<style>
  .hero-banner {
    background-color: var(--bs-dark);
  }
  
  .min-vh-25 {
    min-height: 12vh;
  }
  
  .inset-0 {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
  }
  
  .object-cover {
    object-fit: cover;
    -webkit-mask-image: linear-gradient(to bottom, black 80%, transparent 100%);
    mask-image: linear-gradient(to bottom, black 80%, transparent 100%);
  }
  
  .text-shadow-lg {
    text-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
  }
  
  /* Estilos responsivos para móviles */
  @media (max-width: 768px) {
    .mobile-title {
      font-size: 1.1rem !important;
      line-height: 1;
      margin-bottom: 0.15rem !important;
    }
    
    .mobile-subtitle {
      font-size: 0.75rem !important;
      line-height: 1.1;
      margin-bottom: 0.35rem !important;
    }
    
    .min-vh-25 {
      min-height: 8vh;
      padding: 0.35rem 0;
    }
    
    :global(.btn-lg) {
      padding: 0.2rem 0.5rem;
      font-size: 0.75rem;
    }
  }

  /* Ajustes adicionales para pantallas muy pequeñas */
  @media (max-width: 480px) {
    .min-vh-25 {
      min-height: 6vh;
      padding: 0.25rem 0;
    }

    .mobile-title {
      font-size: 1rem !important;
    }

    .mobile-subtitle {
      font-size: 0.7rem !important;
      margin-bottom: 0.25rem !important;
    }

    :global(.btn-lg) {
      padding: 0.15rem 0.4rem;
      font-size: 0.7rem;
    }
  }
</style>