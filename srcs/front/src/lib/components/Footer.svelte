<script lang="ts">
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  
  const currentYear = new Date().getFullYear();
  
  // Enlaces para el footer
  const links = [
    { url: '/about', text: $t('about', 'Acerca de nosotros') },
    { url: '/movies', text: $t('movies', 'Películas') },
    { url: '/cinemas', text: $t('cinemas', 'Cines') },
    { url: '/contact', text: $t('contact', 'Contacto') }
  ];
  
  // Enlaces legales
  const legalLinks = [
    { url: '/terms', text: $t('terms', 'Términos y condiciones') },
    { url: '/privacy', text: $t('privacy', 'Política de privacidad') }
  ];
  
  // Redes sociales
  const socialLinks = [
    { icon: 'facebook', url: '#', label: 'Facebook' },
    { icon: 'twitter', url: '#', label: 'Twitter' },
    { icon: 'instagram', url: '#', label: 'Instagram' },
    { icon: 'youtube', url: '#', label: 'YouTube' }
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
          <span class="badge bg-primary">CINEMA</span>
        </div>
        <p class="text-muted mb-3">{$t('footerTagline', 'Tu destino para las mejores experiencias cinematográficas')}</p>
        
        <!-- Botón de newsletter -->
        <div class="input-group">
          <input type="email" class="form-control" placeholder={$t('enterEmail', 'Ingresa tu email')} aria-label="Email" aria-describedby="subscribe-btn">
          <button class="btn btn-primary" type="button" id="subscribe-btn">
            <i class="bi bi-envelope me-1"></i> {$t('subscribe', 'Suscribirse')}
          </button>
        </div>
      </div>
      
      <!-- Enlaces rápidos -->
      <div class="col-6 col-md-3 col-lg-2">
        <h6 class="fw-bold text-uppercase text-primary mb-3">{$t('quickLinks', 'Enlaces')}</h6>
        <ul class="list-unstyled">
          {#each links as link}
            <li class="mb-2">
              <a href={link.url} class="text-decoration-none text-body-secondary hover-primary">
                {link.text}
              </a>
            </li>
          {/each}
        </ul>
      </div>
      
      <!-- Enlaces legales -->
      <div class="col-6 col-md-3 col-lg-2">
        <h6 class="fw-bold text-uppercase text-primary mb-3">{$t('legal', 'Legal')}</h6>
        <ul class="list-unstyled">
          {#each legalLinks as link}
            <li class="mb-2">
              <a href={link.url} class="text-decoration-none text-body-secondary hover-primary">
                {link.text}
              </a>
            </li>
          {/each}
        </ul>
      </div>
      
      <!-- Contacto -->
      <div class="col-md-6 col-lg-4">
        <h6 class="fw-bold text-uppercase text-primary mb-3">{$t('contactUs', 'Contáctanos')}</h6>
        <ul class="list-unstyled">
          <li class="mb-2 d-flex align-items-center">
            <i class="bi bi-geo-alt me-2 text-primary"></i>
            <span>123 Calle Principal, Ciudad, País</span>
          </li>
          <li class="mb-2 d-flex align-items-center">
            <i class="bi bi-telephone me-2 text-primary"></i>
            <span>+1 (234) 567-8900</span>
          </li>
          <li class="mb-2 d-flex align-items-center">
            <i class="bi bi-envelope me-2 text-primary"></i>
            <span>info@kaizencinema.com</span>
          </li>
        </ul>
        
        <!-- Redes sociales -->
        <div class="d-flex mt-3">
          {#each socialLinks as social}
            <a href={social.url} class="btn btn-sm btn-outline-secondary rounded-circle me-2" aria-label={social.label}>
              <i class="bi bi-{social.icon}"></i>
            </a>
          {/each}
        </div>
      </div>
    </div>
    
    <!-- Sección inferior con copyright -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
      <p class="text-muted mb-3 mb-md-0">
        © {currentYear} Kaizen Cinema. {$t('allRightsReserved', 'Todos los derechos reservados')}
      </p>
      
      <!-- Métodos de pago -->
      <div class="d-flex align-items-center">
        <small class="text-muted me-3">{$t('paymentMethods', 'Métodos de pago')}:</small>
        <i class="bi bi-credit-card me-2" title="Credit Card"></i>
        <i class="bi bi-paypal me-2" title="PayPal"></i>
        <i class="bi bi-wallet2" title="Digital Wallet"></i>
      </div>
    </div>
  </div>
</footer>

<style>
  /* Solo un estilo mínimo para el hover de los enlaces */
  :global(.hover-primary:hover) {
    color: var(--bs-primary) !important;
    transition: color 0.2s;
  }
  
  /* Estilo para el footer */
  .footer {
    background-color: var(--bs-body-bg);
    border-top: 1px solid var(--bs-border-color);
  }
</style>