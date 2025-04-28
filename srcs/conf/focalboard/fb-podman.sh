#!/bin/bash

# Colores para salida
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables de configuración
CONTAINER_NAME="kaizen_focalboard"
IMAGE_NAME="docker.io/mattermost/focalboard:latest"
VOLUME_NAME="kaizen_focalboard_data"
PORT="8090:8000"

# Función para mostrar uso
show_usage() {
  echo -e "${YELLOW}Uso:${NC} $0 [comando]"
  echo "Comandos:"
  echo "  start      - Inicia Focalboard"
  echo "  stop       - Detiene Focalboard"
  echo "  restart    - Reinicia Focalboard"
  echo "  status     - Muestra el estado del contenedor"
  echo "  logs       - Muestra los logs de Focalboard"
  echo "  reset      - ¡PELIGRO! Elimina todos los datos y reinicia"
  echo "  info       - Muestra información de acceso"
  exit 1
}

# Mostrar información de acceso
show_info() {
  echo -e "${BLUE}=== Información de Focalboard ===${NC}"
  echo -e "${GREEN}URL de acceso:${NC} http://localhost:8090"
  echo
  echo -e "${BLUE}=== Guía rápida ===${NC}"
  echo -e "1. Crea un nuevo workspace en la primera visita"
  echo -e "2. Crea un tablero de tipo 'Sprint Planning'"
  echo -e "3. Define tus sprints semanales como listas individuales"
  echo -e "4. Añade tarjetas para cada tarea"
  echo -e "5. Usa etiquetas para prioridades y categorías"
  echo -e "6. Configura la vista Kanban para visualizar el flujo de trabajo"
  echo -e "7. Usa la vista Calendario para planificar cada semana"
}

# Verificar si el volumen existe o crearlo
ensure_volume() {
  if ! podman volume exists $VOLUME_NAME; then
    echo -e "${GREEN}Creando volumen $VOLUME_NAME...${NC}"
    podman volume create $VOLUME_NAME
  fi
}

# Comprobar si se proporcionó un comando
if [ $# -eq 0 ]; then
  show_usage
fi

# Ejecutar el comando
case "$1" in
  start)
    echo -e "${GREEN}Iniciando Focalboard...${NC}"
    ensure_volume
    
    # Verificar si el contenedor ya existe
    if podman container exists $CONTAINER_NAME; then
      echo -e "${YELLOW}El contenedor ya existe, iniciándolo...${NC}"
      podman start $CONTAINER_NAME
    else
      echo -e "${GREEN}Creando y ejecutando nuevo contenedor...${NC}"
      podman run -d \
        --name $CONTAINER_NAME \
        -v $VOLUME_NAME:/data \
        -e VIRTUAL_HOST=localhost \
        -e VIRTUAL_PORT=8000 \
        -e VIRTUAL_ROOT=/ \
        -p $PORT \
        --restart unless-stopped \
        $IMAGE_NAME
    fi
    
    echo -e "${GREEN}Focalboard está disponible en http://localhost:8090${NC}"
    show_info
    ;;
  stop)
    echo -e "${YELLOW}Deteniendo Focalboard...${NC}"
    podman stop $CONTAINER_NAME
    ;;
  restart)
    echo -e "${YELLOW}Reiniciando Focalboard...${NC}"
    podman restart $CONTAINER_NAME
    ;;
  status)
    podman ps -a -f name=$CONTAINER_NAME
    ;;
  logs)
    podman logs -f $CONTAINER_NAME
    ;;
  reset)
    echo -e "${RED}¡ADVERTENCIA! Esto eliminará TODOS los datos de Focalboard.${NC}"
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
        -v $VOLUME_NAME:/data \
        -e VIRTUAL_HOST=localhost \
        -e VIRTUAL_PORT=8000 \
        -e VIRTUAL_ROOT=/ \
        -p $PORT \
        --restart unless-stopped \
        $IMAGE_NAME
        
      echo -e "${GREEN}Focalboard reiniciado con una instancia limpia.${NC}"
      show_info
    else
      echo "Operación cancelada."
    fi
    ;;
  info)
    show_info
    ;;
  *)
    show_usage
    ;;
esac

exit 0
