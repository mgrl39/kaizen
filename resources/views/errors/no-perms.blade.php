@extends('layouts.app')

@section('title', 'Acceso Denegado')

@section('content')
<!-- TODO: FALTA USAR INTERNACIONALIZACION AQUI -->
<div class="text-center py-5">
    <div class="mb-4">
        <i class="fas fa-lock text-danger display-1"></i>
    </div>
    <h1 class="display-4 fw-bold mb-3">403</h1>
    <p class="fs-4 mb-2">Acceso Denegado</p>
    <p class="text-muted mb-4">No tienes los permisos necesarios para acceder a esta página</p>

    <div class="d-flex justify-content-center gap-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4">
            <i class="fas fa-arrow-left me-2"></i>Volver atrás
        </a>
        <a href="{{ url('/') }}" class="btn btn-primary px-4">
            <i class="fas fa-home me-2"></i>Ir al inicio
        </a>
    </div>
</div>
@endsection
