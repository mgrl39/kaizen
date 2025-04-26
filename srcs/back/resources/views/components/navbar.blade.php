@php
$navItems = [
    ['url' => '/cinemas', 'icon' => 'building', 'text' => 'Cines'],
    ['url' => '/movies', 'icon' => 'film', 'text' => 'Películas']
];

$userMenu = [
    ['url' => '/profile', 'icon' => 'person', 'text' => 'Mi Perfil'],
    ['url' => '/bookings', 'icon' => 'ticket', 'text' => 'Mis Reservas'],
    ['divider' => true],
    ['form' => true, 'action' => '/logout', 'icon' => 'box-arrow-right', 'text' => 'Cerrar Sesión']
];
@endphp

<nav class="navbar navbar-expand-md navbar-dark fixed-top stripe-navbar">
    <div class="container">
        {{-- Brand --}}
        <a href="/" class="navbar-brand d-flex align-items-center">
            <span class="ms-2">Kaizen</span>
        </a>

        {{-- Toggler --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapsible content --}}
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center">
                @foreach($navItems as $item)
                <li class="nav-item">
                    <a href="{{ $item['url'] }}" class="nav-link">
                        <i class="bi bi-{{ $item['icon'] }} me-1"></i>{{ $item['text'] }}
                    </a>
                </li>
                @endforeach
                
                {{-- Auth menu --}}
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach($userMenu as $item)
                                @if(isset($item['divider']))
                                    <li><hr class="dropdown-divider"></li>
                                @elseif(isset($item['form']))
                                    <li>
                                        <form action="{{ $item['action'] }}" method="POST" class="d-inline w-100">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-{{ $item['icon'] }} me-1"></i>{{ $item['text'] }}
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <a class="dropdown-item" href="{{ $item['url'] }}">
                                            <i class="bi bi-{{ $item['icon'] }} me-1"></i>{{ $item['text'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li class="nav-item ms-2">
                        <a href="/login" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Acceder
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>