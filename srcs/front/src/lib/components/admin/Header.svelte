<script lang="ts">
  import { t } from '$lib/i18n';
  import { page } from '$app/stores';

  let isProfileMenuOpen = false;
</script>

<header class="bg-white shadow-sm z-10 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-4 flex justify-between items-center">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">
        {$t($page.url.pathname.split('/').pop() || 'dashboard')}
      </h1>
    </div>

    <div class="flex items-center space-x-4">
      <!-- Botón de notificaciones -->
      <button
        type="button"
        class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
      >
        <span class="sr-only">{$t('viewNotifications')}</span>
        <i class="bi bi-bell text-xl"></i>
      </button>

      <!-- Menú de perfil -->
      <div class="relative">
        <button
          type="button"
          class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
          id="user-menu-button"
          aria-expanded={isProfileMenuOpen}
          aria-haspopup="true"
          on:click={() => isProfileMenuOpen = !isProfileMenuOpen}
        >
          <span class="sr-only">{$t('openUserMenu')}</span>
          <img
            class="h-8 w-8 rounded-full"
            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
            alt=""
          />
        </button>

        {#if isProfileMenuOpen}
          <div
            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="user-menu-button"
            tabindex="-1"
          >
            <a href="/admin/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
              {$t('yourProfile')}
            </a>
            <a href="/admin/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
              {$t('settings')}
            </a>
            <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
              {$t('signOut')}
            </a>
          </div>
        {/if}
      </div>
    </div>
  </div>
</header>
