<script lang="ts">
  import { onMount } from 'svelte';
  import { theme } from '$lib/theme';

  // Page metadata
  const pageTitle = "Contacto | Kaizen Cinema";
  const pageDescription = "Contacta con el equipo de Kaizen Cinema para información general o reportar problemas técnicos.";

  // Form state
  let formSubmitted = false;
  let formError = false;
  let loading = false;
  
  // Theme reactivity
  $: currentTheme = $theme;

  // Contact options data
  const contactOptions = [
    {
      id: 'info',
      title: 'Información General',
      description: 'Para consultas sobre nuestros servicios, colaboraciones o información general.',
      icon: 'info-circle',
      email: 'kaizencontact@doncom.me',
      color: 'primary'
    },
    {
      id: 'technical',
      title: 'Soporte Técnico',
      description: 'Para reportar problemas técnicos, bugs o sugerir mejoras en la plataforma.',
      icon: 'code-slash',
      url: 'https://github.com/mgrl39/kaizen/issues/new?template=BLANK_ISSUE',
      color: 'success'
    }
  ];

  // Form submission handler
  async function handleSubmit(event: SubmitEvent) {
    event.preventDefault();
    loading = true;
    formError = false;

    try {
      const form = event.target as HTMLFormElement;
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'Accept': 'application/json' }
      });

      if (response.ok) {
        formSubmitted = true;
        form.reset();
      } else {
        formError = true;
      }
    } catch (error) {
      console.error('Error al enviar formulario:', error);
      formError = true;
    } finally {
      loading = false;
    }
  }

  onMount(() => {
    // Load chat widget
    const s1 = document.createElement("script");
    s1.async = true;
    s1.src = 'https://embed.tawk.to/682d391da55be4190a7c5bab/1iroae6sn';
    s1.charset = 'UTF-8';
    s1.setAttribute('crossorigin', '*');
    document.getElementsByTagName("script")[0].parentNode?.insertBefore(s1, null);
  });
</script>

<svelte:head>
  <title>{pageTitle}</title>
  <meta name="description" content={pageDescription} />
</svelte:head>

<div class="container" data-bs-theme={currentTheme}>
  <div class="row justify-content-center">
    <div class="col-12 col-lg-10">
      <!-- Header -->
      <h1 class="display-5 fw-bold mb-2 text-center">Contacto</h1>
      <p class="mb-3 text-center">Estamos aquí para ayudarte.</p>

      <!-- Contact Options -->
      <div class="row mb-3">
        {#each contactOptions as option}
          <div class="col-md-6 mb-3">
            <div class="card h-100 shadow-sm border-{option.color} border-top-0 border-end-0 border-bottom-0 border-3">
              <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                  <div class="bg-{option.color} bg-opacity-10 p-2 rounded-circle me-3">
                    <i class="bi bi-{option.icon} text-{option.color} fs-4"></i>
                  </div>
                  <h2 class="h5 mb-0">{option.title}</h2>
                </div>
                <p class="small">{option.description}</p>
                {#if option.email}
                  <a href="mailto:{option.email}" class="btn btn-sm btn-{option.color}">
                    <i class="bi bi-envelope me-1"></i> Enviar email
                  </a>
                {:else}
                  <a href={option.url} target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-{option.color}">
                    <i class="bi bi-github me-1"></i> Crear issue
                  </a>
                {/if}
              </div>
            </div>
          </div>
        {/each}
      </div>

      <!-- Contact Form -->
      <div class="card shadow-sm mb-3">
        <div class="card-body p-3">
          <h2 class="h5 mb-2">Formulario de contacto rápido</h2>

          {#if formSubmitted}
            <div class="alert alert-success py-2">
              <i class="bi bi-check-circle me-2"></i> ¡Gracias! Te responderemos pronto.
            </div>
            <button class="btn btn-sm btn-outline-primary" on:click={() => formSubmitted = false}>
              <i class="bi bi-arrow-repeat me-1"></i> Nuevo mensaje
            </button>
          {:else}
            {#if formError}
              <div class="alert alert-danger py-2">
                <i class="bi bi-exclamation-triangle me-2"></i> Error al enviar mensaje.
              </div>
            {/if}

            <form 
              action="https://formspree.io/f/xqaqjozd" 
              method="POST" 
              on:submit={handleSubmit}
            >
              <div class="row">
                <div class="col-md-6 mb-2">
                  <input type="text" name="name" placeholder="Tu nombre" class="form-control form-control-sm" required />
                </div>
                <div class="col-md-6 mb-2">
                  <input type="email" name="email" placeholder="tu@email.com" class="form-control form-control-sm" required />
                </div>
              </div>
              <input type="text" name="subject" placeholder="Asunto" class="form-control form-control-sm mb-2" required />
              <textarea name="message" placeholder="¿En qué podemos ayudarte?" rows="3" class="form-control form-control-sm mb-2" required></textarea>
              <button type="submit" class="btn btn-sm btn-primary" disabled={loading}>
                {#if loading}
                  <span class="spinner-border spinner-border-sm me-1"></span> Enviando...
                {:else}
                  <i class="bi bi-send me-1"></i> Enviar
                {/if}
              </button>
            </form>
          {/if}
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  :global([data-bs-theme="dark"]) .card {
    background-color: var(--bs-dark);
    border-color: var(--bs-gray-700);
  }
  :global([data-bs-theme="dark"]) .form-control {
    background-color: var(--bs-gray-800);
    border-color: var(--bs-gray-700);
    color: var(--bs-light);
  }
  :global([data-bs-theme="dark"]) .form-control:focus {
    border-color: var(--bs-primary);
  }
  :global([data-bs-theme="dark"]) .text-muted {
    color: var(--bs-gray-400) !important;
  }
</style>
