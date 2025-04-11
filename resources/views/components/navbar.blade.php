<nav class="navbar navbar-expand-md fixed-top" style="background-color: var(--secondary-bg); border-bottom: 1px solid var(--border-color);">
    <div class="container">
<!-- TODO: de nuevo... ese kaizen logo y tal deberia cambiarlo..... pero no se bien bien como -->
        <a href="/" class="navbar-brand d-flex align-items-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Kaizen Logo" class="navbar-logo">
            <span class="ms-2 fw-bold" style="color: var(--text-primary);">Kaizen</span>
        </a>

        <div class="d-flex align-items-center">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" style="border-color: var(--border-color);">
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
                        <a href="{{ $item['url'] }}" class="nav-link d-flex align-items-center" style="color: var(--text-primary);">
                            <i class="{{ $item['icon'] }} me-2" style="color: var(--primary-color);"></i>{{ $item['text'] }}
                        </a>
                    </li>
                @endforeach

<!-- TODO: ARREGLAR TEMA AUTH::USER()->avatar -->
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--text-primary);">
                            <div class="d-flex align-items-center">
                                <div class="profile-image-container me-2">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ Auth::user()->avatar }}" alt="Profile" class="rounded-circle" width="32" height="32">
                                    @else
                                        <i class="fa-solid fa-user-circle fs-4" style="color: var(--primary-color);"></i>
                                    @endif
                                </div>
                                <span>{{ Auth::user()->username ?? Auth::user()->name }}</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="background-color: var(--card-bg); border-color: var(--border-color);">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.show') }}" style="color: var(--text-primary);">
                                    <i class="fa-solid fa-user me-2" style="color: var(--primary-color);"></i>
                                    Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}" style="color: var(--text-primary);">
                                    <i class="fa-solid fa-cog me-2" style="color: var(--primary-color);"></i>
                                    Editar Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/bookings" style="color: var(--text-primary);">
                                    <i class="fa-solid fa-ticket-alt me-2" style="color: var(--primary-color);"></i>
                                    Mis Reservas
                                </a>
                            </li>
                            <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center" style="color: var(--text-primary);">
                                        <i class="fa-solid fa-sign-out-alt me-2" style="color: var(--danger-color);"></i>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- Botón de autenticación simplificado -->
                    <li class="nav-item auth-guest">
                        <a href="/login" class="btn auth-btn" style="margin-right: 5px; background-color: var(--primary-color); color: white; border: none;">
                            <i class="fa-solid fa-sign-in-alt me-1"></i> Acceder
                        </a>
                    </li>

                    <!-- Menú de usuario JWT - Oculto por defecto -->
                    <li class="nav-item dropdown auth-user d-none">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--text-primary);">
                            <div class="d-flex align-items-center">
                                <div class="profile-image-container me-2">
                                    <img id="user-avatar" src="" alt="Profile" class="rounded-circle d-none" width="32" height="32">
                                    <i class="fa-solid fa-user-circle fs-4 avatar-placeholder" style="color: var(--primary-color);"></i>
                                </div>
                                <span id="username-display">Usuario</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="background-color: var(--card-bg); border-color: var(--border-color);">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/profile" style="color: var(--text-primary);">
                                    <i class="fa-solid fa-user me-2" style="color: var(--primary-color);"></i>
                                    Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/profile/edit" style="color: var(--text-primary);">
                                    <i class="fa-solid fa-cog me-2" style="color: var(--primary-color);"></i>
                                    Editar Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/bookings" style="color: var(--text-primary);">
                                    <i class="fa-solid fa-ticket-alt me-2" style="color: var(--primary-color);"></i>
                                    Mis Reservas
                                </a>
                            </li>
                            <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#" id="logout-btn" style="color: var(--text-primary);">
                                    <i class="fa-solid fa-sign-out-alt me-2" style="color: var(--danger-color);"></i>
                                    Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<style>
.profile-image-container {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 50%;
    background-color: var(--card-bg);
    border: 2px solid var(--primary-color);
}

.profile-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: background-color 0.2s ease;
}

.dropdown-item:hover {
    background-color: var(--primary-color);
    color: white !important;
}

.dropdown-item:hover i {
    color: white !important;
}

.navbar-logo {
    height: 40px;
    width: auto;
    object-fit: contain;
}
</style>

<script>
<!-- TODO: CAMBIAR TOTALMETE ESTA LOGICA -->
document.addEventListener('DOMContentLoaded', function() {
    if (!document.querySelector('.auth-user.d-none')) {
        function checkAuthentication() {
            const token = localStorage.getItem('auth_token');
            const authUser = localStorage.getItem('auth_user');

            if (token && authUser) {
                try {
                    const userData = JSON.parse(authUser);

                    document.querySelectorAll('.auth-guest').forEach(el => el.classList.add('d-none'));
                    document.querySelectorAll('.auth-user').forEach(el => el.classList.remove('d-none'));

                    const usernameDisplay = document.getElementById('username-display');
                    if (usernameDisplay) {
                        usernameDisplay.textContent = userData.username || userData.name || 'Usuario';
                    }

                    const userAvatar = document.getElementById('user-avatar');
                    const avatarPlaceholder = document.querySelector('.avatar-placeholder');
                    if (userAvatar && userData.avatar) {
                        userAvatar.src = userData.avatar;
                        userAvatar.classList.remove('d-none');
                        avatarPlaceholder.classList.add('d-none');
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

        function logout() {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');

            // Actualizar la interfaz
            checkAuthentication();

            // Redireccionar al inicio
            window.location.href = '/';
        }
        checkAuthentication();
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                logout();
            });
        }
        window.addEventListener('storage', function(e) {
            if (e.key === 'auth_token' || e.key === 'auth_user') {
                checkAuthentication();
            }
        });
    }
});
</script>
