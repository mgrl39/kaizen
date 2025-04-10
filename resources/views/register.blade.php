@extends('layouts.app')

@section('title', 'Registro')

@section('styles')
<style>
.auth-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 900px;
    margin: 2rem;
}

.auth-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    padding: 2rem;
}

.auth-welcome {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 2rem;
}

.auth-welcome h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #4a90e2, #357abd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
}

.auth-welcome p {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.1rem;
    line-height: 1.6;
}

.auth-form {
    padding: 2rem;
    border-left: 1px solid rgba(255, 255, 255, 0.1);
}

.form-control, .input-group-text {
    background: rgba(255, 255, 255, 0.07) !important;
    border: 2px solid rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
    padding: 0.8rem 1rem;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: #4a90e2 !important;
    box-shadow: 0 0 15px rgba(74, 144, 226, 0.3);
}

.btn-primary {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
    border: none !important;
    padding: 0.8rem 2rem;
    font-weight: 500;
}

.auth-link {
    color: #4a90e2 !important;
    text-decoration: none;
    transition: all 0.3s ease;
}

.auth-link:hover {
    color: #357abd !important;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .auth-content {
        grid-template-columns: 1fr;
    }
    
    .auth-form {
        border-left: none;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
}
</style>
@endsection

@section('content')
<div class="auth-container">
    <div class="auth-card rounded-4">
        <div class="auth-content">
            <!-- Sección de bienvenida -->
            <div class="auth-welcome animate__animated animate__fadeInLeft">
                <h1>¡Únete a nosotros!</h1>
                <p class="mb-4">Crea tu cuenta y descubre un mundo de entretenimiento. Las mejores películas y series te esperan.</p>
            </div>

            <!-- Formulario -->
            <div class="auth-form animate__animated animate__fadeInRight">
                <h2 class="text-white mb-4">Crear cuenta</h2>

                <!-- Alertas -->
                <div id="alert-success" class="alert alert-success alert-dismissible fade d-none" role="alert">
                    <span id="success-message"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div id="alert-error" class="alert alert-danger alert-dismissible fade d-none" role="alert">
                    <span id="error-message"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <form id="register-form" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-user text-white-50"></i>
                            </span>
                            <input type="text"
                                id="name"
                                name="name"
                                placeholder="{{ __('Nombre de usuario') }}"
                                class="form-control"
                                required>
                        </div>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-envelope text-white-50"></i>
                            </span>
                            <input type="email"
                                id="email"
                                name="email"
                                placeholder="{{ __('Email') }}"
                                class="form-control"
                                required>
                        </div>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>

                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa-solid fa-lock text-white-50"></i>
                            </span>
                            <input type="password"
                                id="password"
                                name="password"
                                placeholder="{{ __('Contraseña') }}"
                                class="form-control"
                                required
                                minlength="8">
                        </div>
                        <div class="invalid-feedback" id="password-error"></div>
                    </div>

                    <div class="d-grid">
                        <button type="submit"
                                id="submit-btn"
                                class="btn btn-primary btn-lg">
                            {{ __('Registrarse') }}
                        </button>
                    </div>
                </form>

                <p class="text-center mt-4 mb-0">
                    <span class="text-white-50">¿Ya tienes cuenta?</span>
                    <a href="/login" class="auth-link ms-2">Inicia sesión aquí</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('register-form');
        const submitBtn = document.getElementById('submit-btn');
        const alertSuccess = document.getElementById('alert-success');
        const alertError = document.getElementById('alert-error');
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        const debugInfo = document.getElementById('debug-info');
        const debugContent = document.getElementById('debug-content');

        // Mostrar información de depuración
        function showDebugInfo(info, title = null) {
            // Convertir cualquier objeto a formato legible
            const content = typeof info === 'object' ? JSON.stringify(info, null, 2) : info;
            debugContent.textContent = content;
            
            if (title) {
                debugContent.insertAdjacentHTML('beforebegin', `<div class="alert alert-warning mb-2">${title}</div>`);
            }
            
            debugInfo.classList.remove('d-none');
        }

        // Función para mostrar mensajes con animación
        function showMessage(isSuccess, message) {
            const alert = isSuccess ? alertSuccess : alertError;
            const messageElement = isSuccess ? successMessage : errorMessage;
            
            messageElement.textContent = message;
            alert.classList.remove('d-none');
            alert.classList.add('show', 'animate__fadeIn');
            
            // Ocultar el otro mensaje
            const otherAlert = isSuccess ? alertError : alertSuccess;
            otherAlert.classList.add('d-none');
            otherAlert.classList.remove('show');
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Reset form state
            form.classList.remove('was-validated');
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            alertSuccess.classList.remove('show');
            alertSuccess.classList.add('d-none');
            alertError.classList.remove('show');
            alertError.classList.add('d-none');
            debugInfo.classList.add('d-none');
            debugInfo.querySelectorAll('.alert-warning').forEach(el => el.remove());
            
            // Form validation
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            };
            
            // Iniciar estado de carga
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
            
            try {
                const token = document.querySelector('input[name="_token"]').value;
                
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorElement = document.getElementById(`${key}-error`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                                document.getElementById(key).classList.add('is-invalid');
                            }
                        });
                        showMessage(false, 'Por favor corrige los errores en el formulario');
                    } else {
                        throw new Error(data.message || 'Error en el registro');
                    }
                } else {
                    showMessage(true, 'Registro exitoso. Redirigiendo...');
                    
                    if (data.token) {
                        localStorage.setItem('auth_token', data.token);
                    }
                    
                    if (data.user) {
                        localStorage.setItem('auth_user', JSON.stringify(data.user));
                    }
                    
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                }
            } catch (error) {
                console.error('Registration error:', error);
                showMessage(false, error.message || 'Ha ocurrido un error inesperado');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ __("Registrarse") }}';
            }
        });
    });
</script>
@endsection
