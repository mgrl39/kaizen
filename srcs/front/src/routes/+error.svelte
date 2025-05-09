<script lang="ts">
  import { page } from '$app/stores';
  import { t } from '$lib/i18n';
  
  $: statusCode = $page.status || '404';
  $: errorMessage = $page.error?.message || $t('pageNotFound');
  
  $: errorTitle = statusCode === 404 ? $t('pageNotFoundTitle') : 
                 statusCode === 403 ? $t('accessDeniedTitle') :
                 statusCode === 500 ? $t('serverErrorTitle') :
                 $t('genericErrorTitle');
</script>

<div class="flex flex-col items-center justify-center px-4 bg-dark" style="height: calc(100vh - 64px);">
  <div class="w-full max-w-lg text-center">
    <div class="bg-card border border-white/10 rounded-lg shadow-lg p-8 mb-6">
      <div class="text-6xl font-bold text-purple-500 mb-4">
        {statusCode}
      </div>
      
      <h1 class="text-3xl font-bold text-white mb-4">
        {errorTitle}
      </h1>
      
      <p class="text-gray-300 mb-6">
        {errorMessage}
      </p>
      
      <a href="/" class="inline-block bg-purple-700 hover:bg-purple-600 text-white py-2 px-6 rounded-md transition-colors">
        <i class="bi bi-house-door mr-2"></i>
        {$t('backToHome')}
      </a>
    </div>
    
    <p class="text-sm text-gray-500">
      {$t('needHelp')} <a href="/contact" class="text-purple-400 hover:text-purple-300">
        {$t('contactSupport')}
      </a>
    </p>
  </div>
</div>

<style>
  :global(body) {
    background-color: #121212;
    color: #f8f9fa;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  :global(.bg-card) {
    background-color: #212529;
  }
  
  :global(.bg-dark) {
    background-color: #121212;
  }
</style> 