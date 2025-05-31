#!/bin/bash

RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[1;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}👤 Configuración de entorno de usuario (Node.js + NVM)${NC}"

if [ "$EUID" -eq 0 ]; then
    echo -e "${RED}✖ No ejecutes este script como root. Usa: ./init_user.sh${NC}"
    exit 1
fi

export NVM_DIR="$HOME/.nvm"
mkdir -p "$NVM_DIR"

if [ ! -s "$NVM_DIR/nvm.sh" ]; then
    echo -e "${YELLOW}➤ Instalando NVM...${NC}"
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash
fi

# Cargar NVM
export NVM_DIR="$HOME/.nvm"
. "$NVM_DIR/nvm.sh"

# Instalar Node.js v18.20.8 si no está
if ! nvm ls 2>/dev/null | grep -q "v18.20.8"; then
    echo -e "${YELLOW}➤ Instalando Node.js v18.20.8...${NC}"
    nvm install 18.20.8
fi

nvm use 18.20.8
nvm alias default 18.20.8

echo -e "${GREEN}✔ Node.js activo: $(node -v), npm: $(npm -v)${NC}"

# Añadir nvm al bashrc si no está
if ! grep -q 'nvm.sh' "$HOME/.bashrc"; then
    echo -e "${YELLOW}➤ Configurando bashrc para cargar NVM...${NC}"
    echo '' >> "$HOME/.bashrc"
    echo 'export NVM_DIR="$HOME/.nvm"' >> "$HOME/.bashrc"
    echo '[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"' >> "$HOME/.bashrc"
fi

echo -e "${GREEN}✅ Entorno del usuario configurado. Ahora puedes ejecutar: 'make all'${NC}"

