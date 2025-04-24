@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh">
    <div class="col-md-5 col-lg-4 col-xl-3">
        <div class="card border-0 shadow">
            <div class="card-body p-4">
                <h4 class="text-center mb-4">Iniciar Sesión</h4>

                <div id="alert-error" class="alert alert-danger d-none mb-3">
                    <span id="error-message"></span>
                </div>

                <form id="login-form">
                    @csrf
                    <div class="mb-3">
                        <input type="email" 
                            name="email" 
                            class="form-control bg-dark border-secondary" 
                            placeholder="Email" 
                            required>
                    </div>
                    
                    <div class="mb-4">
                        <input type="password" 
                            name="password" 
                            class="form-control bg-dark border-secondary" 
                            placeholder="Contraseña" 
                            required>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            Iniciar Sesión
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <small>¿No tienes cuenta? <a href="/register">Regístrate</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.content-container {
    background-color: transparent !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
}
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('login-form');
        const submitBtn = document.getElementById('submit-btn');
        const alertError = document.getElementById('alert-error');
        const errorMessage = document.getElementById('error-message');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBtn.disabled = true;
            
            const formData = new FormData(form);
            
            fetch('/api/v1/login', {
                method: 'POST',
                body: formData,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/';
                } else {
                    errorMessage.textContent = data.message || 'Error al iniciar sesión';
                    alertError.classList.remove('d-none');
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                errorMessage.textContent = 'Error de conexión';
                alertError.classList.remove('d-none');
                submitBtn.disabled = false;
            });
        });
    });
</script>
@endsection