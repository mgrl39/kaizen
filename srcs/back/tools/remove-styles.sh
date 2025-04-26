#!/bin/bash

# Colores para los mensajes
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== Eliminando todos los estilos CSS de archivos Blade ===${NC}"

# Encuentra todos los archivos .blade.php
FILES=$(find resources/views -name "*.blade.php")
COUNT=0

for file in $FILES; do
  # Elimina etiquetas <style> completas y su contenido
  sed -i '/<style/,/<\/style>/d' "$file"
  
  # Elimina etiquetas <link> que se refieren a hojas de estilo
  sed -i '/<link.*rel=["'"'"']stylesheet["'"'"']/d' "$file"
  
  # Elimina atributos style inline
  sed -i 's/ style=["'"'"'][^"'"'"']*["'"'"']//g' "$file"
  
  # Elimina clases CSS (opcional, descomenta si también quieres eliminar clases)
  # sed -i 's/ class=["'"'"'][^"'"'"']*["'"'"']//g' "$file"
  
  echo -e "${GREEN}Eliminados estilos de:${NC} $file"
  COUNT=$((COUNT+1))
done

echo -e "${YELLOW}=== Finalizado ===${NC}"
echo -e "${GREEN}Se han procesado $COUNT archivos Blade${NC}" 