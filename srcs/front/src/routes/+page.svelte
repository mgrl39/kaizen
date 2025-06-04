<script>
  import { onMount } from 'svelte';
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  import HeroBanner from '$lib/components/HeroBanner.svelte';

  onMount(() => {
    // Inicialización básica si es necesaria
  });
</script>

<div data-bs-theme={$theme}>
  <HeroBanner 
    title="Kaizen Cinema"
    subtitle={$t('heroSubtitle')}
    imageUrl="/images/banners/k57fgmdt.png"
    buttonText={$t('heroCta')}
    buttonUrl="/movies"
  />

  <!-- Servicios -->
  <section class="mb-5 py-5">
    <h2 class="h1 text-center mb-5">{$t('servicesTitle')}</h2>
    <div class="row g-4">
      <div class="col-md-3 mb-4">
        <div class="text-center feature-card p-4">
          <i class="bi bi-badge-4k-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">{$t('servicesProjection4kTitle')}</h3>
          <p class="text-muted">{$t('servicesProjection4kDesc')}</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center feature-card p-4">
          <i class="bi bi-soundwave text-primary display-4 mb-3"></i>
          <h3 class="h5">{$t('servicesDolbyAtmosTitle')}</h3>
          <p class="text-muted">{$t('servicesDolbyAtmosDesc')}</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center feature-card p-4">
          <i class="bi bi-cup-hot-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">{$t('servicesPremiumBarTitle')}</h3>
          <p class="text-muted">{$t('servicesPremiumBarDesc')}</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="text-center feature-card p-4">
          <i class="bi bi-car-front-fill text-primary display-4 mb-3"></i>
          <h3 class="h5">{$t('servicesFreeParking')}</h3>
          <p class="text-muted">{$t('servicesFreeDesc')}</p>
        </div>
      </div>
    </div>
  </section>
</div>

<style>
  .text-primary {
    color: #9333ea !important;
  }

  /* Estilos para features */
  .feature-card {
    background: var(--bs-body-bg);
    border-radius: 1rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(var(--bs-primary-rgb), 0.1);
  }

  .feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175);
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .display-4 {
      font-size: 2.5rem;
    }
  }
</style> 