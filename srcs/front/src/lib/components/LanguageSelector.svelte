<script lang="ts">
  import { currentLanguage, languages } from '$lib/i18n';
  
  let isOpen = false;
  
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
</script>

<div class="language-selector">
  <button 
    class="lang-button" 
    on:click={toggleMenu} 
    aria-label="Seleccionar idioma"
    aria-haspopup="true"
    aria-expanded={isOpen}
  >
    <span class="flag">{getLanguageFlag($currentLanguage)}</span>
    <span class="current-lang">{$currentLanguage.toUpperCase()}</span>
  </button>
  
  {#if isOpen}
    <div class="lang-dropdown">
      {#each languages as lang}
        <button 
          class="lang-option" 
          class:active={$currentLanguage === lang}
          on:click={() => selectLanguage(lang)}
        >
          <span class="flag">{getLanguageFlag(lang)}</span>
          <span>{getLanguageName(lang)}</span>
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