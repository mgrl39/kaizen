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

## Ejecutar manualmente con Podman

```bash
# Crear volumen
podman volume create kaizen_pgdata

# Iniciar PostgreSQL
podman run -d --name kaizen_postgres \
  -e POSTGRES_DB=kaizendb \
  -e POSTGRES_USER=kaizen \
  -e POSTGRES_PASSWORD=kaizen_secure_password \
  -v kaizen_pgdata:/var/lib/postgresql/data \
  -p 5432:5432 \
  postgres:15
```

## Conexión a la base de datos

- **Host**: localhost
- **Puerto**: 5432
- **Base de datos**: kaizendb
- **Usuario**: kaizen
- **Contraseña**: kaizen_secure_password

## Configuración de Laravel

Asegúrate de que tu archivo `.env` en los proyectos Laravel tenga:
