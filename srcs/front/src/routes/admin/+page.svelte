<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  
  onMount(() => {
    // Redirigir a la página de dashboard
    goto('/admin/dashboard');
  });
</script>

<div class="d-flex justify-content-center align-items-center vh-100">
  <div class="text-center">
    <div class="spinner-border text-primary mb-3" role="status">
      <span class="visually-hidden">Cargando...</span>
    </div>
    <p>Redirigiendo al dashboard...</p>
  </div>
</div> 