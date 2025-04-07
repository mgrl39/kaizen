@extends('layouts.app')

@section('title', 'Registro')

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
                        {{ __('Crear cuenta') }}
                    </h2>

                    <!-- Formulario -->
                    <form method="POST" action="#" class="needs-validation" novalidate>
                        @csrf

                        <div class="mb-3">
                            <input type="text"
                                name="name"
                                placeholder="{{ __('Nombre') }}"
                                class="form-control">
                        </div>

                        <div class="mb-3">
                            <input type="email"
                                name="email"
                                placeholder="{{ __('Email') }}"
                                class="form-control">
                        </div>

                        <div class="mb-4">
                            <input type="password"
                                name="password"
                                placeholder="{{ __('Contraseña') }}"
                                class="form-control">
                        </div>

                        <div class="d-grid">
                            <button type="submit"
                                    class="btn btn-primary">
                                {{ __('Registrarse') }}
                            </button>
                        </div>
                    </form>

                    <!-- Enlace a login -->
                    <p class="text-center text-muted mt-4 mb-0">
                        {{ __('¿Ya tienes cuenta?') }}
                        <a href="/login" class="text-decoration-none">
                            {{ __('Iniciar sesión') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
