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
