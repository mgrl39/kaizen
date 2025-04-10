@extends('layouts.app')

@section('title', 'Login')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/styles/authentication.css') }}">
@endsection

@section('content')
<div class="auth-container">
    <div class="auth-card rounded-4">
        <div class="auth-content">
            <!-- Sección de bienvenida -->
            <div class="auth-welcome animate__animated animate__fadeInLeft">
                <h1>¡Bienvenido de nuevo!</h1>
                <p class="mb-4">Accede a tu cuenta para disfrutar de las mejores películas y series. Tu experiencia cinematográfica te espera.</p>
            </div>

            <!-- Formulario -->
            <div class="auth-form animate__animated animate__fadeInRight">
                <h2 class="text-white mb-4">Iniciar sesión</h2>

                <!-- Alertas -->
                <div id="alert-success" class="alert alert-success alert-dismissible fade d-none" role="alert">
                    <span id="success-message"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div id="alert-error" class="alert alert-danger alert-dismissible fade d-none" role="alert">
                    <span id="error-message"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

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

                <p class="text-center mt-4 mb-0">
                    <span class="text-white-50">¿No tienes cuenta?</span>
                    <a href="/register" class="auth-link ms-2">Regístrate aquí</a>
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
