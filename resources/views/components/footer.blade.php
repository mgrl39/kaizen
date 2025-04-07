<footer class="bg-light border-top mt-auto py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                    <i class="fas fa-film text-primary me-2"></i>
                    <span class="fw-semibold">{{ __('Kaizen') }}</span>
                </div>
            </div>

            <div class="col-md-4 text-center mb-3 mb-md-0">
                <nav>
                    <ul class="list-inline mb-0">
                        @foreach(['/' => 'Inicio', '/cinemas' => 'Cines', '/movies' => 'Películas'] as $url => $label)
                            <li class="list-inline-item mx-2">
                                <a href="{{ $url }}" class="text-decoration-none text-secondary">{{ __($label) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Apply dark mode to footer if needed
    const darkMode = localStorage.getItem('darkMode') === 'true';
    if (darkMode) {
        document.querySelector('footer').classList.replace('bg-light', 'bg-dark');
        document.querySelector('footer').classList.add('text-light');
        document.querySelectorAll('footer .text-secondary').forEach(el => {
            el.classList.replace('text-secondary', 'text-light');
        });
    }
    
    // Listen for dark mode changes
    window.addEventListener('storage', function(e) {
        if (e.key === 'darkMode') {
            const isDark = e.newValue === 'true';
            const footer = document.querySelector('footer');
            
            if (isDark) {
                footer.classList.replace('bg-light', 'bg-dark');
                footer.classList.add('text-light');
                document.querySelectorAll('footer .text-secondary').forEach(el => {
                    el.classList.replace('text-secondary', 'text-light');
                });
            } else {
                footer.classList.replace('bg-dark', 'bg-light');
                footer.classList.remove('text-light');
                document.querySelectorAll('footer .text-light').forEach(el => {
                    if (!el.classList.contains('text-secondary')) {
                        el.classList.replace('text-light', 'text-secondary');
                    }
                });
            }
        }
    });
});
</script>