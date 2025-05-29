#!/bin/bash

# Archivo de log
LOGFILE="instalacion_kaizen.log"

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[1;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # Sin color

echo -e "${BLUE}🔧 Instalación de dependencias para: kaizen${NC}"

# Verifica que no se ejecute como root
if [ "$EUID" -eq 0 ]; then
    echo -e "${RED}✖ No ejecutes este script como root.${NC}"
    exit 1
fi

# Función para instalar un paquete si no está instalado
install_if_missing() {
    local pkg=$1
    local cmd=$2

    if ! command -v "$cmd" >/dev/null 2>&1; then
        echo -e "${YELLOW}➤ Instalando $pkg...${NC}"
        sudo apt install -y "$pkg" >> "$LOGFILE" 2>&1
    else
        echo -e "${GREEN}✔ $pkg ya está instalado.${NC}"
    fi
}

# Actualiza los índices de paquetes
echo -e "${YELLOW}➤ Actualizando repositorios...${NC}"
sudo apt update >> "$LOGFILE" 2>&1 &
wait

# Lista de herramientas necesarias (paquete, comando para verificar)
install_if_missing make make
install_if_missing git git
install_if_missing curl curl
install_if_missing npm npm
install_if_missing nodejs node
install_if_missing build-essential gcc

echo -e "${GREEN}✅ Instalación completa. Revisa '${LOGFILE}' si hubo errores.${NC}"

