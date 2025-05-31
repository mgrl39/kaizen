#!/bin/bash

LOGFILE="instalacion_kaizen.log"

RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[1;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}🔧 Instalación de dependencias para: kaizen (root)${NC}"

# Requiere root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}✖ Este script debe ejecutarse como root. Usa: sudo ./init.sh${NC}"
    exit 1
fi

echo "===== Instalación iniciada: $(date) =====" >> "$LOGFILE"

install_if_missing() {
    local pkg=$1
    local cmd=$2
    if ! command -v "$cmd" >/dev/null 2>&1; then
        echo -e "${YELLOW}➤ Instalando $pkg...${NC}"
        apt install -y "$pkg" >> "$LOGFILE" 2>&1
    else
        echo -e "${GREEN}✔ $pkg ya está instalado.${NC}"
    fi
}

# Actualizar repos
echo -e "${YELLOW}➤ Actualizando repositorios...${NC}"
apt update >> "$LOGFILE" 2>&1

# Herramientas básicas
install_if_missing make make
install_if_missing git git
install_if_missing curl curl
install_if_missing build-essential gcc
install_if_missing unzip unzip

# PHP y extensiones
install_if_missing php php
install_if_missing php-cli php
install_if_missing php-mbstring php
phpenmod mbstring
install_if_missing php-xml php
install_if_missing php-pgsql php

# PostgreSQL client (v14)
echo -e "${YELLOW}➤ Instalando cliente de PostgreSQL 14...${NC}"
apt install -y postgresql-client-14 >> "$LOGFILE" 2>&1
if command -v psql >/dev/null 2>&1; then
    echo -e "${GREEN}✔ psql instalado correctamente (v$(psql --version))${NC}"
else
    echo -e "${RED}✖ No se pudo instalar 'psql'. Verifica manualmente.${NC}"
fi

# Composer
if ! command -v composer >/dev/null 2>&1; then
    echo -e "${YELLOW}➤ Instalando Composer...${NC}"
    curl -sS https://getcomposer.org/installer | php >> "$LOGFILE" 2>&1
    mv composer.phar /usr/local/bin/composer
else
    echo -e "${GREEN}✔ Composer ya está instalado.${NC}"
fi

# Podman
install_if_missing podman podman

# Fix explícito para ext-curl
echo -e "${YELLOW}➤ Verificando extensión PHP 'curl'...${NC}"
apt install -y --reinstall php-curl >> "$LOGFILE" 2>&1
phpenmod curl

if php -m | grep -q '^curl$'; then
    echo -e "${GREEN}✔ Extensión PHP 'curl' activada correctamente.${NC}"
else
    echo -e "${RED}✖ La extensión 'curl' NO está activa. Usa 'phpenmod curl && systemctl restart apache2' si usas Apache.${NC}"
fi

echo -e "${GREEN}✅ Sistema listo. Ahora ejecuta como usuario normal: './node.sh'${NC}"
