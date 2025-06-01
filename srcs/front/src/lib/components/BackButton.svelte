<script lang="ts">
  import { goto } from '$app/navigation';

  export let href: string = '/';
</script>

<button 
  class="btn btn-link text-decoration-none back-button" 
  on:click={() => goto(href)}
  aria-label="Go back"
>
  <i class="bi bi-arrow-left"></i>
</button>

<style>
  .back-button {
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1030;
    padding: 0.5rem;
    transition: transform 0.2s ease;
  }

  .back-button i {
    font-size: 1.5rem;
    background: linear-gradient(135deg, var(--primary-color, #6366f1), var(--accent-color, #a855f7));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .back-button:hover {
    transform: translateX(-3px);
  }

  /* Dark theme adjustments */
  :global(html[data-bs-theme="dark"]) .back-button i {
    opacity: 0.9;
  }
</style> 