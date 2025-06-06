<!-- Importar estilos primero -->
<style>
  /* Critical styles */
  :global(body) {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    background: var(--app-bg);
  }

  /* Estilos específicos y adaptados al tema global */
  :global([data-bs-theme="dark"]) .card {
    background-color: var(--app-card-bg);
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  :global(.dropdown-menu) {
    border-color: var(--app-border);
  }

  :global(.dropdown-item.active),
  :global(.dropdown-item:active) {
    background-color: var(--app-primary);
    color: white;
  }

  :global(.dropdown-item:hover) {
    background-color: var(--app-primary-hover);
    color: white;
  }
</style>

<script lang="ts">
  // Importar estilos globales primero
  import '$lib/styles/index.css';
  import 'bootstrap-icons/font/bootstrap-icons.css';
  
  import { t, language, languages } from '$lib/i18n';
  import { onMount } from 'svelte';
  import { API_URL } from '$lib/config';
  import { goto } from '$app/navigation';
  import BackButton from '$lib/components/BackButton.svelte';
  
  // Estado del formulario
  let email = '';
  let password = '';
  let isSubmitting = false;
  let showError = false;
  let showPassword = false;
  let errorMessage = '';
  
  // Validación básica
  $: isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  $: isPasswordValid = password.length >= 6;
  $: isFormValid = isEmailValid && isPasswordValid;
  
  async function handleSubmit() {
    if (isSubmitting) return;
    
    isSubmitting = true;
    showError = false;
    errorMessage = '';
    
    try {
      console.log('Intentando login con:', email);
      const response = await fetch(`${API_URL}/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          identifier: email,
          password: password
        })
      });

      const data = await response.json();
      console.log('Respuesta del servidor:', data);

      if (response.ok && data.success && data.token) {
        // Guardar el token
        localStorage.setItem('token', data.token);
        
        // También podemos guardar información del usuario si es necesario
        if (data.user) {
          localStorage.setItem('user', JSON.stringify(data.user));
        }
        
        // Verificar si hay una URL de redirección guardada
        const redirectUrl = sessionStorage.getItem('redirectAfterLogin');
        if (redirectUrl) {
          sessionStorage.removeItem('redirectAfterLogin');
          goto(redirectUrl);
          return;
        }
        
        // Si no hay redirección, ir al home
        goto('/');
      } else {
        console.error('Error de login:', data);
        errorMessage = data.message || 'Credenciales inválidas';
        showError = true;
      }
    } catch (error) {
      console.error('Error de conexión:', error);
      errorMessage = 'Error de conexión. Por favor, inténtalo de nuevo.';
      showError = true;
    } finally {
      isSubmitting = false;
    }
  }
  
  function togglePasswordVisibility() {
    showPassword = !showPassword;
  }
  
  // Verificar si ya hay sesión y si hay redirección pendiente
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
          // Si el token es válido, verificar si hay redirección pendiente
          const savedBooking = sessionStorage.getItem('bookingRedirect');
          if (savedBooking) {
            try {
              const { returnUrl } = JSON.parse(savedBooking);
              if (returnUrl) {
                goto(returnUrl);
                return;
              }
            } catch (e) {
              console.error('Error al procesar redirección:', e);
            }
          }
          // Si no hay redirección, ir al home
          goto('/');
        }
      })
      .catch(error => {
        console.error('Error al verificar sesión:', error);
        localStorage.removeItem('token');
      });
    }
  });
</script>

<BackButton />

<!-- Language selector -->
<div class="position-fixed top-0 end-0 p-3">
  <div class="dropdown">
    <button class="btn btn-link text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      {languages[$language].flag}
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      {#each Object.entries(languages) as [code, lang]}
        <li>
          <button 
            class="dropdown-item border-0" 
            class:active={$language === code}
            on:click={() => language.set(code)}
          >
            {lang.flag} {lang.name}
          </button>
        </li>
      {/each}
    </ul>
  </div>
</div>

<!-- Main content with proper centering -->
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="margin-top: -5rem;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5 col-xl-4">
        <!-- Encabezado -->
        <div class="text-center mb-4">
          <h1 class="display-6 fw-bold">{$t('login')}</h1>
          <p class="text-muted">{$t('welcomeBack')}</p>
        </div>
        
        <!-- Tarjeta de login -->
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            {#if showError}
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {errorMessage || $t('loginError')}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" on:click={() => showError = false}></button>
              </div>
            {/if}
            
            <form on:submit|preventDefault={handleSubmit}>
              <div class="mb-3">
                <label for="email" class="form-label">
                  {$t('email')} <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    bind:value={email}
                    class:is-invalid={email && !isEmailValid}
                    class:is-valid={email && isEmailValid}
                    placeholder={$t('emailPlaceholder')}
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
                <label for="password" class="form-label">
                  {$t('password')} <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input 
                    type={showPassword ? 'text' : 'password'} 
                    class="form-control" 
                    id="password" 
                    bind:value={password}
                    class:is-invalid={password && !isPasswordValid}
                    class:is-valid={password && isPasswordValid}
                    placeholder={$t('passwordPlaceholder')}
                    required
                  />
                  <button 
                    class="btn btn-outline-secondary" 
                    type="button"
                    on:click={togglePasswordVisibility}
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title={showPassword ? $t('hidePassword') : $t('showPassword')}
                    aria-label={showPassword ? $t('hidePassword') : $t('showPassword')}
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
              
              <div class="d-grid mt-4">
                <button 
                  type="submit" 
                  class="btn btn-lg btn-primary" 
                  disabled={isSubmitting || !isFormValid}
                >
                  {#if isSubmitting}
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    {$t('loading')}
                  {:else}
                    <i class="bi bi-box-arrow-in-right me-2"></i>
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
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
