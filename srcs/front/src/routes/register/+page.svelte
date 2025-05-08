<script lang="ts">
    import { goto } from '$app/navigation';
    import { t } from '$lib/i18n';
    import { API_URL } from '$lib/config';
    import { onMount } from 'svelte';

    // Form data
    let username: string = '';
    let name: string = '';
    let email: string = '';
    let password: string = '';
    let passwordConfirmation: string = '';
    let birthdate: string = '';

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
    let isSubmitting = false;
    let registrationSuccess = false;
    let generalError = '';

    // Client-side validation
    function validateForm() {
        errors = {};
        
        // Username validation
        if (!username) {
            errors.username = $t('usernameRequired');
        } else if (username.length < 3) {
            errors.username = $t('usernameMinLength');
        }
        
        // Name validation
        if (!name) {
            errors.name = $t('nameRequired');
        }
        
        // Email validation
        if (!email) {
            errors.email = $t('emailRequired');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.email = $t('emailInvalid');
        }
        
        // Password validation
        if (!password) {
            errors.password = $t('passwordRequired');
        } else if (password.length < 8) {
            errors.password = $t('passwordMinLength');
        }
        
        // Password confirmation
        if (password !== passwordConfirmation) {
            errors.passwordConfirmation = $t('passwordsDontMatch');
        }
        
        // Birthdate validation is optional now
        if (birthdate) {
            const today = new Date();
            const selectedDate = new Date(birthdate);
            if (selectedDate >= today) {
                errors.birthdate = $t('birthdateInvalid');
            }
        }
        
        return Object.keys(errors).length === 0;
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
                    // Formatear errores específicos de Laravel para mostrarlos
                    const formattedErrors: ErrorsType = {};
                    for (const key in data.errors) {
                        formattedErrors[key] = data.errors[key][0]; // Tomar el primer mensaje de error
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

            // Guardar solo el token, no información del usuario
            if (data.token) {
                localStorage.setItem('token', data.token);
            }

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
        
        isSubmitting = true;
        generalError = '';
        
        try {
            const userData = {
                username,
                name,
                email,
                password,
                password_confirmation: passwordConfirmation,
                birthdate: birthdate || null
            };
            
            const response = await registerUser(userData);
            
            if (response.success) {
                registrationSuccess = true;
                // Redirect to login page after 2 seconds
                setTimeout(() => {
                    goto('/login');
                }, 2000);
            } else {
                if (response.errors) {
                    errors = response.errors;
                } else {
                    generalError = response.message || $t('registerError');
                }
            }
        } catch (error: any) {
            generalError = $t('unknownError');
        } finally {
            isSubmitting = false;
        }
    }

    // Verificar si ya hay sesión
    onMount(() => {
        const token = localStorage.getItem('token');
        if (token) {
            goto('/');
        }
    });
</script>

<div class="flex flex-col items-center justify-center px-4 py-8 bg-dark" style="min-height: calc(100vh - 64px);">
    <div class="w-full max-w-md">
        <!-- Logo y título -->
        <div class="text-center mb-4">
            <h1 class="text-3xl font-bold text-white mb-2">{$t('createAccount')}</h1>
            <p class="text-gray-400">{$t('joinKaizenCinema')}</p>
        </div>
        
        <!-- Tarjeta de registro -->
        <div class="bg-card border border-white/10 rounded-lg shadow-lg p-5">
            {#if registrationSuccess}
                <div class="bg-green-900/20 border border-green-500/30 text-green-200 rounded-md p-3 mb-4">
                    {$t('registerSuccess')}
                </div>
            {:else}
                {#if generalError}
                    <div class="bg-red-900/20 border border-red-500/30 text-red-200 rounded-md p-3 mb-4">
                        <p>{generalError}</p>
                    </div>
                {/if}
                
                <form on:submit|preventDefault={handleSubmit} class="space-y-4">
                    <!-- Username and Name side by side -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Username field -->
                        <div class="flex-1">
                            <label for="username" class="block text-sm font-medium text-gray-300 mb-1">
                                {$t('username')}
                            </label>
                            <input 
                                type="text" 
                                id="username"
                                bind:value={username}
                                class="w-full bg-dark border border-white/10 rounded-md py-2 px-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
                                placeholder={$t('username')} 
                                required
                                disabled={isSubmitting}
                            />
                            {#if errors.username}
                                <p class="mt-1 text-sm text-red-400">{errors.username}</p>
                            {/if}
                        </div>
                        
                        <!-- Name field -->
                        <div class="flex-1">
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-1">
                                {$t('fullName')}
                            </label>
                            <input 
                                type="text" 
                                id="name"
                                bind:value={name}
                                class="w-full bg-dark border border-white/10 rounded-md py-2 px-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
                                placeholder={$t('fullName')} 
                                required
                                disabled={isSubmitting}
                            />
                            {#if errors.name}
                                <p class="mt-1 text-sm text-red-400">{errors.name}</p>
                            {/if}
                        </div>
                    </div>
                    
                    <!-- Email field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">
                            {$t('email')}
                        </label>
                        <input 
                            type="email" 
                            id="email"
                            bind:value={email}
                            class="w-full bg-dark border border-white/10 rounded-md py-2 px-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
                            placeholder={$t('email')} 
                            required
                            disabled={isSubmitting}
                        />
                        {#if errors.email}
                            <p class="mt-1 text-sm text-red-400">{errors.email}</p>
                        {/if}
                    </div>
                    
                    <!-- Password fields side by side -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Password field -->
                        <div class="flex-1">
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-1">
                                {$t('password')}
                            </label>
                            <input 
                                type="password" 
                                id="password"
                                bind:value={password}
                                class="w-full bg-dark border border-white/10 rounded-md py-2 px-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
                                placeholder="••••••••" 
                                required
                                disabled={isSubmitting}
                            />
                            {#if errors.password}
                                <p class="mt-1 text-sm text-red-400">{errors.password}</p>
                            {/if}
                        </div>
                        
                        <!-- Password confirmation field -->
                        <div class="flex-1">
                            <label for="passwordConfirmation" class="block text-sm font-medium text-gray-300 mb-1">
                                {$t('confirmPassword')}
                            </label>
                            <input 
                                type="password" 
                                id="passwordConfirmation"
                                bind:value={passwordConfirmation}
                                class="w-full bg-dark border border-white/10 rounded-md py-2 px-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
                                placeholder="••••••••" 
                                required
                                disabled={isSubmitting}
                            />
                            {#if errors.passwordConfirmation}
                                <p class="mt-1 text-sm text-red-400">{errors.passwordConfirmation}</p>
                            {/if}
                        </div>
                    </div>
                    
                    <!-- Birthdate field -->
                    <div>
                        <label for="birthdate" class="block text-sm font-medium text-gray-300 mb-1">
                            {$t('birthdate')} <span class="text-gray-500 text-xs">({$t('optional')})</span>
                        </label>
                        <input 
                            type="date" 
                            id="birthdate"
                            bind:value={birthdate}
                            class="w-full bg-dark border border-white/10 rounded-md py-2 px-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500"
                            disabled={isSubmitting}
                        />
                        {#if errors.birthdate}
                            <p class="mt-1 text-sm text-red-400">{errors.birthdate}</p>
                        {/if}
                    </div>
                    
                    <button
                        type="submit"
                        disabled={isSubmitting}
                        class="w-full bg-purple-700 hover:bg-purple-600 text-white py-2 px-4 rounded-md transition-colors mt-2"
                    >
                        {#if isSubmitting}
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {$t('processing')}
                        {:else}
                            {$t('register')}
                        {/if}
                    </button>
                </form>
            {/if}
            
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-400">
                    {$t('alreadyHaveAccount')}
                    <a href="/login" class="text-purple-400 hover:text-purple-300 font-medium">
                        {$t('login')}
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