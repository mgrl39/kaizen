@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
        <!-- TODO: I18N -->
            <div class="col-md-12">
                <h1 class="mb-4">Cines Disponibles</h1>

                <div class="list-group">
                    @foreach($cinemas as $cinema)
                        <div class="list-group-item">
                            <h5 class="mb-1">{{ $cinema->name }}</h5>
                            <p class="mb-1">{{ $cinema->location }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
