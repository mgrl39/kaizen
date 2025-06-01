<script lang="ts">
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  
  const currentYear = new Date().getFullYear();
  
  // Enlaces para el footer
  const links = [
    { url: '/about', text: $t('about') },
    { url: '/movies', text: $t('movies') },
    { url: '/cinemas', text: $t('cinemas') },
    { url: '/contact', text: $t('contact') }
  ];
  
  // Enlaces legales
  const legalLinks = [
    { url: '/terms', text: $t('terms') },
    { url: '/privacy', text: $t('privacy') }
  ];
  
  // Redes sociales
  const socialLinks = [
    { icon: 'facebook', url: 'http://facebook.com', label: 'Facebook' },
    { icon: 'twitter', url: 'http://twitter.com', label: 'Twitter' },
    { icon: 'instagram', url: 'http://instagram.com', label: 'Instagram' },
    { icon: 'youtube', url: 'http://youtube.com', label: 'YouTube' }
  ];
</script>

<footer class="footer py-5 mt-auto" data-bs-theme={$theme}>
  <div class="container">
    <!-- Sección superior con logo y enlaces -->
    <div class="row g-4 pb-4 border-bottom mb-4">
      <!-- Logo y descripción -->
      <div class="col-lg-4 mb-3">
        <div class="d-flex align-items-center mb-2">
          <h5 class="fw-bold mb-0 me-2">KAIZEN</h5>
          <span class="badge custom-badge">CINEMA</span>
        </div>
        <p class="text-muted mb-3">{$t('footerTagline')}</p>
        <p class="text-muted mb-0">
          <i class="bi bi-film me-2 text-custom-primary"></i>
          {$t('experienceTheBest')} 
          <span class="text-custom-primary fw-semibold">4K</span> {$t('and')} 
          <span class="text-custom-primary fw-semibold">IMAX</span>
        </p>
      </div>
      
      <!-- Enlaces rápidos -->
      <div class="col-6 col-md-3 col-lg-2">
        <h6 class="fw-bold text-uppercase text-custom-primary mb-3">{$t('quickLinks')}</h6>
        <ul class="list-unstyled">
          {#each links as link}
            <li class="mb-2">
              <a href={link.url} class="text-decoration-none text-body-secondary hover-custom">
                {link.text}
              </a>
            </li>
          {/each}
        </ul>
      </div>
      
      <!-- Enlaces legales -->
      <div class="col-6 col-md-3 col-lg-2">
        <h6 class="fw-bold text-uppercase text-custom-primary mb-3">{$t('legal')}</h6>
        <ul class="list-unstyled">
          {#each legalLinks as link}
            <li class="mb-2">
              <a href={link.url} class="text-decoration-none text-body-secondary hover-custom">
                {link.text}
              </a>
            </li>
          {/each}
        </ul>
      </div>
      
      <!-- Contacto -->
      <div class="col-md-6 col-lg-4">
        <h6 class="fw-bold text-uppercase text-custom-primary mb-3">{$t('contactUs')}</h6>
        <ul class="list-unstyled">
          <li class="mb-2 d-flex align-items-center">
            <i class="bi bi-geo-alt me-2 text-custom-primary"></i>
            <span>Westfield La Maquinista</span>
          </li>
          <li class="mb-2 d-flex align-items-center">
            <i class="bi bi-telephone me-2 text-custom-primary"></i>
            <span>+34 612 345 678</span>
          </li>
          <li class="mb-2 d-flex align-items-center">
            <i class="bi bi-envelope me-2 text-custom-primary"></i>
            <span>kaizen@doncom.me</span>
          </li>
        </ul>
        
        <!-- Redes sociales -->
        <div class="d-flex mt-3">
          {#each socialLinks as social}
            <a href={social.url} class="btn btn-sm btn-outline-custom rounded-circle me-2" aria-label={social.label}>
              <i class="bi bi-{social.icon}"></i>
            </a>
          {/each}
        </div>
      </div>
    </div>
    
    <!-- Sección inferior con copyright -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
      <p class="text-muted mb-3 mb-md-0">
        MIT License {currentYear} Kaizen Cinema.
      </p>
    </div>
  </div>
</footer>

<style>
  /* Variables de color personalizadas */
  :root {
    --custom-primary: #6d28d9;
    --custom-primary-hover: #5b21b6;
    --custom-primary-light: rgba(109, 40, 217, 0.1);
  }

  /* Estilos personalizados */
  .custom-badge {
    background-color: var(--custom-primary) !important;
    color: white;
  }

  .custom-btn {
    background-color: var(--custom-primary);
    color: white;
    border: 1px solid var(--custom-primary);
    transition: all 0.2s ease;
  }

  .custom-btn:hover {
    background-color: var(--custom-primary-hover);
    border-color: var(--custom-primary-hover);
    color: white;
  }

  .text-custom-primary {
    color: var(--custom-primary) !important;
  }

  .btn-outline-custom {
    color: var(--custom-primary);
    border-color: var(--custom-primary);
    transition: all 0.2s ease;
  }

  .btn-outline-custom:hover {
    background-color: var(--custom-primary);
    border-color: var(--custom-primary);
    color: white;
  }

  /* Hover personalizado para los enlaces */
  :global(.hover-custom) {
    transition: color 0.2s ease;
  }

  :global(.hover-custom:hover) {
    color: var(--custom-primary) !important;
  }
  
  /* Estilo para el footer */
  .footer {
    background-color: var(--bs-body-bg);
    border-top: 1px solid var(--bs-border-color);
  }
</style>