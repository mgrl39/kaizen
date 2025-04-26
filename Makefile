.DEFAULT_GOAL := help

# Colores
C=\033[0;32m
E=\033[0m

# Rutas
BACK=srcs/back
FRONT=srcs/front

# Comandos básicos
all: install run

install:
	@echo "$(C)Instalando dependencias...$(E)"
	@cd $(BACK) && composer install
	@cd $(FRONT) && npm install
	@cd $(BACK) && cp .env.example .env
	@cd $(BACK) && php artisan key:generate

run:
	@echo "$(C)IPs: $(E)" && hostname -I
	@echo "$(C)Frontend: http://IP:5173 | Backend: http://IP:8000$(E)"
	@(trap 'kill 0' SIGINT; cd $(FRONT) && npm run dev -- --host 0.0.0.0 & cd $(BACK) && php artisan serve --host 0.0.0.0 & wait)

front:
	@echo "$(C)IPs: $(E)" && hostname -I
	@cd $(FRONT) && npm run dev -- --host 0.0.0.0

back:
	@echo "$(C)IPs: $(E)" && hostname -I
	@cd $(BACK) && php artisan serve --host 0.0.0.0

build:
	@cd $(FRONT) && npm run build
	@cd $(BACK) && php artisan optimize

db:
	@cd $(BACK) && php artisan migrate

fresh:
	@cd $(BACK) && php artisan migrate:fresh --seed

clear:
	@cd $(BACK) && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

help:
	@echo "$(C)Comandos:$(E)"
	@echo "  make install - Instalar dependencias"
	@echo "  make run     - Ejecutar front+back (externos)"
	@echo "  make front   - Solo frontend"
	@echo "  make back    - Solo backend"
	@echo "  make build   - Construir para producción"
	@echo "  make db      - Ejecutar migraciones"
	@echo "  make fresh   - Resetear DB con seeds"
	@echo "  make clear   - Limpiar caché"

.PHONY: all install run front back build db fresh clear help