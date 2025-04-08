@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 200px);">
    <div class="text-center">
        <h1 class="display-1 fw-bold mb-4" style="color: var(--primary-color);">404</h1>
        <p class="fs-4 mb-4" style="color: var(--text-primary);">Página no encontrada</p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-lg" style="background-color: var(--primary-color); border-color: var(--primary-color);">
            Volver al inicio
        </a>
    </div>
</div>
@endsection
