@extends('layouts.app')

@section('title', 'Nuestros Cines')

@section('content')
<div class="container">
    <h1 class="mb-4">Nuestros Cines</h1>
    
    <div class="row g-4">
        @if(empty($cinemas) || $cinemas->isEmpty())
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>No hay cines disponibles en este momento.
                </div>
            </div>
        @else
            @foreach($cinemas as $cinema)
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="row g-0">
                            <div class="col-md-4 bg-light d-flex align-items-center justify-content-center p-3">
                                <i class="bi bi-building text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-4">
                                    <h5 class="card-title">{{ $cinema->name }}</h5>
                                    <p class="card-text small text-muted">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $cinema->address }}
                                    </p>
                                    
                                    @if(isset($cinema->phone))
                                        <p class="card-text small">
                                            <i class="bi bi-telephone me-1"></i>{{ $cinema->phone }}
                                        </p>
                                    @endif
                                    
                                    <div class="mt-3">
                                        <a href="{{ url('/cinemas/'.$cinema->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-film me-1"></i>Ver cartelera
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease;
        background-color: rgba(255, 255, 255, 0.9);
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }
    
    .bg-light {
        background-color: rgba(240, 242, 248, 0.5) !important;
    }
    
    .text-primary {
        background: linear-gradient(135deg, #7B68EE, #4D9FE3);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
</style>
@endsection
