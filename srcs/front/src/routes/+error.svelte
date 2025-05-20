<script lang="ts">
  import { page } from '$app/stores';
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  
  $: statusCode = $page.status || '404';
  $: errorMessage = $page.error?.message || $t('pageNotFound');
  
  $: errorTitle = statusCode === 404 ? $t('pageNotFoundTitle') : 
                 statusCode === 403 ? $t('accessDeniedTitle') :
                 statusCode === 500 ? $t('serverErrorTitle') :
                 $t('genericErrorTitle');
</script>

<section class="py-5" data-bs-theme={$theme}>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 text-center">
        <div class="error-icon mb-4">
          <i class="bi bi-exclamation-circle display-1 text-primary"></i>
        </div>
        <h1 class="display-1 fw-bold text-primary">{statusCode}</h1>
        <h2 class="fs-2 mb-3">{errorTitle}</h2>
        <p class="mb-5">{errorMessage}</p>
        
        <div class="d-flex justify-content-center gap-3">
          <a href="/" class="btn btn-primary">
            <i class="bi bi-house-door me-2"></i>
            {$t('backToHome', 'Volver al inicio')}
          </a>
          <button class="btn btn-outline-secondary" on:click={() => history.back()}>
            <i class="bi bi-arrow-left me-2"></i>
            {$t('goBack', 'Volver atrás')}
          </button>
        </div>
      </div>
    </div>
  </div>
</section>
