<script lang="ts">
    import { goto } from '$app/navigation';
    import { t } from '$lib/i18n';
    import { API_URL } from '$lib/config';
    import { onMount } from 'svelte';

    // Datos del formulario
    let name = '';
    let email = '';
    let password = '';
    let passwordConfirmation = '';
    let acceptTerms = false;
    
    // Estados
    let loading = false;
    let error: string | null = null;
    let errors: Record<string, string> = {};
    let currentStep = 1;
    let totalSteps = 2;

    // Validación de campos
    function validateStep1() {
        errors = {};
        let isValid = true;
        
        if (!name.trim()) {
            errors.name = 'El nombre es obligatorio';
            isValid = false;
        }
        
        if (!email.trim()) {
            errors.email = 'El correo electrónico es obligatorio';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.email = 'El correo electrónico no es válido';
            isValid = false;
        }
        
        return isValid;
    }
    
    function validateStep2() {
        errors = {};
        let isValid = true;
        
        if (!password) {
            errors.password = 'La contraseña es obligatoria';
            isValid = false;
        } else if (password.length < 8) {
            errors.password = 'La contraseña debe tener al menos 8 caracteres';
            isValid = false;
        }
        
        if (password !== passwordConfirmation) {
            errors.passwordConfirmation = 'Las contraseñas no coinciden';
            isValid = false;
        }
        
        if (!acceptTerms) {
            errors.acceptTerms = 'Debes aceptar los términos y condiciones';
            isValid = false;
        }
        
        return isValid;
    }
    
    function nextStep() {
        if (currentStep === 1 && validateStep1()) {
            currentStep = 2;
        }
    }
    
    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
        }
    }

    // Envío del formulario
    async function handleRegister() {
        if (!validateStep2()) return;
        
        error = null;
        loading = true;
        
        try {
            const response = await fetch(`${API_URL}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name,
                    email,
                    password,
                    password_confirmation: passwordConfirmation
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Guardar token si la API lo devuelve
                if (data.token) {
                    localStorage.setItem('token', data.token);
                }
                
                // Redirigir al usuario
                goto('/login?registered=true');
            } else {
                if (data.errors) {
                    // Errores de validación del servidor
                    errors = Object.entries(data.errors).reduce((acc, [key, value]) => {
                        acc[key] = Array.isArray(value) ? value[0] : value;
                        return acc;
                    }, {});
                } else {
                    error = data.message || 'Error al registrar la cuenta';
                }
            }
        } catch (err) {
            error = 'Error de conexión con el servidor';
        } finally {
            loading = false;
        }
    }
    
    // Verificar si ya hay sesión
    onMount(() => {
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
</script>

<div class="container auth-container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card auth-card">
        <div class="card-body p-4">
          <h4 class="text-center mb-4">{$t('createAccount')}</h4>

          {#if registrationSuccess}
            <div class="alert alert-success mb-3">
              {$t('registerSuccess')}
            </div>
          {:else}
            {#if generalError}
              <div class="alert alert-danger mb-3">
                {generalError}
              </div>
            {/if}

            <form on:submit|preventDefault={handleSubmit}>
              <!-- Username field -->
              <div class="mb-3">
                <input 
                  type="text" 
                  bind:value={username}
                  class="form-control" 
                  placeholder={$t('username')} 
                  required
                  disabled={isSubmitting}>
                {#if errors.username}
                  <small class="text-danger">{errors.username}</small>
                {/if}
              </div>
              
              <!-- Name field -->
              <div class="mb-3">
                <input 
                  type="text" 
                  bind:value={name}
                  class="form-control" 
                  placeholder={$t('fullName')} 
                  required
                  disabled={isSubmitting}>
                {#if errors.name}
                  <small class="text-danger">{errors.name}</small>
                {/if}
              </div>
              
              <!-- Email field -->
              <div class="mb-3">
                <input 
                  type="email" 
                  bind:value={email}
                  class="form-control" 
                  placeholder={$t('email')} 
                  required
                  disabled={isSubmitting}>
                {#if errors.email}
                  <small class="text-danger">{errors.email}</small>
                {/if}
              </div>
              
              <!-- Password field -->
              <div class="mb-3">
                <input 
                  type="password" 
                  bind:value={password}
                  class="form-control" 
                  placeholder={$t('password')} 
                  required
                  disabled={isSubmitting}>
                {#if errors.password}
                  <small class="text-danger">{errors.password}</small>
                {/if}
              </div>
              
              <!-- Password confirmation field -->
              <div class="mb-3">
                <input 
                  type="password" 
                  bind:value={passwordConfirmation}
                  class="form-control" 
                  placeholder={$t('confirmPassword')} 
                  required
                  disabled={isSubmitting}>
                {#if errors.passwordConfirmation}
                  <small class="text-danger">{errors.passwordConfirmation}</small>
                {/if}
              </div>
              
              <!-- Birthdate field -->
              <div class="mb-4">
                <input 
                  type="date" 
                  bind:value={birthdate}
                  class="form-control" 
                  placeholder={$t('birthdate')} 
                  disabled={isSubmitting}>
                {#if errors.birthdate}
                  <small class="text-danger">{errors.birthdate}</small>
                {/if}
              </div>
              
              <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary" disabled={isSubmitting}>
                  {#if isSubmitting}
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    {$t('processing')}
                  {:else}
                    {$t('register')}
                  {/if}
                </button>
              </div>
              
              <div class="text-center">
                <small>{$t('alreadyHaveAccount')} <a href="/login">{$t('login')}</a></small>
              </div>
            </form>
          {/if}
        </div>
      </div>
    </div>
  </div>
</div>