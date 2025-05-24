<script lang="ts">
  import { onMount } from 'svelte';
  import { theme } from '$lib/theme';
  import Navbar from '$lib/components/Navbar.svelte';
  import { marked } from 'marked';

  let currentTheme = 'light';
  $: currentTheme = $theme;

  let content: string | Promise<String> = '';
  let loading = true;

  onMount(async () => {
    window.scrollTo(0, 0);
    try {
      const res = await fetch('/docs/privacy.md');
      const raw = await res.text();
      content = await marked(raw);
    } catch (err) {
      content = '<p class="text-danger">Error al cargar la política de privacidad.</p>';
    } finally {
      loading = false;
    }
  });
</script>

<svelte:head>
  <title>Política de Privacidad | Kaizen Cinema</title>
  <meta name="description" content="Política de privacidad de Kaizen Cinema. Conoce cómo recopilamos, utilizamos y protegemos tus datos personales." />
</svelte:head>

<Navbar />

<div class="container py-5" data-bs-theme={currentTheme}>
  <div class="row justify-content-center">
    <div class="col-12 col-lg-10">
      <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">Política de Privacidad</h1>
        <p class="text-muted">Última actualización: {new Date().toLocaleDateString()}</p>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
          {#if loading}
            <p class="text-muted">Cargando contenido...</p>
          {:else}
            {@html content}
          {/if}
        </div>
      </div>

      <div class="d-flex justify-content-between mt-5 pt-4 border-top">
        <a href="/terms" class="btn btn-outline-primary">
          <i class="bi bi-file-text me-1"></i> Términos de Servicio
        </a>
        <a href="/about" class="btn btn-outline-secondary">
          <i class="bi bi-info-circle me-1"></i> Acerca de Nosotros
        </a>
      </div>
    </div>
  </div>
</div>
