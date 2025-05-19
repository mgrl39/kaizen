<script lang="ts">
  import { onMount } from 'svelte';
  import { currentLanguage, languages } from '$lib/i18n';
  import { browser } from '$app/environment';
  import { theme } from '$lib/theme';
  
  // Props para controlar el estado desde el componente padre
  export let isOpen = false;
  export let toggleMenu = () => { isOpen = !isOpen; };
  
  let selectorRef: HTMLElement; // Referencia al elemento contenedor con tipo explícito
  
  function selectLanguage(lang: string) {
    currentLanguage.set(lang);
    isOpen = false;
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
  
  // Forzar español como idioma por defecto si el valor guardado no es válido
  onMount(() => {
    if (browser && !languages.includes($currentLanguage)) {
      console.warn('Idioma no válido en localStorage, usando español como predeterminado');
      $currentLanguage = 'es';
    }
  });
  
  // Cerrar el menú al hacer clic fuera
  function handleClickOutside(event: MouseEvent) {
    if (isOpen && selectorRef && !selectorRef.contains(event.target as Node)) {
      isOpen = false;
    }
  }
  
  onMount(() => {
    document.addEventListener('click', handleClickOutside);
    return () => {
      document.removeEventListener('click', handleClickOutside);
    };
  });
</script>

<!-- Contenedor con referencia para detectar clics fuera -->
<div class="dropdown" id="language-dropdown">
  <button 
    class="btn btn-sm {$theme === 'dark' ? 'btn-outline-light' : 'btn-outline-dark'}" 
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

<style>
</style>