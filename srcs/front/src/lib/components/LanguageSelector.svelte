<script lang="ts">
  import { onMount } from 'svelte';
  import { get } from 'svelte/store';
  import { currentLanguage, languages } from '$lib/i18n';
  import { browser } from '$app/environment';
  import { theme } from '$lib/theme';

  export let isOpen = false;
  export let toggleMenu: () => void;

  let selectorRef: HTMLElement | null = null;

  function selectLanguage(lang: string) {
    currentLanguage.set(lang);
    toggleMenu(); // Cierra el menú desde el padre si así lo controla
  }

  function getLanguageFlag(lang: string): string {
    switch(lang) {
      case 'es': return '🇪🇸';
      case 'en': return '🇬🇧';
      default: return '🌐';
    }
  }

  function getLanguageName(lang: string): string {
    switch(lang) {
      case 'es': return 'Español';
      case 'en': return 'English';
      default: return 'Unknown';
    }
  }

  onMount(() => {
    if (browser && !languages.includes(get(currentLanguage))) {
      console.warn('Idioma no válido en localStorage, usando español como predeterminado');
      currentLanguage.set('es');
    }
  });

  function handleClickOutside(event: MouseEvent) {
    if (isOpen && selectorRef && !selectorRef.contains(event.target as Node)) {
      toggleMenu(); // Usa la función pasada para cerrar
    }
  }

  onMount(() => {
    document.addEventListener('click', handleClickOutside);
    return () => {
      document.removeEventListener('click', handleClickOutside);
    };
  });

  $: themeClass = $theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark';
</script>

<div class="dropdown" id="language-dropdown" bind:this={selectorRef}>
  <button 
    class="btn btn-sm {themeClass}" 
    type="button" 
    on:click={toggleMenu}
    aria-expanded={isOpen}
  >
    <span class="me-1">{getLanguageFlag($currentLanguage)}</span>
    <span class="d-none d-md-inline">{getLanguageName($currentLanguage)}</span>
    <i class="bi bi-chevron-down ms-1"></i>
  </button>

  {#if isOpen}
    <ul class="dropdown-menu dropdown-menu-end show">
      {#each languages as lang}
        <li>
          <button 
            class="dropdown-item {$currentLanguage === lang ? 'active' : ''}" 
            on:click={() => selectLanguage(lang)}
          >
            <span class="me-2">{getLanguageFlag(lang)}</span>
            {getLanguageName(lang)}
          </button>
        </li>
      {/each}
    </ul>
  {/if}
</div>
