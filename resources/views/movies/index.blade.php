@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Películas Disponibles</h1>
                
                <div class="list-group">
                    @foreach($movies as $movie)
                        <div class="list-group-item">
                            <h5 class="mb-1">{{ $movie->title }}</h5>
                            <p class="mb-1">Duración: {{ $movie->duration }} minutos</p>
                            <small>Género: {{ $movie->genre }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection 