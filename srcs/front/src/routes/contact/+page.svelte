<script lang="ts">
  import { theme } from '$lib/theme';
  import { t } from '$lib/i18n';

  // Page metadata
  $: pageTitle = `${$t('contact')} | Kaizen Cinema`;
  $: pageDescription = $t('contactSubtitle');

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
      title: 'generalInfo',
      description: 'generalInfoDesc',
      icon: 'info-circle',
      email: 'kaizencontact@doncom.me',
      color: 'indigo'
    },
    {
      id: 'technical',
      title: 'technicalSupport',
      description: 'technicalSupportDesc',
      icon: 'code-slash',
      url: 'https://github.com/mgrl39/kaizen/issues/new?template=BLANK_ISSUE',
      color: 'purple'
    },
    {
      id: 'helpdesk',
      title: 'helpDesk',
      description: 'helpDeskDesc',
      icon: 'headset',
      url: '/contact/helpdesk',
      color: 'teal'
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
</script>

<svelte:head>
  <title>{pageTitle}</title>
  <meta name="description" content={pageDescription} />
</svelte:head>

<div class="container" data-bs-theme={currentTheme}>
  <div class="row justify-content-center">
    <div>
      <!-- Header -->
      <h1 class="display-5 fw-bold mb-2 text-center">{$t('contactTitle')}</h1>
      <p class="mb-3 text-center">{$t('contactSubtitle')}</p>

      <!-- Contact Options -->
      <div class="row mb-4 g-4">
        {#each contactOptions as option}
          <div class="col-md-4">
            <div class="contact-card h-100 position-relative overflow-hidden" data-color={option.color}>
              <div class="contact-card-content p-4">
                <div class="icon-wrapper mb-4">
                  <i class="bi bi-{option.icon}"></i>
                </div>
                <h2 class="h4 mb-3">{$t(option.title)}</h2>
                <p class="text-body-secondary mb-4">{$t(option.description)}</p>
                {#if option.email}
                  <a href="mailto:{option.email}" class="btn btn-custom">
                    <i class="bi bi-envelope me-2"></i> {$t('sendEmail')}
                  </a>
                {:else}
                  <a href={option.url} target="_blank" rel="noopener noreferrer" class="btn btn-custom">
                    {#if option.id === 'technical'}
                      <i class="bi bi-github me-2"></i> {$t('createIssue')}
                    {:else}
                      <i class="bi bi-headset me-2"></i> {$t('getHelp')}
                    {/if}
                  </a>
                {/if}
              </div>
            </div>
          </div>
        {/each}
      </div>

      <!-- Contact Form -->
      <div class="contact-card mb-4" data-color="indigo">
        <div class="contact-card-content p-4">
          <div class="icon-wrapper mb-4">
            <i class="bi bi-envelope"></i>
          </div>
          <h2 class="h4 mb-3">{$t('quickContactForm')}</h2>

          {#if formSubmitted}
            <div class="alert alert-success py-3 rounded-lg bg-green-100 border-green-200">
              <i class="bi bi-check-circle me-2"></i> {$t('thankYouMessage')}
            </div>
            <button class="btn-custom mt-3" on:click={() => formSubmitted = false}>
              <i class="bi bi-arrow-repeat me-2"></i> {$t('newMessage')}
            </button>
          {:else}
            {#if formError}
              <div class="alert alert-danger py-3 rounded-lg bg-red-100 border-red-200">
                <i class="bi bi-exclamation-triangle me-2"></i> {$t('errorSendingMessage')}
              </div>
            {/if}

            <form 
              action="https://formspree.io/f/xqaqjozd" 
              method="POST" 
              on:submit={handleSubmit}
              class="space-y-4"
            >
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <input 
                    type="text" 
                    name="name" 
                    placeholder={$t('yourName')} 
                    class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all duration-300"
                    required 
                  />
                </div>
                <div>
                  <input 
                    type="email" 
                    name="email" 
                    placeholder={$t('emailPlaceholder')} 
                    class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all duration-300"
                    required 
                  />
                </div>
              </div>
              <input 
                type="text" 
                name="subject" 
                placeholder={$t('subject')} 
                class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all duration-300"
                required 
              />
              <textarea 
                name="message" 
                placeholder={$t('howCanWeHelp')} 
                rows="3" 
                class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all duration-300"
                required
              ></textarea>
              <button type="submit" class="btn-custom" disabled={loading}>
                {#if loading}
                  <span class="inline-block animate-spin mr-2">⌛</span> {$t('sending')}
                {:else}
                  <i class="bi bi-send me-2"></i> {$t('send')}
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

  .contact-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 24px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    position: relative;
  }

  .contact-card::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 24px;
    padding: 2px;
    background: linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.15),
      rgba(255, 255, 255, 0.05),
      rgba(255, 255, 255, 0)
    );
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
  }

  .contact-card[data-color="indigo"] {
    background: linear-gradient(135deg, rgba(76, 29, 149, 0.08) 0%, rgba(124, 58, 237, 0.08) 100%);
  }

  .contact-card[data-color="purple"] {
    background: linear-gradient(135deg, rgba(126, 34, 206, 0.08) 0%, rgba(168, 85, 247, 0.08) 100%);
  }

  .contact-card[data-color="teal"] {
    background: linear-gradient(135deg, rgba(15, 118, 110, 0.08) 0%, rgba(20, 184, 166, 0.08) 100%);
  }

  .contact-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
  }

  .contact-card:hover::after {
    background: linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.3),
      rgba(255, 255, 255, 0.1),
      rgba(255, 255, 255, 0)
    );
  }

  :global([data-bs-theme="dark"]) .contact-card {
    background: rgba(17, 24, 39, 0.4);
    border-color: rgba(255, 255, 255, 0.05);
  }

  :global([data-bs-theme="dark"]) .contact-card[data-color="indigo"] {
    background: linear-gradient(135deg, rgba(76, 29, 149, 0.15) 0%, rgba(124, 58, 237, 0.15) 100%);
  }

  :global([data-bs-theme="dark"]) .contact-card[data-color="purple"] {
    background: linear-gradient(135deg, rgba(126, 34, 206, 0.15) 0%, rgba(168, 85, 247, 0.15) 100%);
  }

  :global([data-bs-theme="dark"]) .contact-card[data-color="teal"] {
    background: linear-gradient(135deg, rgba(15, 118, 110, 0.15) 0%, rgba(20, 184, 166, 0.15) 100%);
  }

  :global([data-bs-theme="dark"]) .contact-card:hover {
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
  }

  :global([data-bs-theme="dark"]) .contact-card::after {
    background: linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.1),
      rgba(255, 255, 255, 0.03),
      rgba(255, 255, 255, 0)
    );
  }

  :global([data-bs-theme="dark"]) .contact-card:hover::after {
    background: linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.2),
      rgba(255, 255, 255, 0.05),
      rgba(255, 255, 255, 0)
    );
  }

  .contact-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 24px;
    background: radial-gradient(circle at top right, rgba(255,255,255,0.2), transparent 70%);
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .contact-card:hover::before {
    opacity: 1;
  }

  .icon-wrapper {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 20px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .contact-card[data-color="indigo"] .icon-wrapper {
    background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%);
    box-shadow: 0 8px 16px -4px rgba(76, 29, 149, 0.3);
  }

  .contact-card[data-color="purple"] .icon-wrapper {
    background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%);
    box-shadow: 0 8px 16px -4px rgba(126, 34, 206, 0.3);
  }

  .contact-card[data-color="teal"] .icon-wrapper {
    background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
    box-shadow: 0 8px 16px -4px rgba(15, 118, 110, 0.3);
  }

  .contact-card:hover .icon-wrapper {
    transform: scale(1.1);
  }

  .icon-wrapper i {
    font-size: 28px;
    color: white;
    position: relative;
    z-index: 1;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
  }

  .icon-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0));
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .contact-card:hover .icon-wrapper::after {
    opacity: 1;
  }

  .btn-custom {
    padding: 0.6rem 1.8rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border-radius: 100px;
    position: relative;
    overflow: hidden;
    border: none;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .contact-card[data-color="indigo"] .btn-custom {
    background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%);
  }

  .contact-card[data-color="purple"] .btn-custom {
    background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%);
  }

  .contact-card[data-color="teal"] .btn-custom {
    background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
  }

  .btn-custom::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0));
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .btn-custom:hover {
    transform: translateY(-2px);
    color: white;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
  }

  .btn-custom:hover::before {
    opacity: 1;
  }

  .contact-card h2 {
    background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 600;
  }

  :global([data-bs-theme="dark"]) .contact-card h2 {
    background: linear-gradient(135deg, #ffffff 0%, #cccccc 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  @media (max-width: 768px) {
    .contact-card {
      margin-bottom: 1rem;
    }

    .icon-wrapper {
      width: 56px;
      height: 56px;
      border-radius: 16px;
    }

    .icon-wrapper i {
      font-size: 24px;
    }

    .contact-card-content {
      padding: 1.5rem !important;
    }

    .btn-custom {
      width: 100%;
      text-align: center;
      padding: 0.5rem 1rem;
    }
  }

  :global([data-bs-theme="dark"]) .text-body-secondary {
    color: rgba(255, 255, 255, 0.7) !important;
  }

  /* Estilos adicionales para el formulario */
  :global([data-bs-theme="dark"]) input,
  :global([data-bs-theme="dark"]) textarea {
    color: white !important;
    background-color: rgba(255, 255, 255, 0.05) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
  }

  :global([data-bs-theme="dark"]) input::placeholder,
  :global([data-bs-theme="dark"]) textarea::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
  }

  :global([data-bs-theme="dark"]) input:focus,
  :global([data-bs-theme="dark"]) textarea:focus {
    border-color: rgba(255, 255, 255, 0.2) !important;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1) !important;
  }

  /* Estilos para los alerts */
  .alert {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
  }

  .alert-success {
    background: rgba(34, 197, 94, 0.1);
    border-color: rgba(34, 197, 94, 0.2);
    color: #16a34a;
  }

  .alert-danger {
    background: rgba(239, 68, 68, 0.1);
    border-color: rgba(239, 68, 68, 0.2);
    color: #dc2626;
  }

  :global([data-bs-theme="dark"]) .alert-success {
    background: rgba(34, 197, 94, 0.15);
    border-color: rgba(34, 197, 94, 0.3);
    color: #4ade80;
  }

  :global([data-bs-theme="dark"]) .alert-danger {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.3);
    color: #f87171;
  }

  /* Ajustes adicionales para el formulario */
  form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  input, textarea {
    transition: all 0.3s ease;
    font-size: 0.95rem;
  }

  input:hover, textarea:hover {
    border-color: rgba(255, 255, 255, 0.3);
  }

  .space-y-4 > * + * {
    margin-top: 1rem;
  }

  .grid {
    display: grid;
  }

  .grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }

  @media (min-width: 768px) {
    .md\:grid-cols-2 {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  .gap-4 {
    gap: 1rem;
  }
</style>
