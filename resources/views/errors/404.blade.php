@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
<!-- TODO: Quitar el css -->
<div style="text-align: center; padding: 4rem 0;">
    <h1 style="font-size: 6rem; font-weight: bold; margin-bottom: 1rem;">404</h1>
    <p style="font-size: 1.2rem; margin-bottom: 2rem;">Página no encontrada</p>
    <a href="{{ url('/') }}" style="padding: 0.75rem 1.5rem; background: #2563eb; color: white; text-decoration: none; border-radius: 0.5rem;">
        Volver al inicio
    </a>
</div>
@endsection
