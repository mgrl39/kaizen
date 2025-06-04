<script lang="ts">
  import { onMount } from 'svelte';
  import { theme } from '$lib/theme';
  import { t } from '$lib/i18n';

  // Page metadata
  $: pageTitle = `${$t('helpDesk')} | Kaizen Cinema`;
  $: pageDescription = $t('helpDeskDesc');

  // Theme reactivity
  $: currentTheme = $theme;

  let isChatOpen = false;
  let isButtonVisible = true;

  onMount(() => {
    // Load chat widget
    const s1 = document.createElement("script");
    s1.async = true;
    s1.src = 'https://embed.tawk.to/682d391da55be4190a7c5bab/1iroae6sn';
    s1.charset = 'UTF-8';
    s1.setAttribute('crossorigin', '*');
    document.getElementsByTagName("script")[0].parentNode?.insertBefore(s1, null);

    // Observar cambios en el widget de Tawk.to
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.addedNodes.length) {
          const tawkWidget = document.getElementById('tawk-widget');
          if (tawkWidget) isButtonVisible = false;
        }
      });
    });

    observer.observe(document.body, { childList: true, subtree: true });
  });

  function toggleChat() {
    if (window.Tawk_API) {
      if (isChatOpen) {
        window.Tawk_API.minimize();
      } else {
        window.Tawk_API.maximize();
      }
      isChatOpen = !isChatOpen;
    }
  }
</script>

<svelte:head>
  <title>{pageTitle}</title>
  <meta name="description" content={pageDescription} />
</svelte:head>

<div class="container py-4" data-bs-theme={currentTheme}>
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="contact-card">
        <div class="contact-card-content p-4">
          <div class="text-center">
            <div class="icon-wrapper mx-auto mb-3">
              <i class="bi bi-headset"></i>
            </div>
            <h1 class="h4 mb-2">{$t('helpDesk')}</h1>
            <p class="text-muted mb-0">{$t('chatWithSupportDesc')}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="arrow-container">
  <i class="bi bi-arrow-down-right"></i>
</div>

<style>
  .contact-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    position: relative;
    background: linear-gradient(135deg, rgba(15, 118, 110, 0.08) 0%, rgba(20, 184, 166, 0.08) 100%);
  }

  :global([data-bs-theme="dark"]) .contact-card {
    background: linear-gradient(135deg, rgba(15, 118, 110, 0.15) 0%, rgba(20, 184, 166, 0.15) 100%);
  }

  .icon-wrapper {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
    box-shadow: 0 8px 16px -4px rgba(15, 118, 110, 0.3);
    transition: all 0.3s ease;
  }

  .icon-wrapper i {
    font-size: 24px;
    color: white;
  }

  @media (max-width: 768px) {
    .container {
      padding-top: 1rem !important;
      padding-bottom: 1rem !important;
    }
    
    .icon-wrapper {
      width: 48px;
      height: 48px;
    }

    .icon-wrapper i {
      font-size: 20px;
    }
  }

  .arrow-container {
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    animation: bounceDownRight 1.5s infinite;
    z-index: 999;
  }

  .arrow-container i {
    font-size: 64px;
    color: #14b8a6;
    text-shadow: 0 0 10px rgba(20, 184, 166, 0.3);
  }

  @keyframes bounceDownRight {
    0%, 100% {
      transform: translate(-50%, -50%);
    }
    50% {
      transform: translate(-45%, -45%);
    }
  }

  @media (max-width: 768px) {
    .arrow-container {
      transform: translate(-50%, -50%) scale(0.8);
    }

    .arrow-container i {
      font-size: 48px;
    }
  }
</style> 