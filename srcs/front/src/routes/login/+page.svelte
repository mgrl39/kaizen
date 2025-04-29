<script lang="ts">
  // Importa solo la función, las variables explícitas aquí
  import { handleSubmit } from './login.ts';

  let identifier: string = '';
  let password: string = '';
  let loading: boolean = false;
  let errorMessage: string = '';
  let showError: boolean = false;
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

          <form on:submit|preventDefault={() => handleSubmit({ identifier, password, setLoading: v => loading = v, setShowError: v => showError = v, setErrorMessage: v => errorMessage = v })}>
            <div class="mb-3">
              <input type="text" 
                     bind:value={identifier}
                     class="form-control" 
                     placeholder="Email o nombre de usuario" 
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