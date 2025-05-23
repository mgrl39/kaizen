<script lang="ts">
  import { t } from '$lib/i18n';
  import { page } from '$app/stores';
  import { fly } from 'svelte/transition';

  let isMobileMenuOpen = false;

  const navigation = [
    { name: 'dashboard', href: '/admin', icon: 'house-door' },
    { name: 'movies', href: '/admin/movies', icon: 'film' },
    { name: 'cinemas', href: '/admin/cinemas', icon: 'building' },
    { name: 'users', href: '/admin/users', icon: 'people' },
    { name: 'reports', href: '/admin/reports', icon: 'bar-chart' },
    { name: 'settings', href: '/admin/settings', icon: 'gear' },
  ];

  function getIcon(iconName: string): string {
    return `<i class="bi bi-${iconName} me-2"></i>`;
  }
</script>

{#if isMobileMenuOpen}
  <div class="fixed inset-0 flex z-40 md:hidden" role="dialog" aria-modal="true" transition:fly={{ x: -250, duration: 300 }}>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75" aria-hidden="true" on:click={() => isMobileMenuOpen = false}></div>

    <div class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-gray-800">
      <div class="absolute top-0 right-0 -mr-12 pt-2">
        <button
          type="button"
          class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
          on:click={() => isMobileMenuOpen = false}
        >
          <span class="sr-only">{$t('closeSidebar')}</span>
          <i class="bi bi-x text-white text-lg"></i>
        </button>
      </div>

      <div class="flex-shrink-0 flex items-center px-4">
        <span class="text-lg font-bold text-white">{$t('cinemaAdmin')}</span>
      </div>

      <div class="mt-5 flex-1 h-0 overflow-y-auto">
        <nav class="px-2 space-y-1">
          {#each navigation as item}
            <a 
              href={item.href} 
              class={`group flex items-center px-2 py-2 text-base font-medium rounded-md ${$page.url.pathname === item.href ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'}`}
            >
              {@html getIcon(item.icon)}
              {$t(item.name)}
            </a>
          {/each}
        </nav>
      </div>
    </div>

    <div class="flex-shrink-0 w-14" aria-hidden="true"></div>
  </div>
{/if}

<div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
  <div class="flex-1 flex flex-col min-h-0 bg-gray-800">
    <div class="flex items-center h-16 flex-shrink-0 px-4 bg-gray-900">
      <span class="text-lg font-bold text-white">{$t('cinemaAdmin')}</span>
    </div>

    <div class="flex-1 flex flex-col overflow-y-auto">
      <nav class="flex-1 px-2 py-4 space-y-1">
        {#each navigation as item}
          <a 
            href={item.href} 
            class={`group flex items-center px-2 py-2 text-sm font-medium rounded-md ${$page.url.pathname === item.href ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'}`}
          >
            {@html getIcon(item.icon)}
            {$t(item.name)}
          </a>
        {/each}
      </nav>
    </div>
  </div>
</div>

<div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow md:hidden">
  <button
    type="button"
    class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary md:hidden"
    on:click={() => isMobileMenuOpen = true}
  >
    <span class="sr-only">{$t('openSidebar')}</span>
    <i class="bi bi-list text-lg"></i>
  </button>
</div>
