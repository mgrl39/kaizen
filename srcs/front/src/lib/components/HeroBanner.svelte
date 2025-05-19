<script lang="ts">
  import { t } from '$lib/i18n';
  
  // Props para personalizar el banner
  export let title = "Bienvenido a Kaizen Cinema";
  export let subtitle = "La mejor experiencia cinematográfica";
  export let imageUrl = "/images/hero-default.jpg";
  export let buttonText = "Ver películas";
  export let buttonUrl = "/movies";
  
  import { theme } from '$lib/theme';
</script>

<div class="p-5 mb-4 bg-body-tertiary rounded-3" data-bs-theme={$theme}>
  <div class="container py-5">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <h1 class="display-5 fw-bold">{title}</h1>
        <p class="fs-4">{subtitle}</p>
        <a href={buttonUrl} class="btn btn-primary btn-lg">
          {buttonText}
          <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
      <div class="col-lg-6 d-none d-lg-block">
        <img src={imageUrl} alt="{title}" class="img-fluid rounded shadow">
      </div>
    </div>
  </div>
</div>

<style>
  .text-shadow-lg {
    text-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
  }
</style>