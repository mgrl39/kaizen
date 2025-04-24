@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
<div class="container d-flex align-items-center justify-content-center">
    <div class="text-center">
        <h1 class="display-1 fw-bold mb-4">404</h1>
        <p class="fs-4 mb-4">Página no encontrada</p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
            Volver al inicio
        </a>
    </div>
</div>
@endsection
