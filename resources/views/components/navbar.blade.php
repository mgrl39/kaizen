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
                ];
                @endphp

                @foreach($menuItems as $item)
                    <li class="nav-item">
                        <a href="{{ $item['url'] }}" class="nav-link d-flex align-items-center">
                            <i class="{{ $item['icon'] }} me-2"></i>{{ $item['text'] }}
                        </a>
                    </li>
                @endforeach

                <!-- Autenticación basada en sesiones Laravel -->
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-circle me-2"></i><span>{{ Auth::user()->username ?? Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/profile"><i class="fa-solid fa-id-card me-2"></i>Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="/bookings"><i class="fa-solid fa-ticket-alt me-2"></i>Mis Reservas</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fa-solid fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- Zona de autenticación por JWT - Gestionada por JS cuando no hay sesión -->
                    <li class="nav-item auth-guest">
                        <a href="/login" class="nav-link d-flex align-items-center">
                            <i class="fa-solid fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                    </li>
                    <li class="nav-item auth-guest">
                        <a href="/register" class="nav-link d-flex align-items-center">
                            <i class="fa-solid fa-user-plus me-2"></i>Registrarse
                        </a>
                    </li>
                    
                    <!-- Menú de usuario JWT - Oculto por defecto -->
                    <li class="nav-item dropdown auth-user d-none">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-circle me-2"></i><span id="username-display">Usuario</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/profile"><i class="fa-solid fa-id-card me-2"></i>Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="/bookings"><i class="fa-solid fa-ticket-alt me-2"></i>Mis Reservas</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="logout-btn"><i class="fa-solid fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </li>
                @endauth
                
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
    // --- Gestión de Tema Oscuro ---
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

    // --- Gestión de Autenticación en la interfaz (solo para JWT) ---
    // Solo ejecutamos esta lógica si no hay sesión de Laravel activa
    if (!document.querySelector('.auth-user.d-none')) {
        // Función para comprobar si el usuario está autenticado por JWT
        function checkAuthentication() {
            const token = localStorage.getItem('auth_token');
            const authUser = localStorage.getItem('auth_user');
            
            if (token && authUser) {
                try {
                    // Intentar parsear la información del usuario
                    const userData = JSON.parse(authUser);
                    
                    // Actualizar la interfaz para usuario autenticado
                    document.querySelectorAll('.auth-guest').forEach(el => el.classList.add('d-none'));
                    document.querySelectorAll('.auth-user').forEach(el => el.classList.remove('d-none'));
                    
                    // Mostrar el nombre de usuario
                    const usernameDisplay = document.getElementById('username-display');
                    if (usernameDisplay) {
                        usernameDisplay.textContent = userData.username || userData.name || 'Usuario';
                    }
                    
                    return true;
                } catch (e) {
                    console.error('Error parsing auth user data', e);
                    return false;
                }
            } else {
                // Actualizar la interfaz para invitado
                document.querySelectorAll('.auth-guest').forEach(el => el.classList.remove('d-none'));
                document.querySelectorAll('.auth-user').forEach(el => el.classList.add('d-none'));
                
                return false;
            }
        }
        
        // Función para cerrar sesión (JWT)
        function logout() {
            // Eliminar el token y datos de usuario
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
            
            // Actualizar la interfaz
            checkAuthentication();
            
            // Redireccionar al inicio
            window.location.href = '/';
        }
        
        // Comprobar autenticación al cargar la página
        checkAuthentication();
        
        // Evento para el botón de cerrar sesión
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                logout();
            });
        }
        
        // Escuchar cambios en el almacenamiento (por si se inicia sesión en otra pestaña)
        window.addEventListener('storage', function(e) {
            if (e.key === 'auth_token' || e.key === 'auth_user') {
                checkAuthentication();
            }
        });
    }
});
</script>