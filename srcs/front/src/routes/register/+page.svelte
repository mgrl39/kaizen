<script lang="ts">
    import { goto } from '$app/navigation';

    // Form data
    let username: string = '';
    let email: string = '';
    let password: string = '';
    let passwordConfirmation: string = '';
    let birthdate: string = '';

    // Error handling
    type ErrorsType = {
        username?: string;
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
            errors.username = 'El nombre de usuario es obligatorio';
        } else if (username.length < 3) {
            errors.username = 'El nombre de usuario debe tener al menos 3 caracteres';
        }
        
        // Email validation
        if (!email) {
            errors.email = 'El email es obligatorio';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.email = 'El formato del email no es válido';
        }
        
        // Password validation
        if (!password) {
            errors.password = 'La contraseña es obligatoria';
        } else if (password.length < 8) {
            errors.password = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        // Password confirmation
        if (password !== passwordConfirmation) {
            errors.passwordConfirmation = 'Las contraseñas no coinciden';
        }
        
        // Birthdate validation
        if (!birthdate) {
            errors.birthdate = 'La fecha de nacimiento es obligatoria';
        } else {
            const today = new Date();
            const selectedDate = new Date(birthdate);
            if (selectedDate >= today) {
                errors.birthdate = 'La fecha de nacimiento debe ser anterior a hoy';
            }
        }
        
        return Object.keys(errors).length === 0;
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
                email,
                password,
                password_confirmation: passwordConfirmation,
                birthdate
            };
            
            const response = await registerUser(userData);
            
            if (response.success) {
                registrationSuccess = true;
                // Redirect to login page after 2 seconds
                setTimeout(() => {
                    goto('/login');
                }, 2000);
            } else {
                generalError = response.message || 'Error en el registro';
            }
        } catch (error: any) {
            if (error.errors) {
                errors = error.errors as ErrorsType;
            } else {
                generalError = error.message || 'Error en el registro';
            }
        } finally {
            isSubmitting = false;
        }
    }
</script>

<svelte:head>
    <title>Registro - Cinema App</title>
</svelte:head>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6 text-center">Crear cuenta</h1>
        
        {#if registrationSuccess}
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <p>¡Registro exitoso! Redireccionando al inicio de sesión...</p>
            </div>
        {:else}
            {#if generalError}
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <p>{generalError}</p>
                </div>
            {/if}
            
            <form on:submit|preventDefault={handleSubmit} class="space-y-4">
                <!-- Username field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Nombre de usuario</label>
                    <input
                        type="text"
                        id="username"
                        bind:value={username}
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    {#if errors.username}
                        <p class="text-red-500 text-xs mt-1">{errors.username}</p>
                    {/if}
                </div>
                
                <!-- Email field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        type="email"
                        id="email"
                        bind:value={email}
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    {#if errors.email}
                        <p class="text-red-500 text-xs mt-1">{errors.email}</p>
                    {/if}
                </div>
                
                <!-- Password field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        bind:value={password}
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    {#if errors.password}
                        <p class="text-red-500 text-xs mt-1">{errors.password}</p>
                    {/if}
                </div>
                
                <!-- Password confirmation field -->
                <div>
                    <label for="passwordConfirmation" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                    <input
                        type="password"
                        id="passwordConfirmation"
                        bind:value={passwordConfirmation}
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    {#if errors.passwordConfirmation}
                        <p class="text-red-500 text-xs mt-1">{errors.passwordConfirmation}</p>
                    {/if}
                </div>
                
                <!-- Birthdate field -->
                <div>
                    <label for="birthdate" class="block text-sm font-medium text-gray-700">Fecha de nacimiento</label>
                    <input
                        type="date"
                        id="birthdate"
                        bind:value={birthdate}
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    {#if errors.birthdate}
                        <p class="text-red-500 text-xs mt-1">{errors.birthdate}</p>
                    {/if}
                </div>
                
                <!-- Submit button -->
                <div>
                    <button
                        type="submit"
                        disabled={isSubmitting}
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        {#if isSubmitting}
                            Procesando...
                        {:else}
                            Registrarse
                        {/if}
                    </button>
                </div>
            </form>
            
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">
                    ¿Ya tienes cuenta?
                    <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500">Inicia sesión</a>
                </p>
            </div>
        {/if}
    </div>
</div> 