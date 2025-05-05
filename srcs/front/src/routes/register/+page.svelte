<script lang="ts">
    import { goto } from '$app/navigation';
    import { t } from '$lib/i18n';

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
            const response = await fetch('http://localhost:8000/api/v1/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
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

<div class="decorative-blob blob-1"></div>
<div class="decorative-blob blob-2"></div> 