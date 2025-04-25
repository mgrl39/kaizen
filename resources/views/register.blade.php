@extends('layouts.app')

@section('title', 'Registro')

@section('styles')
<link href="{{ asset('assets/styles/authentication.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container auth-container">
    <div class="col-md-5 col-lg-4 col-xl-3">
        <div class="card auth-card">
            <div class="card-body p-4">
                <h4 class="text-center mb-4">Crear Cuenta</h4>

                <div id="alert-error" class="alert alert-danger d-none mb-3">
                    <span id="error-message"></span>
                </div>

                <form id="register-form">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="name" id="name" class="form-control auth-form-input" placeholder="Nombre" required>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <input type="email" name="email" id="email" class="form-control auth-form-input" placeholder="Email" required>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>
                    
                    <div class="mb-4">
                        <input type="password" name="password" id="password" class="form-control auth-form-input" placeholder="Contraseña" required>
                        <div class="invalid-feedback" id="password-error"></div>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" id="submit-btn" class="btn btn-primary">Registrarse</button>
                    </div>
                    
                    <div class="text-center">
                        <small>¿Ya tienes cuenta? <a href="/login">Iniciar sesión</a></small>
                    </div>
                </form>
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
        const alertError = document.getElementById('alert-error');
        const errorMessage = document.getElementById('error-message');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            submitBtn.disabled = true;
            
            fetch('/api/v1/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value
                })
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                
                if (data.success) {
                    if (data.token) localStorage.setItem('auth_token', data.token);
                    if (data.user) localStorage.setItem('auth_user', JSON.stringify(data.user));
                    window.location.href = '/login';
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const input = document.getElementById(key);
                            if (input) {
                                input.classList.add('is-invalid');
                                document.getElementById(`${key}-error`).textContent = data.errors[key][0];
                            }
                        });
                    }
                    errorMessage.textContent = data.message || 'Error en el registro';
                    alertError.classList.remove('d-none');
                }
            })
            .catch(() => {
                submitBtn.disabled = false;
                errorMessage.textContent = 'Error de conexión';
                alertError.classList.remove('d-none');
            });
        });
    });
</script>
@endsection
