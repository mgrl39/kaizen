<nav class="navbar navbar-expand-md navbar-light bg-light fixed-top border-bottom">
    <div class="container">
        <a href="/" class="navbar-brand d-flex align-items-center">
            <i class="fa-solid fa-film fs-3 text-primary"></i>
            <span class="ms-2 fw-bold">Kaizen</span>
        </a>
        
        <div class="d-flex align-items-center">
            <button class="btn btn-light rounded-circle d-md-none me-2" id="darkModeToggle">
                <i class="fa-solid fa-sun text-warning" id="lightIcon"></i>
                <i class="fa-solid fa-moon text-primary d-none" id="darkIcon"></i>
            </button>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-md-0">
                @php
                $menuItems = [
                    ['url' => '/cinemas', 'icon' => 'fa-solid fa-building', 'text' => 'Cines'],
                    ['url' => '/movies', 'icon' => 'fa-solid fa-video', 'text' => 'Películas'], 
                    ['url' => '/login', 'icon' => 'fa-solid fa-sign-in-alt', 'text' => 'Iniciar Sesión'],
                    ['url' => '/register', 'icon' => 'fa-solid fa-user-plus', 'text' => 'Registrarse']
                ];
                @endphp

                @foreach($menuItems as $item)
                    <li class="nav-item">
                        <a href="{{ $item['url'] }}" class="nav-link d-flex align-items-center">
                            <i class="{{ $item['icon'] }} me-2"></i>{{ $item['text'] }}
                        </a>
                    </li>
                @endforeach
                
                <li class="nav-item d-none d-md-block">
                    <button class="btn btn-light rounded-circle ms-2" id="darkModeToggleLg">
                        <i class="fa-solid fa-sun text-warning" id="lightIconLg"></i>
                        <i class="fa-solid fa-moon text-primary d-none" id="darkIconLg"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for dark mode preference
    const darkMode = localStorage.getItem('darkMode') === 'true';
    
    // Function to toggle dark/light mode
    function toggleDarkMode(isDark) {
        if (isDark) {
            document.body.classList.add('bg-dark', 'text-light');
            document.querySelector('.navbar').classList.replace('navbar-light', 'navbar-dark');
            document.querySelector('.navbar').classList.replace('bg-light', 'bg-dark');
            
            // Toggle icons
            document.querySelectorAll('#lightIcon, #lightIconLg').forEach(icon => icon.classList.add('d-none'));
            document.querySelectorAll('#darkIcon, #darkIconLg').forEach(icon => icon.classList.remove('d-none'));
        } else {
            document.body.classList.remove('bg-dark', 'text-light');
            document.querySelector('.navbar').classList.replace('navbar-dark', 'navbar-light');
            document.querySelector('.navbar').classList.replace('bg-dark', 'bg-light');
            
            // Toggle icons
            document.querySelectorAll('#lightIcon, #lightIconLg').forEach(icon => icon.classList.remove('d-none'));
            document.querySelectorAll('#darkIcon, #darkIconLg').forEach(icon => icon.classList.add('d-none'));
        }
        
        // Save preference
        localStorage.setItem('darkMode', isDark);
    }
    
    // Initialize
    toggleDarkMode(darkMode);
    
    // Add event listeners to both toggle buttons
    document.getElementById('darkModeToggle').addEventListener('click', function() {
        toggleDarkMode(!document.body.classList.contains('bg-dark'));
    });
    
    document.getElementById('darkModeToggleLg').addEventListener('click', function() {
        toggleDarkMode(!document.body.classList.contains('bg-dark'));
    });
});
</script>