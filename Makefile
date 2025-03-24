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

install:
	composer install

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

help:
	@echo -n "$(GREEN)"
	@echo "Uso del Makefile:"
	@echo "  make install		- Instala las dependencias del proyecto"
	@echo "  make serve		- Inicia el servidor de desarrollo"
	@echo "  make serve-bg		- Inicia el servidor de desarrollo en segundo plano"
	@echo "  make clear		- Limpia la caché"
	@echo "  make kill-serve	- Mata el servidor de desarrollo"
	@echo "  make pull		- Actualiza el repositorio"
	@echo "  make create-test-unit	- Make test Unit"
	@echo "  make test		- Tirar testeos automaticos"
	@echo "  make cache		- Cachear las templates de blade"

.PHONY: help install serve clear kill-serve serve-bg pull create-test-unit cache
