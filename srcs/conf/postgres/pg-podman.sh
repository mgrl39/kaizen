#!/bin/bash

# Colores para salida
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

CONTAINER_NAME="kaizen_postgres"
VOLUME_NAME="kaizen_pgdata"
POSTGRES_PORT="5433"  # Cambiado a 5433 ya que 5432 está en uso

# Verificar si Podman está instalado
check_podman() {
  if ! command -v podman &> /dev/null; then
    echo -e "${RED}Error: Podman no está instalado o no está en el PATH${NC}"
    echo -e "Por favor instale Podman antes de continuar"
    exit 1
  fi
}

# Función para mostrar uso
show_usage() {
  echo -e "${YELLOW}Uso:${NC} $0 [comando]"
  echo "Comandos:"
  echo "  start      - Inicia el contenedor PostgreSQL"
  echo "  stop       - Detiene el contenedor PostgreSQL"
  echo "  restart    - Reinicia el contenedor PostgreSQL"
  echo "  status     - Muestra el estado del contenedor"
  echo "  logs       - Muestra los logs del contenedor"
  echo "  shell      - Abre una shell psql en el contenedor"
  echo "  dump       - Genera un dump del esquema de la base de datos"
  echo "  reset      - ¡PELIGRO! Elimina todos los datos y reinicia"
  exit 1
}

# Comprobar si se proporcionó un comando
if [ $# -eq 0 ]; then
  show_usage
fi

# Verificar si el volumen existe o crearlo
ensure_volume() {
  if ! podman volume exists $VOLUME_NAME; then
    echo -e "${GREEN}Creando volumen $VOLUME_NAME...${NC}"
    podman volume create $VOLUME_NAME
  fi
}

# Verificar Podman antes de ejecutar cualquier comando
check_podman

# Ejecutar el comando
case "$1" in
  start)
    echo -e "${GREEN}Iniciando PostgreSQL con Podman...${NC}"
    ensure_volume
    
    # Verificar si el contenedor ya existe
    if podman container exists $CONTAINER_NAME; then
      echo -e "${YELLOW}El contenedor ya existe, iniciándolo...${NC}"
      podman start $CONTAINER_NAME
    else
      echo -e "${GREEN}Creando y ejecutando nuevo contenedor...${NC}"
      podman run -d \
        --name $CONTAINER_NAME \
        -e POSTGRES_DB=kaizendb \
        -e POSTGRES_USER=kaizen \
        -e POSTGRES_PASSWORD=kaizen \
        -e PGDATA=/var/lib/postgresql/data/pgdata \
        -v $VOLUME_NAME:/var/lib/postgresql/data \
        -v ./init:/docker-entrypoint-initdb.d \
        -p $POSTGRES_PORT:5432 \
        docker.io/library/postgres:15
    fi
    ;;
    
  stop)
    echo -e "${YELLOW}Deteniendo PostgreSQL...${NC}"
    podman stop $CONTAINER_NAME
    ;;
    
  restart)
    echo -e "${YELLOW}Reiniciando PostgreSQL...${NC}"
    podman restart $CONTAINER_NAME
    ;;
    
  status)
    podman ps -a -f name=$CONTAINER_NAME
    ;;
    
  logs)
    podman logs -f $CONTAINER_NAME
    ;;
    
  shell)
    echo -e "${GREEN}Conectando a PostgreSQL...${NC}"
    podman exec -it $CONTAINER_NAME psql -U kaizen -d kaizendb
    ;;
    
  dump)
    echo -e "${GREEN}Generando dump del esquema SQL...${NC}"
    # Verificar si el contenedor está en ejecución
    if ! podman container exists $CONTAINER_NAME || [ "$(podman inspect -f '{{.State.Running}}' $CONTAINER_NAME)" != "true" ]; then
      echo -e "${RED}Error: El contenedor PostgreSQL no está en ejecución.${NC}"
      echo -e "Inicie el contenedor primero con: $0 start"
      exit 1
    fi
    
    # Crear directorio para dumps si no existe
    DUMP_DIR="./dumps"
    mkdir -p $DUMP_DIR
    
    # Generar nombre de archivo con fecha y hora
    TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
    DUMP_FILE="$DUMP_DIR/schema_$TIMESTAMP.sql"
    
    # Ejecutar pg_dump y guardar el resultado
    echo -e "${YELLOW}Ejecutando pg_dump...${NC}"
    podman exec $CONTAINER_NAME pg_dump -U kaizen -d kaizendb --schema-only > $DUMP_FILE
    
    # Verificar si el dump se generó correctamente
    if [ $? -eq 0 ] && [ -f "$DUMP_FILE" ]; then
      echo -e "${GREEN}Dump del esquema generado correctamente en:${NC} $DUMP_FILE"
      # Crear un enlace simbólico al último dump
      ln -sf "$DUMP_FILE" "$DUMP_DIR/schema_latest.sql"
      echo -e "${GREEN}También disponible como:${NC} $DUMP_DIR/schema_latest.sql"
    else
      echo -e "${RED}Error al generar el dump del esquema.${NC}"
      exit 1
    fi
    ;;
    
  reset)
    echo -e "${RED}¡ADVERTENCIA! Esto eliminará TODOS los datos de la base de datos.${NC}"
    read -p "¿Estás seguro? (s/N): " confirm
    if [[ $confirm == [sS] ]]; then
      echo -e "${RED}Eliminando contenedor y volumen...${NC}"
      podman stop $CONTAINER_NAME || true
      podman rm $CONTAINER_NAME || true
      podman volume rm $VOLUME_NAME || true
      
      echo -e "${GREEN}Recreando...${NC}"
      ensure_volume
      podman run -d \
        --name $CONTAINER_NAME \
        -e POSTGRES_DB=kaizendb \
        -e POSTGRES_USER=kaizen \
        -e POSTGRES_PASSWORD=kaizen \
        -e PGDATA=/var/lib/postgresql/data/pgdata \
        -v $VOLUME_NAME:/var/lib/postgresql/data \
        -v ./init:/docker-entrypoint-initdb.d \
        -p $POSTGRES_PORT:5432 \
        docker.io/library/postgres:15
        
      echo -e "${GREEN}PostgreSQL reiniciado con una base de datos limpia.${NC}"
    else
      echo "Operación cancelada."
    fi
    ;;
    
  *)
    show_usage
    ;;
esac

exit 0