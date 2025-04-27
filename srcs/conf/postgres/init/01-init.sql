-- Crear extensiones útiles
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pg_trgm";

-- Asegurar permisos correctos
ALTER USER kaizen WITH SUPERUSER;

-- Configuración de la zona horaria
ALTER DATABASE kaizendb SET timezone TO 'Europe/Madrid'; 