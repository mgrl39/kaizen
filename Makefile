# mgrl39
.DEFAULT_GOAL := help

GREEN=\033[0;32m
RED=\033[0;31m
BLUE=\033[0;34m
END=\033[0m

server:
	@php artisan serve

vendor/autoload.php:
	@echo "\033[31mError: Archivo vendor/autoload.php no encontrado\033[0m"
	@echo "\033[31mPor favor, ejecuta: composer install\033[0m"
	@exit 1

serve: 
	@echo -n "$(GREEN)" && hostname -I | tr ' ' '\n'
	@echo "$(END)"
	php artisan serve --host 0.0.0.0

install-composer:
	sudo apt update
	sudo apt install -y composer

install:
	composer install
	npm install
	cp .env.example .env
	php artisan key:generate

install-php-deps:
	sudo apt-get update
	sudo apt-get install -y php8.1 \
		php8.1-cli \
		php8.1-common \
		php8.1-curl \
		php8.1-mbstring \
		php8.1-xml \
		php8.1-zip \
		php8.1-mysql \
		php8.1-pgsql \
		php8.1-sqlite3 \
		php8.1-gd \
		php8.1-bcmath \
		php8.1-intl \
		php8.1-soap \
		php8.1-ldap \
		php8.1-imap \
		php8.1-redis \
		php8.1-memcached

clear:
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear

pull:
	git fetch && git pull origin $(shell git branch --show-current)

serve-bg:
	php artisan serve --host 0.0.0.0 &

# Esto esta mal creo. No me ha tirado como pensaba
kill-serve:
	pgrep -f "php artisan serve --host 0.0.0.0" | xargs kill -9

CLASS_TEST_NAME ?= $(shell bash -c 'read -p "Añade el nombre: " name; echo $$name')

create-test-unit:
	php artisan make:test $(CLASS_TEST_NAME) --unit

cache:
	php artisan view:cache

# O si solo quieres correr las pruebas de MovieApiTest:
# php artisan test --filter=MovieApiTest
test:
	php artisan test

lang:
	php artisan lang:publish

help:
	@echo -n "$(GREEN)"
	@echo "Uso del Makefile:"
	@echo "  make install		- Instala las dependencias del proyecto"
	@echo "  make install-composer	- Instala Composer globalmente"
	@echo "  make install-php-deps	- Instala todas las dependencias PHP necesarias"
	@echo "  make serve		- Inicia el servidor de desarrollo"
	@echo "  make serve-bg		- Inicia el servidor de desarrollo en segundo plano"
	@echo "  make clear		- Limpia la caché"
	@echo "  make kill-serve	- Mata el servidor de desarrollo"
	@echo "  make pull		- Actualiza el repositorio"
	@echo "  make create-test-unit	- Make test Unit"
	@echo "  make test		- Tirar testeos automaticos"
	@echo "  make cache		- Cachear las templates de blade"
	@echo "  make lang		- Publicar los archivos de internaciolizacion"

.PHONY: help install serve clear kill-serve serve-bg pull create-test-unit cache lang install-php-deps install-composer setup-db system-status

.PHONY: setup-db
setup-db:
	@echo "🚀 Iniciando configuración de MySQL y base de datos..."
	@chmod +x ./scripts/setup-database.sh
	@./scripts/setup-database.sh

.PHONY: test-system
test-system:
	@echo "🧪 Verificando configuración básica del sistema..."
	php artisan test --filter=GeneralSystemTest

# Comandos de sistema y construcción
deploy:
	git pull
	composer install --no-dev
	php artisan migrate --force
	php artisan optimize

# Atajos para comandos artisan comunes
migrate:
	php artisan migrate

fresh:
	php artisan migrate:fresh --seed

routes:
	php artisan route:list

# Comandos de desarrollo
dev:
	npm run dev

build:
	npm run build

#########################################################
# Comando para desarrollo con Vite
#########################################################
serve-dev:
	@echo "$(GREEN)Iniciando servidor de desarrollo con Vite...$(END)"
	@echo -n "$(GREEN)" && hostname -I | tr ' ' '\n'
	@echo "$(END)"
	@echo "$(BLUE)Presiona Ctrl+C para detener ambos servidores$(END)"
	@(trap 'kill 0' SIGINT; npm run dev & php artisan serve --host 0.0.0.0)
