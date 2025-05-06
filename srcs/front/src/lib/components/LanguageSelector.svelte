<script lang="ts">
  import { onMount } from 'svelte';
  import { currentLanguage, languages } from '$lib/i18n';
  import { browser } from '$app/environment';
  
  let isOpen = false;
  let selectorRef; // Referencia al elemento contenedor
  
  function toggleMenu() {
    isOpen = !isOpen;
  }
  
  function selectLanguage(lang: string) {
    $currentLanguage = lang;
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
<div class="relative" bind:this={selectorRef}>
  <button 
    class="flex items-center text-white hover:bg-white/10 rounded-md transition-colors px-2 py-1.5"
    on:click|stopPropagation={toggleMenu}
    aria-label="Seleccionar idioma"
    aria-haspopup="true"
    aria-expanded={isOpen}
  >
    <span class="text-lg">{getLanguageFlag($currentLanguage)}</span>
    <span class="font-medium ml-1 text-sm">{$currentLanguage.toUpperCase()}</span>
    <i class="bi bi-chevron-down text-xs ml-1 transition-transform duration-200 {isOpen ? 'rotate-180' : ''}"></i>
  </button>
  
  {#if isOpen}
    <div 
      class="absolute left-0 md:left-auto md:right-0 top-full mt-1 w-36 bg-card border border-white/10 rounded-md shadow-lg overflow-hidden z-50"
    >
      {#each languages as lang}
        <button 
          class="flex items-center w-full px-3 py-2 text-white hover:bg-purple-900/20 text-left {$currentLanguage === lang ? 'bg-purple-900/30 font-medium' : ''}"
          on:click={() => selectLanguage(lang)}
        >
          <span class="text-lg mr-2">{getLanguageFlag(lang)}</span>
          <span class="text-sm">{getLanguageName(lang)}</span>
        </button>
      {/each}
    </div>
  {/if}
</div>

<style>
  .language-selector {
    position: relative;
  }
  
  .lang-button {
    display: flex;
    align-items: center;
    background: transparent;
    border: none;
    color: white;
    padding: 0.5rem;
    cursor: pointer;
    border-radius: 4px;
    font-size: 0.875rem;
  }
  
  .lang-button:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  .flag {
    margin-right: 0.25rem;
    font-size: 1.25rem;
  }
  
  .current-lang {
    font-weight: 500;
  }
  
  .lang-dropdown {
    position: absolute;
    top: calc(100% + 5px);
    right: 0;
    min-width: 120px;
    background-color: #212529;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    overflow: hidden;
  }
  
  .lang-option {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 0.5rem 1rem;
    background: transparent;
    border: none;
    color: white;
    text-align: left;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .lang-option:hover {
    background-color: rgba(109, 40, 217, 0.2);
  }
  
  .lang-option.active {
    background-color: rgba(109, 40, 217, 0.4);
    font-weight: 500;
  }
</style> 