# Configuración de Infraestructura con Podman

Esta carpeta contiene la configuración de infraestructura para el proyecto Kaizen Cinema usando Podman.

## PostgreSQL con Podman

Para gestionar la base de datos:

```bash
# Usar el script de administración
cd postgres
./pg-podman.sh start   # Iniciar PostgreSQL
./pg-podman.sh stop    # Detener PostgreSQL
./pg-podman.sh status  # Ver estado
./pg-podman.sh shell   # Abrir shell SQL
./pg-podman.sh reset   # Reiniciar y limpiar datos (¡Cuidado!)
```

## Focalboard (Gestión de Proyectos)

Focalboard es una herramienta de gestión visual que combina tableros Kanban, listas y más:

```bash
# Usar el script de administración
cd focalboard
./fb-podman.sh start    # Iniciar Focalboard
./fb-podman.sh stop     # Detener Focalboard
./fb-podman.sh status   # Ver estado
./fb-podman.sh logs     # Ver logs
./fb-podman.sh reset    # Reiniciar (¡Cuidado!)
./fb-podman.sh info     # Mostrar información de acceso
```

Acceso a Focalboard:

- URL: http://localhost:8090
- Sin credenciales iniciales (crear usuario en el primer acceso)

### Configuración recomendada para planificación de sprints:

1. Crear un tablero "Sprint Planning"
2. Configurar columnas para: Backlog, To Do, In Progress, Review, Done
3. Crear una tarjeta para cada tarea con:

   - Título descriptivo
   - Descripción y criterios de aceptación
   - Etiquetas para prioridades (Alta, Media, Baja)
   - Asignados
   - Fecha límite

4. Usar vista Kanban para seguimiento diario
5. Usar vista Calendario para planificación semanal
6. Usar vista de Tabla para tener una visión general

## Conexión a la base de datos PostgreSQL

- **Host**: localhost
- **Puerto**: 5432
- **Base de datos**: kaizendb
- **Usuario**: kaizen
- **Contraseña**: kaizen_secure_password

## Configuración de Laravel

Asegúrate de que tu archivo `.env` en los proyectos Laravel tenga:
