@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 my-5">
                <div class="card-body p-4 p-sm-5">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <i class="fas fa-film text-primary fs-1"></i>
                    </div>

                    <!-- Título -->
                    <h2 class="text-center mb-4 fw-semibold">
                        {{ __('Hola de nuevo') }}
                    </h2>

                    <!-- Alertas -->
                    <div id="alert-success" class="alert alert-success alert-dismissible fade d-none" role="alert">
                        <span id="success-message"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <div id="alert-error" class="alert alert-danger alert-dismissible fade d-none" role="alert">
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

                        <div class="mb-3">
                            <input type="text"
                                id="identifier"
                                name="identifier"
                                placeholder="{{ __('Nombre de usuario o email') }}"
                                class="form-control"
                                required>
                            <div class="invalid-feedback" id="identifier-error"></div>
                        </div>

                        <div class="mb-4">
                            <input type="password"
                                id="password"
                                name="password"
                                placeholder="{{ __('Contraseña') }}"
                                class="form-control"
                                required>
                            <div class="invalid-feedback" id="password-error"></div>
                        </div>

                        <div class="d-grid">
                            <button type="submit"
                                    id="submit-btn"
                                    class="btn btn-primary">
                                {{ __('Iniciar sesión') }}
                            </button>
                        </div>
                    </form>

                    <!-- Enlace a login -->
                    <p class="text-center text-muted mt-4 mb-0">
                        {{ __('¿No tienes cuenta?') }}
                        <a href="/register" class="text-decoration-none">
                            {{ __('Registrate') }}
                        </a>
                    </p>
                </div>
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

        // Función para mostrar mensajes
        function showMessage(isSuccess, message) {
            if (isSuccess) {
                successMessage.textContent = message;
                alertSuccess.classList.add('show');
                alertSuccess.classList.remove('d-none');
                alertError.classList.add('d-none');
                alertError.classList.remove('show');
            } else {
                errorMessage.textContent = message;
                alertError.classList.add('show');
                alertError.classList.remove('d-none');
                alertSuccess.classList.add('d-none');
                alertSuccess.classList.remove('show');
            }
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
                // Get CSRF token
                const token = document.querySelector('input[name="_token"]').value;
                
                const requestInfo = {
                    url: '/api/login',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(formData)
                };
                
                console.log('Request info:', requestInfo);
                
                // Send API request
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(formData)
                });
                
                // Mostrar información sobre la respuesta
                console.log('Response status:', response.status);
                console.log('Response headers:', [...response.headers.entries()]);
                console.log('Response ok:', response.ok);
                
                // Intentar obtener el cuerpo de la respuesta
                const responseText = await response.text();
                console.log('Response text:', responseText);
                
                let data;
                try {
                    // Convertir la respuesta a JSON
                    data = JSON.parse(responseText);
                    console.log('Response data:', data);
                    
                    // Mostrar para debug
                    const responseInfo = {
                        status: response.status,
                        ok: response.ok,
                        data: data
                    };
                    showDebugInfo(responseInfo, 'Respuesta del servidor');
                } catch (parseError) {
                    console.error('Error parsing JSON:', parseError);
                    showMessage(false, 'Error al procesar la respuesta del servidor');
                    showDebugInfo(responseText, 'Respuesta no JSON');
                    return;
                }
                
                // Comprobar si la respuesta es correcta
                if (!response.ok) {
                    // Handle validation errors
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
                        // Credenciales incorrectas
                        showMessage(false, data.message || 'Credenciales incorrectas');
                    } else {
                        // Mostrar más detalles sobre el error
                        let errorMsg = data.message || 'Error al iniciar sesión';
                        if (data.details) {
                            errorMsg += ` (${data.details.file}:${data.details.line})`;
                        }
                        throw new Error(errorMsg);
                    }
                } else if (data && data.success === true) {
                    // Success - respuesta correcta
                    showMessage(true, 'Inicio de sesión exitoso. Redirigiendo...');
                    
                    // Store token if returned
                    if (data.token) {
                        localStorage.setItem('auth_token', data.token);
                    }
                    
                    // Guardar datos del usuario
                    if (data.user) {
                        localStorage.setItem('auth_user', JSON.stringify(data.user));
                    }
                    
                    // Redirect after success
                    setTimeout(() => {
                        // Redirigir a la página principal
                        window.location.href = '/';
                    }, 2000);
                } else {
                    // Response was OK but success flag not present or not true
                    console.error('Formato de respuesta incorrecto:', data);
                    throw new Error('La respuesta del servidor no tiene el formato esperado');
                }
            } catch (error) {
                console.error('Login error:', error);
                // Handle network errors
                showMessage(false, error.message || 'Ha ocurrido un error inesperado');
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ __("Iniciar sesión") }}';
            }
        });
    });
</script>
@endsection
