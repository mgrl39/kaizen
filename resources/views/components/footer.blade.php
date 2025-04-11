<footer class="bg-light border-top mt-auto py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                    <i class="fas fa-film text-primary me-2"></i>
        <!-- TODO: SEGURO SEGUIRA CON ESE NOMBRE???? -->
                    <span class="fw-semibold">{{ __('Kaizen') }}</span>
                </div>
            </div>

            <div class="col-md-4 text-center mb-3 mb-md-0">
                <nav>
                    <ul class="list-inline mb-0">
<!-- TODO: Otra vez hardcodeando la info en le back... necesito igualar todo... no megusta tener datos hardcodeados... -->
                        @foreach(['/' => 'Inicio', '/cinemas' => 'Cines', '/movies' => 'Películas'] as $url => $label)
                            <li class="list-inline-item mx-2">
                                <a href="{{ $url }}" class="text-decoration-none text-secondary">{{ __($label) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>

<!-- TODO: VER SI SE PUEDE MEJORAR ESTO QUE ES MAS FEOOOO -->
            <div class="col-md-4 text-center text-md-end">
                <div>
                    <a href="https://github.com/mgrl39" target="_blank" class="text-secondary me-3">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="https://opensource.org/licenses/MIT" target="_blank" class="text-decoration-none small text-muted">
                        {{ __('MIT License') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
