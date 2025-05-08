<script lang="ts">
  import { handleSubmit } from './login';
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
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
  
  function submitForm() {
    handleSubmit({
      identifier,
      password,
      setLoading,
      setShowError,
      setErrorMessage
    });
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
<div class="flex flex-col items-center justify-center px-4 py-10 bg-dark" style="min-height: calc(100vh - 64px);">
  <div class="w-full max-w-md">
    <!-- Logo y título -->
    <div class="text-center mb-6">
      <h1 class="text-3xl font-bold text-white mb-2">Iniciar Sesión</h1>
      <p class="text-gray-400">Bienvenido de nuevo a Kaizen Cinema</p>
    </div>
    
    <!-- Tarjeta de login -->
    <div class="bg-card border border-white/10 rounded-lg shadow-lg p-6">
      {#if showError}
        <div class="bg-red-900/20 border border-red-500/30 text-red-200 rounded-md p-3 mb-4">
          <p>{errorMessage}</p>
        </div>
      {/if}
      
      <form on:submit|preventDefault={submitForm} class="space-y-4">
        <div>
          <label for="identifier" class="block text-sm font-medium text-gray-300 mb-1">
            Email o nombre de usuario
          </label>
          <input
            type="text"
            id="identifier"
            bind:value={identifier}
            required
            class="w-full bg-dark border border-white/10 rounded-md py-2 px-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
            placeholder="usuario@email.com"
          />
        </div>
        
        <div>
          <label for="password" class="block text-sm font-medium text-gray-300 mb-1">
            Contraseña
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
        
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              type="checkbox"
              id="remember-me"
              class="h-4 w-4 rounded border-gray-600 bg-dark text-purple-600 focus:ring-purple-500"
            />
            <label for="remember-me" class="ml-2 block text-sm text-gray-300">
              Recordarme
            </label>
          </div>
          
          <a href="/forgot-password" class="text-sm text-purple-400 hover:text-purple-300">
            ¿Olvidaste tu contraseña?
          </a>
        </div>
        
        <button
          type="submit"
          disabled={loading}
          class="w-full bg-purple-700 hover:bg-purple-600 text-white py-2 px-4 rounded-md transition-colors"
        >
          {#if loading}
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Iniciando sesión...
          {:else}
            Iniciar sesión
          {/if}
        </button>
      </form>
      
      <div class="mt-4 text-center">
        <p class="text-sm text-gray-400">
          ¿No tienes una cuenta?
          <a href="/register" class="text-purple-400 hover:text-purple-300 font-medium">
            Regístrate
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
</style>
