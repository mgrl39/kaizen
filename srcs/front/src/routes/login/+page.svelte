<script lang="ts">
  import { onMount } from 'svelte';
  import type { LoginCredentials } from '$lib/types';
  
  let email: string = '';
  let password: string = '';
  let loading: boolean = false;
  let errorMessage: string = '';
  let showError: boolean = false;
  
  async function handleSubmit(): Promise<void> {
    loading = true;
    showError = false;
    
    try {
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      if (email === 'admin@example.com' && password === 'password') {
        window.location.href = '/';
      } else {
        errorMessage = 'Credenciales incorrectas';
        showError = true;
        loading = false;
      }
    } catch (error) {
      errorMessage = 'Error de conexión';
      showError = true;
      loading = false;
    }
  }
</script>

<div class="container auth-container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card auth-card">
        <div class="card-body p-4">
          <h4 class="text-center mb-4">Iniciar Sesión</h4>

          {#if showError}
            <div class="alert alert-danger mb-3">
              {errorMessage}
            </div>
          {/if}

          <form on:submit|preventDefault={handleSubmit}>
            <div class="mb-3">
              <input type="email" 
                     bind:value={email}
                     class="form-control" 
                     placeholder="Email" 
                     required
                     disabled={loading}>
            </div>
            
            <div class="mb-4">
              <input type="password" 
                     bind:value={password}
                     class="form-control" 
                     placeholder="Contraseña" 
                     required
                     disabled={loading}>
            </div>
            
            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-primary" disabled={loading}>
                {#if loading}
                  <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Procesando...
                {:else}
                  Iniciar Sesión
                {/if}
              </button>
            </div>
            
            <div class="text-center">
              <small>¿No tienes cuenta? <a href="/register">Regístrate</a></small>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .auth-container {
    padding-top: 2rem;
  }
  
  .auth-card {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }
</style> 

<div class="decorative-blob blob-1"></div>
<div class="decorative-blob blob-2"></div> 