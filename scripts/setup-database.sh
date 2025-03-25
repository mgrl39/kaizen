#!/bin/bash

# Colores para los mensajes
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}🔧 Instalando MySQL Server...${NC}"
sudo apt update
sudo apt install -y mysql-server

# Iniciar el servicio MySQL
echo -e "${YELLOW}🚀 Iniciando servicio MySQL...${NC}"
sudo systemctl start mysql
sudo systemctl enable mysql

# Configurar MySQL para permitir conexiones remotas
echo -e "${YELLOW}📝 Configurando MySQL para conexiones remotas...${NC}"
sudo sed -i 's/bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf

# Reiniciar MySQL para aplicar cambios
echo -e "${YELLOW}🔄 Reiniciando MySQL...${NC}"
sudo systemctl restart mysql

# Crear usuario root con acceso remoto y establecer contraseña
echo -e "${YELLOW}👤 Configurando usuarios y permisos...${NC}"
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';"
sudo mysql -e "CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'root';"
sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;"
sudo mysql -e "FLUSH PRIVILEGES;"

# Crear la base de datos
echo -e "${YELLOW}🗄️ Creando base de datos...${NC}"
sudo mysql -e "CREATE DATABASE IF NOT EXISTS cinedb;"

# Configurar .env
echo -e "${YELLOW}⚙️ Configurando archivo .env...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Actualizar configuración de base de datos en .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=cinedb/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=root/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=root/' .env

# Instalar dependencias de Composer si no están instaladas
if [ ! -d "vendor" ]; then
    echo -e "${YELLOW}📦 Instalando dependencias de Composer...${NC}"
    composer install
fi

# Generar clave de aplicación si no existe
if ! grep -q "^APP_KEY=" .env || grep -q "^APP_KEY=$" .env; then
    echo -e "${YELLOW}🔑 Generando clave de aplicación...${NC}"
    php artisan key:generate
fi

# Ejecutar migraciones
echo -e "${YELLOW}🔄 Ejecutando migraciones...${NC}"
php artisan migrate:fresh --seed

# Configurar firewall
echo -e "${YELLOW}🛡️ Configurando firewall...${NC}"
sudo ufw allow 3306

# Verificar estado del servicio
echo -e "${YELLOW}✅ Verificando estado de MySQL...${NC}"
sudo systemctl status mysql --no-pager

echo -e "${GREEN}✨ Configuración completada!${NC}"
echo -e "${GREEN}📝 Credenciales de conexión:${NC}"
echo -e "Host: 0.0.0.0"
echo -e "Puerto: 3306"
echo -e "Usuario: root"
echo -e "Contraseña: root"
echo -e "Base de datos: cinedb"

# Mostrar información de conexión externa
echo -e "${GREEN}🌐 Para conectarte desde otro equipo:${NC}"
echo -e "mysql -h <IP_DEL_SERVIDOR> -u root -p"
echo -e "Contraseña: root" 