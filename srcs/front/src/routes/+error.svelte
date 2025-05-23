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

<div class="error-page" data-bs-theme={$theme}>
  <div class="error-content text-center">
    <div class="error-icon">
      <i class="bi bi-exclamation-circle display-1 text-primary"></i>
    </div>
    
    <h1 class="display-1 fw-bold text-primary">{statusCode}</h1>
    <h2 class="fs-2">{errorTitle}</h2>
    <p class="text-muted mb-4">{errorMessage}</p>
    
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

<style>
  .error-page {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }

  .error-content {
    max-width: 400px;
    width: 100%;
  }

  .error-icon {
    margin-bottom: 1rem;
  }

  h1 {
    margin-bottom: 0.5rem;
  }

  h2 {
    margin-bottom: 1rem;
  }

  @media (max-width: 768px) {
    :global(.display-1) {
      font-size: 4rem !important;
    }
    
    .fs-2 {
      font-size: 1.25rem !important;
    }
  }
</style>
