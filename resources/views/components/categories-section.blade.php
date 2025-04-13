<!-- Categories Section -->
<div class="container mb-5">
    <div class="row g-4">
        <!-- TODO: Modificar el foreach y saber los que hay disponibles por el BACK-->
        <!-- TODO: foreach da pena -->
        @foreach(['Acción', 'Comedia', 'Drama', 'Animacion'] as $category)
        <div class="col-md-3">
            <div class="card bg-dark text-white card-hover h-100 border-0">
                <div class="card-body text-center">
                    <i class="fas fa-film fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">{{ $category }}</h5>
                    <p class="card-text text-muted">Explora películas de {{ $category }}</p>
                    <a href="/movies?category={{ $category }}" class="btn btn-outline-light btn-floating mt-2">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
