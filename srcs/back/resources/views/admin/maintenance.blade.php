<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaizen Cinema - Mantenimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #9333ea;
            --primary-hover: #7e22ce;
            --bg-color: #f8fafc;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            --text-color: #1e293b;
            --border-color: #e2e8f0;
        }
        
        body {
            background: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            padding: 1rem;
            overflow-y: auto;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .nav-link {
            color: var(--text-color);
            padding: 0.5rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: rgba(147, 51, 234, 0.1);
            color: var(--primary-color);
        }

        .nav-link.active {
            background: var(--primary-color);
            color: #ffffff;
        }

        .section-title {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--primary-color);
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            padding-left: 1rem;
            font-weight: 600;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            color: var(--text-color);
            font-weight: 600;
        }

        .card-text {
            color: #64748b;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-hover);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-danger {
            background: #dc2626;
            border-color: #b91c1c;
        }

        .btn-danger:hover {
            background: #b91c1c;
            border-color: #991b1b;
        }

        .output-box {
            background: #f1f5f9;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1rem;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
            color: #334155;
        }

        .command-card {
            transition: transform 0.2s;
        }

        .command-card:hover {
            transform: translateY(-2px);
        }

        h2, h3, h4 {
            color: var(--text-color);
            font-weight: 600;
        }

        .bi {
            color: inherit;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="mb-4">
            <i class="bi bi-gear-fill me-2"></i>
            Panel Admin
        </h3>

        <div class="section-title">Base de Datos</div>
        <nav class="nav flex-column">
            <a class="nav-link" href="#database" data-section="database">
                <i class="bi bi-database me-2"></i>
                Base de Datos
            </a>
        </nav>

        <div class="section-title">Contenido</div>
        <nav class="nav flex-column">
            <a class="nav-link" href="#content" data-section="content">
                <i class="bi bi-film me-2"></i>
                Películas y Salas
            </a>
        </nav>

        <div class="section-title">Funciones</div>
        <nav class="nav flex-column">
            <a class="nav-link" href="#functions" data-section="functions">
                <i class="bi bi-calendar-event me-2"></i>
                Gestión de Funciones
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Database Section -->
        <section id="database" class="mb-5">
            <h2 class="mb-4">Base de Datos</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 command-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-database-fill-x me-2"></i>
                                Limpiar Base de Datos
                            </h5>
                            <p class="card-text">Elimina todos los datos de la base de datos.</p>
                            <button class="btn btn-danger" onclick="executeCommand('db_wipe')">
                                <i class="bi bi-trash me-2"></i>
                                Limpiar DB
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 command-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-database-fill-gear me-2"></i>
                                Migrar Base de Datos
                            </h5>
                            <p class="card-text">Reinicia la base de datos y ejecuta todas las migraciones.</p>
                            <button class="btn btn-primary" onclick="executeCommand('migrate_fresh')">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Ejecutar migrate:fresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Section -->
        <section id="content" class="mb-5">
            <h2 class="mb-4">Películas y Salas</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 command-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-grid-3x3-gap me-2"></i>
                                Poblar Salas
                            </h5>
                            <p class="card-text">Crea las salas de cine con sus configuraciones.</p>
                            <button class="btn btn-primary" onclick="executeCommand('seed_rooms')">
                                <i class="bi bi-plus-circle me-2"></i>
                                Ejecutar RoomSeeder
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 command-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-film me-2"></i>
                                Importar Películas
                            </h5>
                            <p class="card-text">Importa el catálogo de películas desde TMDB.</p>
                            <button class="btn btn-primary" onclick="executeCommand('import_movies')">
                                <i class="bi bi-cloud-download me-2"></i>
                                Importar Películas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Functions Section -->
        <section id="functions" class="mb-5">
            <h2 class="mb-4">Gestión de Funciones</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 command-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-calendar-event me-2"></i>
                                Generar Funciones
                            </h5>
                            <p class="card-text">Genera las funciones para la fecha actual.</p>
                            <button class="btn btn-primary" onclick="executeCommand('generate_functions')">
                                <i class="bi bi-calendar-plus me-2"></i>
                                Generar Funciones
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 command-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-bug me-2"></i>
                                Probar Funciones
                            </h5>
                            <p class="card-text">Prueba la API de funciones con ID 4.</p>
                            <button class="btn btn-primary" onclick="executeCommand('test_functions')">
                                <i class="bi bi-play-circle me-2"></i>
                                Probar API
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Output Box -->
        <section class="mt-5">
            <h4>
                <i class="bi bi-terminal me-2"></i>
                Salida del Comando
            </h4>
            <div id="output" class="output-box mt-3">
                Esperando comando...
            </div>
        </section>
    </div>

    <script>
        // Highlight active section in sidebar
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                e.target.classList.add('active');
            });
        });

        async function executeCommand(command) {
            const outputBox = document.getElementById('output');
            outputBox.textContent = 'Ejecutando comando...';

            try {
                const response = await fetch('/admin/maintenance/execute', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ command })
                });

                const data = await response.json();
                
                if (data.success) {
                    outputBox.textContent = data.output || 'Comando ejecutado con éxito';
                } else {
                    outputBox.textContent = data.error || 'Error al ejecutar el comando';
                }
            } catch (error) {
                outputBox.textContent = 'Error: ' + error.message;
            }
        }

        // Scroll to section when clicking sidebar links
        document.querySelectorAll('a[data-section]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const sectionId = e.target.getAttribute('data-section');
                document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Set initial active section based on scroll position
        function updateActiveSection() {
            const sections = document.querySelectorAll('section[id]');
            let currentSection = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (window.pageYOffset >= sectionTop - 100) {
                    currentSection = section.getAttribute('id');
                }
            });

            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-section') === currentSection) {
                    link.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', updateActiveSection);
        updateActiveSection();
    </script>
</body>
</html> 