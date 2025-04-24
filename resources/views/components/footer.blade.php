<footer class="bg-dark text-white py-3 mt-4">
    <div class="container">
        <div class="row g-3 text-center text-md-start">
            <div class="col-md-4">
                <a href="/" class="text-white text-decoration-none">
                    <i class="bi bi-film me-2"></i>Kaizen
                </a>
            </div>

            <div class="col-md-4 text-md-center">
                <div class="d-flex justify-content-center justify-content-md-center gap-3">
                    @foreach(['/' => 'Inicio', '/cinemas' => 'Cines', '/movies' => 'Películas'] as $url => $label)
                        <a href="{{ $url }}" class="text-white-50 text-decoration-none">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            <div class="col-md-4 text-md-end">
                <a href="https://github.com" target="_blank" class="text-white me-2">
                    <i class="bi bi-github"></i>
                </a>
                <small class="text-white-50">&copy; {{ date('Y') }} Kaizen</small>
            </div>
        </div>
    </div>
</footer>
