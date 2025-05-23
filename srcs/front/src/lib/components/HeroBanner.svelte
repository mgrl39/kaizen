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
          e.target.src = DEFAULT_IMAGE_BASE64;
          e.target.onerror = null;
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
    background-color: #000;
  }
  
  .min-vh-25 {
    min-height: 20vh;
  }
  
  .inset-0 {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
  }
  
  .object-cover {
    object-fit: cover;
  }
  
  .text-shadow-lg {
    text-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
  }
  
  /* Estilos responsivos para móviles */
  @media (max-width: 768px) {
    .mobile-title {
      font-size: 1.75rem !important;
      line-height: 1.2;
      margin-bottom: 0.5rem !important;
    }
    
    .mobile-subtitle {
      font-size: 0.9rem !important;
      line-height: 1.4;
      margin-bottom: 1rem !important;
    }
    
    .min-vh-25 {
      min-height: 25vh;
      padding: 1rem 0;
    }
    
    :global(.btn-lg) {
      padding: 0.4rem 0.8rem;
      font-size: 0.9rem;
    }
  }
</style>