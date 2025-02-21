# mgrl39
.DEFAULT_GOAL := help

server:
	@php artisan serve

vendor/autoload.php:
	@echo "\033[31mError: Archivo vendor/autoload.php no encontrado\033[0m"
	@echo "\033[31mPor favor, ejecuta: composer install\033[0m"
	@exit 1

serve: 
	php artisan serve

install:
	composer install

clear:
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear

help:
	@echo "Uso del Makefile:"
	@echo "  make install    - Instala las dependencias del proyecto"
	@echo "  make serve     - Inicia el servidor de desarrollo"
	@echo "  make clear     - Limpia la caché"

.PHONY: help install serve clear
