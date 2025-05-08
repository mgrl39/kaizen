<script lang="ts">
  import ErrorPage from '$lib/components/ErrorPage.svelte';
  import { page } from '$app/stores';
  import { t } from '$lib/i18n';
  
  $: statusCode = $page.status || '404';
  $: errorMessage = $page.error?.message || $t('pageNotFound');
  
  $: errorTitle = statusCode === 404 ? $t('pageNotFoundTitle') : 
                 statusCode === 403 ? $t('accessDeniedTitle') :
                 statusCode === 500 ? $t('serverErrorTitle') :
                 $t('genericErrorTitle');
</script>

<ErrorPage 
  statusCode={statusCode.toString()}
  title={errorTitle}
  message={errorMessage}
/> 