<script lang="ts">
  import { onMount } from 'svelte';
  import { theme } from '$lib/theme';
  import Navbar from '$lib/components/Navbar.svelte';
  import { marked } from 'marked';

  let currentTheme = 'light';
  let currentLang = 'es'; // Idioma por defecto
  $: currentTheme = $theme;

  let content: string | Promise<String> = '';
  let loading = true;

  onMount(async () => {
    window.scrollTo(0, 0);
    
    // Obtener el idioma guardado en localStorage o usar español por defecto
    const savedLanguage = localStorage.getItem('language') || 'es';
    currentLang = savedLanguage;

    try {
      const res = await fetch(`/docs/${currentLang}/privacy.md`);
      const raw = await res.text();
      content = await marked(raw);
    } catch (err) {
      content = `<p class="text-danger">${currentLang === 'es' ? 'Error al cargar la política de privacidad.' : 'Error loading privacy policy.'}</p>`;
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
    <div>
      <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">Política de Privacidad</h1>
        <p class="text-muted">Última actualización: {new Date().toLocaleDateString()}</p>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
          {#if loading}
            <p class="text-muted">Cargando contenido...</p>
          {:else}
            <div class="markdown-content">
              {@html content}
            </div>
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

<style>
  :global(.markdown-content) {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
  }

  :global(.markdown-content h1) {
    font-size: 2.25rem;
    font-weight: 800;
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(139, 92, 246, 0.2);
  }

  :global(.markdown-content h2) {
    font-size: 1.75rem;
    font-weight: 700;
    color: #6d28d9;
    margin-top: 2.5rem;
    margin-bottom: 1rem;
  }

  :global(.markdown-content h3) {
    font-size: 1.35rem;
    font-weight: 600;
    color: #7c3aed;
    margin-top: 2rem;
    margin-bottom: 1rem;
  }

  :global(.markdown-content p) {
    line-height: 1.8;
    color: var(--bs-body-color);
    margin-bottom: 1.25rem;
  }

  :global(.markdown-content strong) {
    color: #8b5cf6;
    font-weight: 600;
  }

  :global(.markdown-content ul), :global(.markdown-content ol) {
    margin: 1.5rem 0;
    padding-left: 0;
  }

  :global(.markdown-content li) {
    margin-bottom: 0.75rem;
    line-height: 1.7;
    position: relative;
    padding-left: 1.75rem;
    list-style: none;
  }

  :global(.markdown-content ul li::before) {
    content: '→';
    position: absolute;
    left: 0;
    color: #8b5cf6;
    font-weight: bold;
  }

  :global(.markdown-content ol) {
    counter-reset: item;
  }

  :global(.markdown-content ol li::before) {
    content: counter(item) ".";
    counter-increment: item;
    position: absolute;
    left: 0;
    color: #8b5cf6;
    font-weight: 600;
  }

  :global(.markdown-content a) {
    color: #7c3aed;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
  }

  :global(.markdown-content a:hover) {
    color: #6d28d9;
    border-bottom-color: #6d28d9;
  }

  :global(.markdown-content blockquote) {
    margin: 2rem 0;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, 
      rgba(139, 92, 246, 0.1),
      rgba(124, 58, 237, 0.05)
    );
    border-left: 4px solid #8b5cf6;
    border-radius: 0.5rem;
  }

  :global(.markdown-content blockquote p) {
    margin-bottom: 0;
    color: #6d28d9;
    font-style: italic;
    font-weight: 500;
  }

  :global(.markdown-content code) {
    background: rgba(139, 92, 246, 0.1);
    color: #7c3aed;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-family: 'Fira Code', monospace;
    font-size: 0.9em;
  }

  :global(.markdown-content pre) {
    background: rgba(139, 92, 246, 0.05);
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
  }

  :global(.markdown-content pre code) {
    background: none;
    padding: 0;
    font-size: 0.9em;
    color: inherit;
  }

  :global(.markdown-content hr) {
    margin: 2rem 0;
    border: 0;
    height: 2px;
    background: linear-gradient(to right, 
      transparent,
      rgba(139, 92, 246, 0.3),
      rgba(109, 40, 217, 0.3),
      transparent
    );
  }

  :global(.markdown-content table) {
    width: 100%;
    margin: 2rem 0;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0 0 1px rgba(139, 92, 246, 0.2);
  }

  :global(.markdown-content th) {
    background: rgba(139, 92, 246, 0.1);
    color: #6d28d9;
    font-weight: 600;
    padding: 1rem;
    text-align: left;
  }

  :global(.markdown-content td) {
    padding: 1rem;
    border-top: 1px solid rgba(139, 92, 246, 0.1);
  }

  :global(.markdown-content tr:hover) {
    background: rgba(139, 92, 246, 0.05);
  }

  /* Dark mode adjustments */
  :global([data-bs-theme="dark"] .markdown-content) {
    color: rgba(255, 255, 255, 0.9);
  }

  :global([data-bs-theme="dark"] .markdown-content blockquote) {
    background: linear-gradient(135deg, 
      rgba(139, 92, 246, 0.2),
      rgba(124, 58, 237, 0.1)
    );
  }

  :global([data-bs-theme="dark"] .markdown-content pre) {
    background: rgba(139, 92, 246, 0.1);
  }

  :global([data-bs-theme="dark"] .markdown-content th) {
    background: rgba(139, 92, 246, 0.2);
  }

  :global([data-bs-theme="dark"] .markdown-content tr:hover) {
    background: rgba(139, 92, 246, 0.15);
  }

  :global([data-bs-theme="dark"] .markdown-content code) {
    background: rgba(139, 92, 246, 0.2);
  }
</style>
