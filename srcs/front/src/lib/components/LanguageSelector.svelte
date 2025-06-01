<script lang="ts">
  import { onMount } from 'svelte';
  import { get } from 'svelte/store';
  import { language, languages } from '$lib/i18n';
  import { browser } from '$app/environment';

  export let isOpen = false;
  export let toggleMenu: () => void;

  let selectorRef: HTMLElement | null = null;

  const availableLanguages = Object.keys(languages);

  function selectLanguage(lang: string) {
    language.set(lang);
    toggleMenu(); // Cierra el menú desde el padre
  }

  onMount(() => {
    if (browser && !availableLanguages.includes(get(language))) {
      console.warn('Idioma no válido en localStorage, usando español como predeterminado');
      language.set('es');
    }
  });

  function handleClickOutside(event: MouseEvent) {
    if (isOpen && selectorRef && !selectorRef.contains(event.target as Node)) {
      toggleMenu();
    }
  }

  onMount(() => {
    document.addEventListener('click', handleClickOutside);
    return () => {
      document.removeEventListener('click', handleClickOutside);
    };
  });
</script>

<div class="dropdown" id="language-dropdown" bind:this={selectorRef}>
  <button 
    class="btn btn-sm btn-outline-theme" 
    type="button" 
    on:click={toggleMenu}
    aria-expanded={isOpen}
  >
    <span class="me-1">{languages[$language as keyof typeof languages]?.flag || '🌐'}</span>
    <span class="d-none d-md-inline">{languages[$language as keyof typeof languages]?.name || 'Unknown'}</span>
    <i class="bi bi-chevron-down ms-1"></i>
  </button>

  {#if isOpen}
    <ul class="dropdown-menu dropdown-menu-end show">
      {#each Object.keys(languages) as lang}
        <li>
          <button 
            class="dropdown-item {$language === lang ? 'active' : ''}" 
            on:click={() => selectLanguage(lang)}
          >
            <span class="me-2">{languages[lang as keyof typeof languages].flag}</span>
            {languages[lang as keyof typeof languages].name}
          </button>
        </li>
      {/each}
    </ul>
  {/if}
</div>

<style>
  .btn-outline-theme {
    border-color: var(--bs-border-color);
    color: var(--bs-body-color);
  }

  .btn-outline-theme:hover {
    background-color: var(--bs-tertiary-bg);
    border-color: var(--bs-border-color);
    color: var(--bs-body-color);
  }

  :global(html[data-bs-theme="dark"]) .btn-outline-theme {
    border-color: var(--bs-border-color);
    color: var(--bs-body-color);
  }

  :global(html[data-bs-theme="dark"]) .btn-outline-theme:hover {
    background-color: var(--bs-tertiary-bg);
    border-color: var(--bs-border-color);
    color: var(--bs-body-color);
  }
</style>
