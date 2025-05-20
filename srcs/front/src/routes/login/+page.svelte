<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { theme } from '$lib/theme';
  import Navbar from '$lib/components/Navbar.svelte';
  import { goto } from '$app/navigation';
  
  // Importar estilos globales necesarios
  import '$lib/styles/index.css';
  import 'bootstrap-icons/font/bootstrap-icons.css';
  
  // Estado del formulario
  let email = '';
  let password = '';
  let rememberMe = false;
  let isSubmitting = false;
  let errorMessage = '';
  let showPassword = false;
  
  // Validación básica
  $: isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  $: isPasswordValid = password.length >= 6;
  $: isFormValid = isEmailValid && isPasswordValid;
  
  async function handleSubmit() {
    if (!isFormValid || isSubmitting) return;
    
    isSubmitting = true;
    errorMessage = '';
    
    try {
      // Simulación de login (reemplazar con tu lógica real)
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // Redirigir al usuario después del login exitoso
      goto('/');
    } catch (error) {
      console.error('Error de login:', error);
      errorMessage = 'Credenciales inválidas. Por favor intenta de nuevo.';
    } finally {
      isSubmitting = false;
    }
  }
  
  function togglePasswordVisibility() {
    showPassword = !showPassword;
  }
  
  // Verificar si ya hay sesión y cargar usuario recordado
  onMount(() => {
    const token = localStorage.getItem('token');
    if (token) {
      goto('/');
    }
    
    // Cargar usuario recordado si existe
    const rememberedUser = localStorage.getItem('rememberedUser');
    if (rememberedUser) {
      email = rememberedUser;
      rememberMe = true;
    }
    
    // Inicializar tooltips de Bootstrap si es necesario
    if (typeof bootstrap !== 'undefined') {
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }
  });
</script>

<!-- Navbar -->
<Navbar />

<!-- Main content with proper centering -->
<div class="container py-5 my-5" data-bs-theme={$theme}>
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5 col-xl-4">
      <!-- Encabezado -->
      <div class="text-center mb-4">
        <h1 class="display-6 fw-bold">{$t('login')}</h1>
        <p class="text-muted">{$t('welcomeBack', 'Bienvenido de nuevo')}</p>
      </div>
      
      <!-- Tarjeta de login -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          {#if errorMessage}
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {errorMessage}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          {/if}
          
          <form on:submit|preventDefault={handleSubmit}>
            <div class="mb-3">
              <label for="email" class="form-label">{$t('emailAddress')}</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  bind:value={email}
                  class:is-invalid={email && !isEmailValid}
                  placeholder="tu@email.com" 
                  required
                />
                {#if email && !isEmailValid}
                  <div class="invalid-feedback">
                    {$t('invalidEmail')}
                  </div>
                {/if}
              </div>
            </div>
            
            <div class="mb-4">
              <label for="password" class="form-label">{$t('password')}</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input 
                  type={showPassword ? 'text' : 'password'} 
                  class="form-control" 
                  id="password" 
                  bind:value={password}
                  class:is-invalid={password && !isPasswordValid}
                  placeholder={$t('password')} 
                  required
                />
                <button 
                  class="btn btn-outline-secondary" 
                  type="button"
                  on:click={togglePasswordVisibility}
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title={showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'}
                  aria-label="Mostrar contraseña"
                >
                  <i class="bi bi-{showPassword ? 'eye-slash' : 'eye'}"></i>
                </button>
                {#if password && !isPasswordValid}
                  <div class="invalid-feedback">
                    {$t('passwordTooShort')}
                  </div>
                {/if}
              </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="form-check">
                <input 
                  type="checkbox" 
                  class="form-check-input" 
                  id="rememberMe" 
                  bind:checked={rememberMe}
                />
                <label class="form-check-label" for="rememberMe">{$t('rememberMe')}</label>
              </div>
              <a href="/reset-password" class="text-decoration-none">{$t('forgotPassword')}</a>
            </div>
            
            <div class="d-grid">
              <button 
                type="submit" 
                class="btn btn-primary btn-lg" 
                disabled={!isFormValid || isSubmitting}
              >
                {#if isSubmitting}
                  <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  {$t('loading')}
                {:else}
                  {$t('signIn')}
                {/if}
              </button>
            </div>
          </form>
          
          <!-- Enlace a registro -->
          <div class="text-center mt-4">
            <p>
              {$t('dontHaveAccount')} <a href="/register" class="text-decoration-none fw-semibold">{$t('signUpNow')}</a>
            </p>
          </div>
          
          <!-- Información de seguridad -->
          <div class="text-center mt-4">
            <small class="text-muted d-flex align-items-center justify-content-center">
              <i class="bi bi-shield-lock me-2"></i>
              {$t('secureLogin', 'Conexión segura')}
            </small>
          </div>
        </div>
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
