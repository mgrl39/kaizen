<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { theme } from '$lib/theme';
  import Navbar from '$lib/components/Navbar.svelte';
  
  // Importar estilos globales necesarios
  import '$lib/styles/index.css';
  import 'bootstrap-icons/font/bootstrap-icons.css';
  
  // Estado del formulario
  let identifier = '';
  let password = '';
  let loading = false;
  let showError = false;
  let errorMessage = '';
  let rememberMe = false;
  
  // Validación de formulario
  let errors = {
    identifier: '',
    password: ''
  };
  
  function setLoading(value: boolean) {
    loading = value;
  }
  
  function setShowError(value: boolean) {
    showError = value;
  }
  
  function setErrorMessage(value: string) {
    errorMessage = value;
  }
  
  // Función para validar el formulario
  function validateForm() {
    let isValid = true;
    errors = {
      identifier: '',
      password: ''
    };
    
    // Validar identificador (email o nombre de usuario)
    if (!identifier) {
      errors.identifier = 'Este campo es obligatorio';
      isValid = false;
    }
    
    // Validar contraseña
    if (!password) {
      errors.password = 'La contraseña es obligatoria';
      isValid = false;
    }
    
    return isValid;
  }
  
  // Función de manejo de login
  async function handleSubmit() {
    if (!validateForm()) {
      return;
    }
    
    loading = true;
    showError = false;

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
        
        // Si se seleccionó "recordarme", guardar el identificador
        if (rememberMe) {
          localStorage.setItem('rememberedUser', identifier);
        } else {
          localStorage.removeItem('rememberedUser');
        }
        
        window.location.href = '/';
      } else {
        errorMessage = data.message || 'Credenciales incorrectas';
        showError = true;
      }
    } catch (error) {
      errorMessage = 'Error de conexión con el servidor';
      showError = true;
    } finally {
      loading = false;
    }
  }
  
  // Verificar si ya hay sesión y cargar usuario recordado
  onMount(() => {
    const token = localStorage.getItem('token');
    if (token) {
      window.location.href = '/';
    }
    
    // Cargar usuario recordado si existe
    const rememberedUser = localStorage.getItem('rememberedUser');
    if (rememberedUser) {
      identifier = rememberedUser;
      rememberMe = true;
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
        <h1 class="display-6 fw-bold">{$t('login', 'Iniciar sesión')}</h1>
        <p class="text-muted">{$t('welcomeBack', 'Bienvenido de nuevo')}</p>
      </div>
      
      <!-- Tarjeta de login -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          {#if showError}
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              {errorMessage}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" on:click={() => showError = false}></button>
            </div>
          {/if}
          
          <form on:submit|preventDefault={handleSubmit}>
            <!-- Email o nombre de usuario -->
            <div class="mb-3">
              <label for="identifier" class="form-label">
                {$t('emailOrUsername', 'Email o nombre de usuario')}
              </label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-person"></i>
                </span>
                <input
                  type="text"
                  id="identifier"
                  bind:value={identifier}
                  class="form-control {errors.identifier ? 'is-invalid' : ''}"
                  placeholder={$t('emailOrUsernamePlaceholder', 'Ingresa tu email o nombre de usuario')}
                  autocomplete="username"
                />
                {#if errors.identifier}
                  <div class="invalid-feedback">{errors.identifier}</div>
                {/if}
              </div>
            </div>
            
            <!-- Contraseña -->
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label for="password" class="form-label">
                  {$t('password', 'Contraseña')}
                </label>
                <a href="/forgot-password" class="text-decoration-none small">
                  {$t('forgotPassword', '¿Olvidaste tu contraseña?')}
                </a>
              </div>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-lock"></i>
                </span>
                <input
                  type="password"
                  id="password"
                  bind:value={password}
                  class="form-control {errors.password ? 'is-invalid' : ''}"
                  placeholder="••••••••"
                  autocomplete="current-password"
                />
                {#if errors.password}
                  <div class="invalid-feedback">{errors.password}</div>
                {/if}
              </div>
            </div>
            
            <!-- Recordarme -->
            <div class="mb-4">
              <div class="form-check">
                <input
                  type="checkbox"
                  id="rememberMe"
                  bind:checked={rememberMe}
                  class="form-check-input"
                />
                <label class="form-check-label" for="rememberMe">
                  {$t('rememberMe', 'Recordarme')}
                </label>
              </div>
            </div>
            
            <!-- Botón de login -->
            <div class="d-grid">
              <button
                type="submit"
                disabled={loading}
                class="btn btn-primary py-2"
              >
                {#if loading}
                  <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  {$t('loggingIn', 'Iniciando sesión...')}
                {:else}
                  <i class="bi bi-box-arrow-in-right me-2"></i>
                  {$t('login', 'Iniciar sesión')}
                {/if}
              </button>
            </div>
          </form>
          
          <!-- Enlace a registro -->
          <div class="text-center mt-4">
            <p>
              {$t('noAccount', '¿No tienes una cuenta?')}
              <a href="/register" class="text-decoration-none fw-semibold">
                {$t('register', 'Regístrate')}
              </a>
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
