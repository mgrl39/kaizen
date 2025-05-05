<script lang="ts">
  // Propiedades personalizables
  export let statusCode = "404";
  export let title = "Página no encontrada";
  export let message = "Lo sentimos, no pudimos encontrar la página que estás buscando.";
  export let buttonText = "Volver al inicio";
  export let buttonHref = "/";
  export let showHomeButton = true;
  
  // Para personalizar aún más el estilo basado en tipo de error
  export let variant: 'error' | 'warning' | 'info' = 'error';
  
  // Mapeo de variantes a clases CSS
  const variantClasses = {
    'error': 'error-gradient',
    'warning': 'warning-gradient',
    'info': 'info-gradient'
  };
  
  // Obtener la clase según la variante
  $: gradientClass = variantClasses[variant] || variantClasses.error;
</script>

<div class="error-container d-flex align-items-center justify-content-center">
  <div class="content-wrapper text-center p-5">
    <div class="error-code-wrapper mb-4">
      <h1 class="display-1 {gradientClass}">{statusCode}</h1>
    </div>
    <h2 class="error-title mb-3">{title}</h2>
    <p class="error-message text-muted mb-4">{message}</p>
    
    {#if showHomeButton}
      <a href={buttonHref} class="btn btn-primary btn-lg">
        <i class="bi bi-house-door me-2"></i>
        {buttonText}
      </a>
    {/if}
    <slot />
  </div>
</div>
