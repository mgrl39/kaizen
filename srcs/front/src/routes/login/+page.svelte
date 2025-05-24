<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
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
      const response = await fetch(`${API_URL}/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          identifier: email,
          password
        })
      });

      const data = await response.json();

      if (response.ok && data.success) {
        // Guardar el token
        localStorage.setItem('token', data.token);
        
        // Si "recordar usuario" está activado, guardar el email
        if (rememberMe) {
          localStorage.setItem('rememberedUser', email);
        } else {
          localStorage.removeItem('rememberedUser');
        }

        // Guardar estado de autenticación
        const authState = {
          isAuthenticated: true,
          userName: data.user.name || data.user.username || 'Usuario',
          loading: false
        };
        localStorage.setItem('authState', JSON.stringify(authState));

        // Redirigir a inicio
        goto('/');
      } else {
        errorMessage = data.message || 'Credenciales inválidas. Por favor intenta de nuevo.';
      }
    } catch (error) {
      console.error('Error de login:', error);
      errorMessage = 'Error al conectar con el servidor. Por favor intenta más tarde.';
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
      // Verificar si el token es válido
      fetch(`${API_URL}/profile`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const authState = {
            isAuthenticated: true,
            userName: data.user.name || data.user.username || 'Usuario',
            loading: false
          };
          localStorage.setItem('authState', JSON.stringify(authState));
          
          goto('/');
        } else {
          // Token inválido o expirado, limpiar estado
          localStorage.removeItem('token');
          localStorage.removeItem('authState');
        }
      })
      .catch(error => {
        console.error('Error al verificar sesión:', error);
        localStorage.removeItem('token');
        localStorage.removeItem('authState');
      });
    }
    
    // Cargar email recordado si existe
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
<div class="container py-5 my-5">
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
  /* Estilos específicos y adaptados al tema global */
  :global([data-bs-theme="dark"]) .card {
    background-color: var(--app-card-bg);
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
