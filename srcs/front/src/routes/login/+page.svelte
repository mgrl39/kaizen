<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import Navbar from '$lib/components/Navbar.svelte';
  
  // Importar estilos globales necesarios
  import '$lib/styles/index.css';
  import 'bootstrap-icons/font/bootstrap-icons.css';
  
  let identifier = '';
  let password = '';
  let loading = false;
  let showError = false;
  let errorMessage = '';
  
  function setLoading(value: boolean) {
    loading = value;
  }
  
  function setShowError(value: boolean) {
    showError = value;
  }
  
  function setErrorMessage(value: string) {
    errorMessage = value;
  }
  
  // Función de manejo de login integrada directamente en el componente
  async function handleSubmit() {
    setLoading(true);
    setShowError(false);

    try {
      const response = await fetch(`${API_URL}/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ identifier, password })
      });

      const data = await response.json();

      if (response.ok && data.success) {
        localStorage.setItem('token', data.token);
        window.location.href = '/';
      } else {
        setErrorMessage(data.message || 'Credenciales incorrectas');
        setShowError(true);
      }
    } catch (error) {
      setErrorMessage('Error de conexión con el servidor');
      setShowError(true);
    } finally {
      setLoading(false);
    }
  }
  
  function submitForm() {
    handleSubmit();
  }
  
  // Verificar si ya hay sesión
  onMount(() => {
    const token = localStorage.getItem('token');
    if (token) {
      window.location.href = '/';
    }
  });
</script>

<!-- Navbar -->
<Navbar />

<!-- Main content with proper centering -->
<div class="flex flex-col items-center justify-center px-4 bg-dark" style="height: calc(100vh - 64px);">
  <div class="w-full max-w-md">
    <!-- Logo y título -->
    <div class="text-center mb-4">
      <h1 class="text-3xl font-bold text-white mb-2">{$t('login')}</h1>
      <p class="text-gray-400">{$t('welcomeBack')}</p>
    </div>
    
    <!-- Tarjeta de login -->
    <div class="bg-card border border-white/10 rounded-lg shadow-lg p-5">
      {#if showError}
        <div class="bg-red-900/20 border border-red-500/30 text-red-200 rounded-md p-3 mb-4">
          <p>{errorMessage}</p>
        </div>
      {/if}
      
      <form on:submit|preventDefault={submitForm} class="space-y-4">
        <!-- Inputs side by side -->
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <label for="identifier" class="block text-sm font-medium text-gray-300 mb-1">
              {$t('emailOrUsername')}
            </label>
            <input
              type="text"
              id="identifier"
              bind:value={identifier}
              required
              class="w-full bg-dark border border-white/10 rounded-md py-2 px-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
              placeholder={$t('emailPlaceholder')}
            />
          </div>
          
          <div class="flex-1">
            <label for="password" class="block text-sm font-medium text-gray-300 mb-1">
              {$t('password')}
            </label>
            <input
              type="password"
              id="password"
              bind:value={password}
              required
              class="w-full bg-dark border border-white/10 rounded-md py-2 px-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
              placeholder="••••••••"
            />
          </div>
        </div>
        
        <button
          type="submit"
          disabled={loading}
          class="w-full bg-purple-700 hover:bg-purple-600 text-white py-2 px-4 rounded-md transition-colors"
        >
          {#if loading}
            <i class="bi bi-arrow-repeat spin-animation mr-2"></i>
            {$t('loggingIn')}
          {:else}
            {$t('login')}
          {/if}
        </button>
      </form>
      
      <div class="mt-4 text-center">
        <p class="text-sm text-gray-400">
          {$t('noAccount')}
          <a href="/register" class="text-purple-400 hover:text-purple-300 font-medium">
            {$t('register')}
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<style>
  /* Estilos específicos para esta página */
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
  
  /* Animación para el icono de carga */
  .spin-animation {
    display: inline-block;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
