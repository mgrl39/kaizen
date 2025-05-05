<script lang="ts">
  import ErrorPage from '$lib/components/ErrorPage.svelte';
  import { page } from '$app/stores';
  
  // Obtener información del error desde $page
  $: statusCode = $page.status || '404';
  $: errorMessage = $page.error?.message || 'Página no encontrada';
  
  // Determinar título basado en el código de estado
  $: errorTitle = statusCode === 404 ? 'Página no encontrada' : 
                 statusCode === 403 ? 'Acceso denegado' :
                 statusCode === 500 ? 'Error del servidor' :
                 'Ha ocurrido un error';
</script>

<ErrorPage 
  statusCode={statusCode.toString()}
  title={errorTitle}
  message={errorMessage}
/> 