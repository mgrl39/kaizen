@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 200px);">
    <div class="col-md-6 col-lg-4 animate__animated animate__fadeIn">
        <div class="card border-0 shadow-lg" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="card-body p-4 p-sm-5">
                <!-- Logo -->
                <div class="text-center mb-4 animate__animated animate__fadeInDown">
                    <i class="fa-solid fa-film fs-1" style="color: var(--primary-color);"></i>
                </div>

                <!-- Título -->
                <h2 class="text-center mb-4 fw-semibold animate__animated animate__fadeInDown" style="color: var(--text-primary);">
                    {{ __('Hola de nuevo') }}
                </h2>

                <!-- Alertas -->
                <div id="alert-success" class="alert alert-success alert-dismissible fade d-none animate__animated animate__fadeIn" role="alert" style="background-color: var(--success-color); color: white; border: none;">
                    <span id="success-message"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div id="alert-error" class="alert alert-danger alert-dismissible fade d-none animate__animated animate__fadeIn" role="alert" style="background-color: var(--danger-color); color: white; border: none;">
                    <span id="error-message"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <!-- Debug Info (oculto en producción) -->
                <div id="debug-info" class="alert alert-info d-none">
                    <h6>Información de Depuración:</h6>
                    <pre id="debug-content" style="white-space: pre-wrap; max-height: 200px; overflow-y: auto;"></pre>
                </div>

                <!-- Formulario -->
                <form id="login-form" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3 animate__animated animate__fadeInUp">
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: var(--input-bg); border-color: var(--input-border);">
                                <i class="fa-solid fa-user" style="color: var(--text-secondary);"></i>
                            </span>
                            <input type="text"
                                id="identifier"
                                name="identifier"
                                placeholder="{{ __('Nombre de usuario o email') }}"
                                class="form-control"
                                style="background-color: var(--input-bg); color: var(--input-color); border-color: var(--input-border);"
                                required>
                        </div>
                        <div class="invalid-feedback" id="identifier-error" style="color: var(--danger-color);"></div>
                    </div>

                    <div class="mb-4 animate__animated animate__fadeInUp">
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: var(--input-bg); border-color: var(--input-border);">
                                <i class="fa-solid fa-lock" style="color: var(--text-secondary);"></i>
                            </span>
                            <input type="password"
                                id="password"
                                name="password"
                                placeholder="{{ __('Contraseña') }}"
                                class="form-control"
                                style="background-color: var(--input-bg); color: var(--input-color); border-color: var(--input-border);"
                                required>
                        </div>
                        <div class="invalid-feedback" id="password-error" style="color: var(--danger-color);"></div>
                    </div>

                    <div class="d-grid animate__animated animate__fadeInUp">
                        <button type="submit"
                                id="submit-btn"
                                class="btn btn-primary btn-lg"
                                style="background-color: var(--primary-color); border-color: var(--primary-color);">
                            {{ __('Iniciar sesión') }}
                        </button>
                    </div>
                </form>

                <!-- Enlace a registro -->
                <p class="text-center mt-4 mb-0 animate__animated animate__fadeInUp" style="color: var(--text-secondary);">
                    {{ __('¿No tienes cuenta?') }}
                    <a href="/register" class="text-decoration-none" style="color: var(--primary-color);">
                        {{ __('Registrate') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('login-form');
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
                identifier: document.getElementById('identifier').value,
                password: document.getElementById('password').value,
            };
            
            // Iniciar estado de carga
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
            
            try {
                const token = document.querySelector('input[name="_token"]').value;
                
                const response = await fetch('/api/login', {
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
                    } else if (response.status === 401) {
                        showMessage(false, data.message || 'Credenciales incorrectas');
                    } else {
                        throw new Error(data.message || 'Error al iniciar sesión');
                    }
                } else {
                    showMessage(true, 'Inicio de sesión exitoso. Redirigiendo...');
                    
                    if (data.token) {
                        localStorage.setItem('auth_token', data.token);
                    }
                    
                    if (data.user) {
                        localStorage.setItem('auth_user', JSON.stringify(data.user));
                    }
                    
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
                }
            } catch (error) {
                console.error('Login error:', error);
                showMessage(false, error.message || 'Ha ocurrido un error inesperado');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ __("Iniciar sesión") }}';
            }
        });
    });
</script>
@endsection
