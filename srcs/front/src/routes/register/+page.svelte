<script lang="ts">
    import { goto } from '$app/navigation';
    import { t, language, languages } from '$lib/i18n';
    import { API_URL } from '$lib/config';
    import { onMount } from 'svelte';

    // Form data
    let username: string = '';
    let name: string = '';
    let email: string = '';
    let password: string = '';
    let passwordConfirmation: string = '';
    let birthdate: string = '';
    let loading = false;
    let showError = false;
    let showSuccess = false;

    // Error handling
    type ErrorsType = {
        username?: string;
        name?: string;
        email?: string;
        password?: string;
        passwordConfirmation?: string;
        birthdate?: string;
        [key: string]: string | undefined;
    };
    
    let errors: ErrorsType = {};

    // Client-side validation
    function validateForm() {
        errors = {};
        // Username validation
        if (!username) return false;
        if (username.length < 3) return false;
        // Name validation
        if (!name) return false;
        // Email validation
        if (!email) return false;
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return false;
        // Password validation
        if (!password) return false;
        if (password.length < 8) return false;
        // Password confirmation
        if (password !== passwordConfirmation) return false;
        // Birthdate validation is optional now
        if (birthdate) {
            const today : Date = new Date();
            const selectedDate : Date = new Date(birthdate);
            if (selectedDate >= today) return false;
        }
        return true;
    }

    async function handleSubmit() {
        if (!validateForm()) {
            showError = true;
            return;
        }
        
        loading = true;
        showError = false;
        showSuccess = false;
        
        try {
            const userData = {
                username,
                name,
                email,
                password,
                password_confirmation: passwordConfirmation,
                birthdate: birthdate || null
            };
            
            const response = await fetch(`${API_URL}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(userData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showSuccess = true;
                setTimeout(() => {
                    goto('/login');
                }, 2000);
            } else {
                console.error('Error de registro:', data);
                showError = true;
            }
        } catch (error) {
            console.error('Error de conexión:', error);
            showError = true;
        } finally {
            loading = false;
        }
    }

    // Verificar si ya hay sesión
    onMount(() => {
        const token = localStorage.getItem('token');
        if (token) goto('/');
    });
</script>

<div class="position-fixed top-0 start-0 p-3">
  <button 
    class="btn btn-link text-decoration-none" 
    on:click={() => goto('/')}
  >
    <i class="bi bi-arrow-left fs-4"></i>
  </button>
</div>

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
      <div class="col-md-8 col-lg-6">
        <!-- Encabezado -->
        <div class="text-center mb-4">
          <h1 class="display-6 fw-bold">{$t('createAccount')}</h1>
          <p class="text-muted">{$t('joinKaizenCinema')}</p>
        </div>
        
        <!-- Tarjeta de registro -->
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            {#if showError}
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {$t('registerError')}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" on:click={() => showError = false}></button>
              </div>
            {/if}
            
            {#if showSuccess}
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {$t('registerSuccess')}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" on:click={() => showSuccess = false}></button>
              </div>
            {/if}
            
            <form on:submit|preventDefault={handleSubmit}>
              <div class="row g-3">
                <!-- Nombre completo -->
                <div class="col-12">
                  <label for="name" class="form-label">
                    {$t('fullName')} <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-person"></i>
                    </span>
                    <input
                      type="text"
                      id="name"
                      bind:value={name}
                      class="form-control {errors.name ? 'is-invalid' : ''}"
                      placeholder={$t('fullNamePlaceholder')}
                    />
                    {#if errors.name}
                      <div class="invalid-feedback">{errors.name}</div>
                    {/if}
                  </div>
                </div>
                
                <!-- Nombre de usuario -->
                <div class="col-md-6">
                  <label for="username" class="form-label">
                    {$t('username')} <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-at"></i>
                    </span>
                    <input
                      type="text"
                      id="username"
                      bind:value={username}
                      class="form-control {errors.username ? 'is-invalid' : ''}"
                      placeholder={$t('usernamePlaceholder')}
                    />
                    {#if errors.username}
                      <div class="invalid-feedback">{errors.username}</div>
                    {/if}
                  </div>
                </div>
                
                <!-- Email -->
                <div class="col-md-6">
                  <label for="email" class="form-label">
                    {$t('email')} <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-envelope"></i>
                    </span>
                    <input
                      type="email"
                      id="email"
                      bind:value={email}
                      class="form-control {errors.email ? 'is-invalid' : ''}"
                      placeholder={$t('emailPlaceholder')}
                    />
                    {#if errors.email}
                      <div class="invalid-feedback">{errors.email}</div>
                    {/if}
                  </div>
                </div>
                
                <!-- Contraseña -->
                <div class="col-md-6">
                  <label for="password" class="form-label">
                    {$t('password')} <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-lock"></i>
                    </span>
                    <input
                      type="password"
                      id="password"
                      bind:value={password}
                      class="form-control {errors.password ? 'is-invalid' : ''}"
                      placeholder={$t('passwordPlaceholder')}
                    />
                    {#if errors.password}
                      <div class="invalid-feedback">{errors.password}</div>
                    {/if}
                  </div>
                </div>
                
                <!-- Confirmar contraseña -->
                <div class="col-md-6">
                  <label for="passwordConfirmation" class="form-label">
                    {$t('confirmPassword')} <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-lock-fill"></i>
                    </span>
                    <input
                      type="password"
                      id="passwordConfirmation"
                      bind:value={passwordConfirmation}
                      class="form-control {errors.passwordConfirmation ? 'is-invalid' : ''}"
                      placeholder={$t('confirmPasswordPlaceholder')}
                    />
                    {#if errors.passwordConfirmation}
                      <div class="invalid-feedback">{errors.passwordConfirmation}</div>
                    {/if}
                  </div>
                </div>
                
                <!-- Botón de registro -->
                <div class="col-12 mt-4">
                  <button
                    type="submit"
                    disabled={loading}
                    class="btn btn-primary w-100 py-2"
                  >
                    {#if loading}
                      <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                      {$t('registering')}
                    {:else}
                      <i class="bi bi-person-plus me-2"></i>
                      {$t('register')}
                    {/if}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Enlace a login -->
        <div class="text-center mt-4">
          <p>
            {$t('alreadyHaveAccount')}
            <a href="/login" class="text-decoration-none fw-semibold">
              {$t('login')}
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Utilizamos las variables globales para consistencia */
  :global([data-bs-theme="dark"]) .info-card {
    background-color: var(--app-card-bg);
  }
</style>