<script lang="ts">
  import { fade, scale } from 'svelte/transition';

  export let show = false;
  export let url: string;
  export let title: string;

  let showToast = false;
  let toastMessage = '';
  let toastType = '';

  function displayToast(message: string, type: string) {
    toastMessage = message;
    toastType = type;
    showToast = true;
    setTimeout(() => {
      showToast = false;
    }, 2000);
  }

  function shareTwitter() {
    const shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
    window.open(shareUrl, '_blank', 'width=550,height=420');
  }

  function shareWhatsApp() {
    const shareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(`${title}\n${url}`)}`;
    window.open(shareUrl, '_blank');
  }

  function shareTelegram() {
    const shareUrl = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
    window.open(shareUrl, '_blank', 'width=550,height=420');
  }

  function copyToClipboard() {
    try {
      const tempInput = document.createElement('input');
      tempInput.value = url;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand('copy');
      document.body.removeChild(tempInput);
      displayToast('✓', 'success');
    } catch (err) {
      console.error('Error al copiar:', err);
      displayToast('✕', 'error');
    }
  }

  function nativeShare() {
    if (navigator.share) {
      navigator.share({
        title,
        url
      }).catch(console.error);
    }
  }
</script>

{#if show}
  <div 
    class="share-popup-backdrop"
    on:click={() => show = false}
    in:fade={{ duration: 200 }}
  ></div>
  
  <div 
    class="share-popup"
    in:scale={{ duration: 200, start: 0.95 }}
  >
    <div class="share-options">
      <!-- Native Share (mobile) -->
      {#if navigator.share}
        <button 
          class="share-btn native"
          on:click={nativeShare}
        >
          <i class="bi bi-share-fill"></i>
        </button>
      {/if}

      <!-- Twitter -->
      <button 
        class="share-btn twitter"
        on:click={shareTwitter}
      >
        <i class="bi bi-twitter"></i>
      </button>

      <!-- WhatsApp -->
      <button 
        class="share-btn whatsapp"
        on:click={shareWhatsApp}
      >
        <i class="bi bi-whatsapp"></i>
      </button>

      <!-- Telegram -->
      <button 
        class="share-btn telegram"
        on:click={shareTelegram}
      >
        <i class="bi bi-telegram"></i>
      </button>

      <!-- Copy Link -->
      <button 
        class="share-btn copy"
        on:click={copyToClipboard}
      >
        <i class="bi bi-link-45deg"></i>
      </button>
    </div>
  </div>

  <!-- Toast Notification -->
  {#if showToast}
    <div 
      class="toast-notification {toastType}"
      in:scale={{ duration: 200, start: 0.8 }}
      out:fade={{ duration: 150 }}
    >
      {toastMessage}
    </div>
  {/if}
{/if}

<style>
  .share-popup-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 1050;
  }

  .share-popup {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--bs-body-bg);
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    z-index: 1051;
  }

  .share-options {
    display: flex;
    gap: 12px;
  }

  .share-btn {
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .share-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  }

  .share-btn.native {
    background: var(--bs-primary);
  }

  .share-btn.twitter {
    background: #1DA1F2;
  }

  .share-btn.whatsapp {
    background: #25D366;
  }

  .share-btn.telegram {
    background: #0088cc;
  }

  .share-btn.copy {
    background: var(--bs-gray-600);
  }

  .toast-notification {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 16px 24px;
    border-radius: 50%;
    font-size: 24px;
    z-index: 1052;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
  }

  .toast-notification.success {
    background: rgba(40, 167, 69, 0.9);
  }

  .toast-notification.error {
    background: rgba(220, 53, 69, 0.9);
  }

  @media (max-width: 768px) {
    .share-popup {
      bottom: 0;
      left: 0;
      right: 0;
      transform: none;
      border-radius: 16px 16px 0 0;
      padding: 24px 16px;
    }

    .share-options {
      justify-content: space-around;
    }
  }
</style> 