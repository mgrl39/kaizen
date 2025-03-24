<!DOCTYPE html>
<html>
<head>
    <title>Listado de URLs</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5 animate__animated animate__fadeIn">
        <h1 class="mb-4 border-bottom pb-2">URLs de la aplicación</h1>
        
        <div class="list-group mb-4">
            @foreach($routes as $route)
                <div class="list-group-item list-group-item-action animate__animated animate__fadeInUp">
                    @if(strpos($route['method'], 'GET') !== false)
                        <i class="fas fa-arrow-down text-success"></i>
                    @elseif(strpos($route['method'], 'POST') !== false)
                        <i class="fas fa-plus text-primary"></i>
                    @elseif(strpos($route['method'], 'PUT') !== false || strpos($route['method'], 'PATCH') !== false)
                        <i class="fas fa-edit text-warning"></i>
                    @elseif(strpos($route['method'], 'DELETE') !== false)
                        <i class="fas fa-trash text-danger"></i>
                    @endif
                    
                    <strong class="
                        @if(strpos($route['method'], 'GET') !== false) text-success
                        @elseif(strpos($route['method'], 'POST') !== false) text-primary
                        @elseif(strpos($route['method'], 'PUT') !== false || strpos($route['method'], 'PATCH') !== false) text-warning
                        @elseif(strpos($route['method'], 'DELETE') !== false) text-danger
                        @endif
                    ">
                        {{ $route['method'] }}
                    </strong>: 
                    <span class="ms-2">{{ $route['url'] }}</span>
                </div>
            @endforeach
        </div>
        
        <a href="/" class="btn btn-primary animate__animated animate__fadeInUp">
            <i class="fas fa-home"></i> Volver al inicio
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 