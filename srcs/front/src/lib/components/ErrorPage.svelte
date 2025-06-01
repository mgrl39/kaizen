<script>
  // Propiedades personalizables
  export let statusCode = "404";
  export let title = "Página no encontrada";
  export let message = "Lo sentimos, no pudimos encontrar la página que estás buscando.";
  export let buttonText = "Volver al inicio";
  export let buttonHref = "/";
  export let showHomeButton = true;
  export let variant = 'error'; // 'error', 'warning', 'info'
  
  import { t } from '$lib/i18n';
  
  // Mapeo simplificado de variantes
  const icons = {
    'error': 'exclamation-circle',
    'warning': 'exclamation-triangle',
    'info': 'info-circle'
  };
  
  $: iconClass = icons[variant] || icons.error;
</script>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8 text-center">
      <!-- Icono y código de estado -->
      <div class="mb-4">
        <i class="bi bi-{iconClass} display-1 text-{variant}"></i>
      </div>
      
      <h1 class="display-4 fw-bold text-{variant}">{statusCode}</h1>
      <h2 class="h4 mb-3">{title}</h2>
      <p class="text-muted mb-4">{message}</p>
      
      <!-- Botones -->
      <div class="d-flex gap-2 justify-content-center">
        {#if showHomeButton}
          <a href={buttonHref} class="btn btn-primary">
            <i class="bi bi-house-door me-2"></i>{buttonText}
          </a>
        {/if}
        
        <button class="btn btn-outline-secondary" on:click={() => history.back()}>
          <i class="bi bi-arrow-left me-2"></i>{$t('goBack')}
        </button>
      </div>
      
      <!-- Info adicional -->
      <p class="small text-muted mt-4">
        <i class="bi bi-info-circle me-1"></i>{$t('errorMessage')}
      </p>
    </div>
  </div>
</div>
