<script lang="ts">
    import { goto } from '$app/navigation';
    import { t } from '$lib/i18n';
    import { API_URL } from '$lib/config';
    import { onMount } from 'svelte';
    import Navbar from '$lib/components/Navbar.svelte';

    // Form data
    let username: string = '';
    let name: string = '';
    let email: string = '';
    let password: string = '';
    let passwordConfirmation: string = '';
    let birthdate: string = '';
    let loading = false;
    let showError = false;
    let errorMessage = '';
    let showSuccess = false;
    let successMessage = '';
    let agreeTerms = false;

    // Error handling
    type ErrorsType = {
        username?: string;
        name?: string;
        email?: string;
        password?: string;
        passwordConfirmation?: string;
        birthdate?: string;
        terms?: string;
        [key: string]: string | undefined;
    };
    
    let errors: ErrorsType = {};

    // Client-side validation
    function validateForm() {
        errors = {};
        // Username validation
        if (!username) errors.username = $t('usernameRequired');
        else if (username.length < 3) errors.username = $t('usernameMinLength');
        // Name validation
        if (!name) errors.name = $t('nameRequired');
        // Email validation
        if (!email) errors.email = $t('emailRequired');
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.email = $t('emailInvalid');
        // Password validation
        if (!password) errors.password = $t('passwordRequired');
        else if (password.length < 8) errors.password = $t('passwordMinLength');
        // Password confirmation
        if (password !== passwordConfirmation) errors.passwordConfirmation = $t('passwordsDontMatch');
        // Birthdate validation is optional now
        if (birthdate) {
            const today : Date = new Date();
            const selectedDate : Date = new Date(birthdate);
            if (selectedDate >= today) errors.birthdate = $t('birthdateInvalid');
        }
        // Terms validation
        if (!agreeTerms) errors.terms = $t('agreeTermsRequired');
        return (Object.keys(errors).length === 0);
    }

    async function registerUser(userData: any) {
        try {
            const response = await fetch(`${API_URL}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(userData)
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    const formattedErrors: ErrorsType = {};
                    for (const key in data.errors) {
                        formattedErrors[key] = data.errors[key][0];
                    }
                    return {
                        success: false,
                        message: $t('formErrors'),
                        errors: formattedErrors
                    };
                }
                return {
                    success: false,
                    message: data.message || $t('registerError')
                };
            }
            if (data.token) localStorage.setItem('token', data.token);
            return {
                success: true,
                data
            };
        } catch (error) {
            return {
                success: false,
                message: $t('connectionError')
            };
        }
    }

    // Form submission
    async function handleSubmit() {
        if (!validateForm()) {
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
                birthdate: birthdate || null,
                agreeTerms
            };
            
            const response = await registerUser(userData);
            
            if (response.success) {
                showSuccess = true;
                successMessage = $t('registerSuccess');
                // Redirect to login page after 2 seconds
                setTimeout(() => {
                    goto('/login');
                }, 2000);
            } else {
                if (response.errors) errors = response.errors;
                else errorMessage = response.message || $t('registerError');
                showError = true;
            }
        } catch (error: any) {
            errorMessage = $t('unknownError');
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

<Navbar />

<div class="container py-5 my-3">
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
                            {errorMessage}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" on:click={() => showError = false}></button>
                        </div>
                    {/if}
                    
                    {#if showSuccess}
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {successMessage}
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
                                        placeholder="Ej. Juan Pérez"
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
                                        placeholder="Ej. juanperez"
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
                                        placeholder="ejemplo@correo.com"
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
                                        placeholder="••••••••"
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
                                        placeholder="••••••••"
                                    />
                                    {#if errors.passwordConfirmation}
                                        <div class="invalid-feedback">{errors.passwordConfirmation}</div>
                                    {/if}
                                </div>
                            </div>
                            
                            <!-- Términos y condiciones -->
                            <div class="col-12 mt-3">
                                <div class="form-check">
                                    <input
                                        type="checkbox"
                                        id="agreeTerms"
                                        bind:checked={agreeTerms}
                                        class="form-check-input {errors.terms ? 'is-invalid' : ''}"
                                    />
                                    <label class="form-check-label" for="agreeTerms">
                                        {$t('agreeTerms')} 
                                        <a href="/terms" target="_blank" class="text-decoration-none">
                                            {$t('termsAndConditions')}
                                        </a>
                                    </label>
                                    {#if errors.terms}
                                        <div class="invalid-feedback">{errors.terms}</div>
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
            
            <!-- Información adicional -->
            <div class="card border-0 info-card mt-4">
                <div class="card-body p-3 text-center">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        {$t('secureRegistration')}
                    </small>
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