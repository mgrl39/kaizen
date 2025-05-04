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
    
    <!-- Slot para contenido adicional -->
    <slot />
  </div>
</div>

<div class="decorative-blob blob-1"></div>
<div class="decorative-blob blob-2"></div> 

<style>
  .error-container {
    height: 80vh;
    position: relative;
    z-index: 1;
  }
  
  .content-wrapper {
    background: rgba(18, 18, 18, 0.7);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    max-width: 600px;
    width: 90%;
  }
  
  .error-code-wrapper {
    position: relative;
    display: inline-block;
  }
  
  .error-gradient {
    background: linear-gradient(135deg, #ff4e50, #f9d423);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 20px rgba(249, 212, 35, 0.4);
  }
  
  .warning-gradient {
    background: linear-gradient(135deg, #f6d365, #fda085);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 20px rgba(243, 168, 133, 0.4);
  }
  
  .info-gradient {
    background: linear-gradient(135deg, #0093E9, #80D0C7);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 20px rgba(128, 208, 199, 0.4);
  }
  
  .error-title {
    font-size: 2rem;
    font-weight: 600;
    color: #f8f9fa;
  }
  
  .error-message {
    font-size: 1.1rem;
    line-height: 1.6;
  }
  
  /* Animations for the blobs (opcional) */
  @keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
  }
  
  .decorative-blob {
    position: fixed;
    border-radius: 50%;
    filter: blur(80px);
    z-index: -1;
    opacity: 0.5;
  }
  
  .blob-1 {
    width: 400px;
    height: 400px;
    background: rgba(109, 40, 217, 0.4);
    bottom: -100px;
    right: -100px;
    animation: float 8s ease-in-out infinite;
  }
  
  .blob-2 {
    width: 300px;
    height: 300px;
    background: rgba(219, 39, 119, 0.3);
    top: -100px;
    left: -50px;
    animation: float 6s ease-in-out infinite;
  }
</style>