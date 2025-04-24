<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <div class="container">
        <a href="/" class="navbar-brand d-flex align-items-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Kaizen" height="30">
            <span class="ms-2">Kaizen</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                @foreach([
                    ['url' => '/cinemas', 'icon' => 'bi-building', 'text' => 'Cines'],
                    ['url' => '/movies', 'icon' => 'bi-film', 'text' => 'Películas']
                ] as $item)
                    <li class="nav-item">
                        <a href="{{ $item['url'] }}" class="nav-link">
                            <i class="bi {{ $item['icon'] }} me-1"></i>{{ $item['text'] }}
                        </a>
                    </li>
                @endforeach

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach([
                                ['url' => '/profile', 'icon' => 'bi-person', 'text' => 'Mi Perfil'],
                                ['url' => '/bookings', 'icon' => 'bi-ticket', 'text' => 'Mis Reservas']
                            ] as $item)
                                <li>
                                    <a class="dropdown-item" href="{{ $item['url'] }}">
                                        <i class="bi {{ $item['icon'] }} me-1"></i>{{ $item['text'] }}
                                    </a>
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="/logout" method="POST" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item auth-guest">
                        <a href="/login" class="btn btn-primary btn-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Acceder
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('js/auth.js') }}"></script>
