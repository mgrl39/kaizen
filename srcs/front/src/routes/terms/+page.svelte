<script lang="ts">
  import { onMount } from 'svelte';
  import { t } from '$lib/i18n';
  import { theme } from '$lib/theme';
  import Navbar from '$lib/components/Navbar.svelte';
  import Footer from '$lib/components/Footer.svelte';
  import { marked } from 'marked';

  let content: string = '';
  let loading = true;

  onMount(async () => {
    try {
      const res = await fetch('/docs/terms.md');
      const raw = await res.text();
      content = await marked(raw);
    } catch (err) {
      content = '<p class="text-danger">Error al cargar los términos y condiciones.</p>';
    } finally {
      loading = false;
    }
  });
</script>

<Navbar />

<div class="container py-5" data-bs-theme={$theme}>
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">{$t('termsAndConditions', 'Términos y Condiciones')}</h1>
        <p class="text-muted">Última actualización: 20 de mayo de 2025</p>
      </div>

      <!-- Contenido cargado desde Markdown -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
          {#if loading}
            <p class="text-muted">Cargando términos...</p>
          {:else}
            {@html content}
          {/if}
        </div>
      </div>

      <!-- Aceptación de términos -->
      <div class="card border-0 bg-light mb-4">
        <div class="card-body p-4 text-center">
          <h3 class="h5 mb-3">{$t('acceptanceOfTerms', '¿Acepta estos términos y condiciones?')}</h3>
          <p class="mb-4">
            Al utilizar nuestro sitio web y servicios, usted acepta estos términos y condiciones en su totalidad.
          </p>
          <div class="d-flex justify-content-center gap-3">
            <a href="/" class="btn btn-primary">
              <i class="bi bi-check-circle me-2"></i>
              {$t('acceptAndContinue', 'Aceptar y continuar')}
            </a>
            <button on:click={() => history.back()} class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-2"></i>
              {$t('goBack', 'Volver atrás')}
            </button>
          </div>
        </div>
      </div>

      <!-- Información adicional -->
      <div class="text-center">
        <p class="text-muted small">
          <i class="bi bi-shield-check me-1"></i>
          {$t('termsFooter', 'Estos términos están diseñados para proteger tanto a nuestros usuarios como a nuestra empresa.')}
        </p>
        <p class="text-muted small">
          <a href="/privacy" class="text-decoration-none">Política de Privacidad</a> • 
          <a href="/contact" class="text-decoration-none">Contacto</a> • 
          <a href="/faq" class="text-decoration-none">Preguntas Frecuentes</a>
        </p>
      </div>
    </div>
  </div>
</div>

<Footer />
