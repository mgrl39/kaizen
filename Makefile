.DEFAULT_GOAL := help

# Colores
C=\033[0;32m
Y=\033[0;33m
E=\033[0m

# Rutas
BACK=srcs/back
FRONT=srcs/front
CONF=srcs/conf

# Comandos básicos
all: db-start install run

install:
	@echo "$(C)Instalando dependencias...$(E)"
	@cd $(BACK) && composer install
	@cd $(FRONT) && npm install
	@cd $(BACK) && cp .env.example .env
	@cd $(BACK) && php artisan key:generate
	@echo "$(Y)Recuerda configurar .env con los datos de PostgreSQL$(E)"

run:
	@echo "$(C)IPs: $(E)" && hostname -I
	@echo "$(C)Frontend: http://IP:5173 | Backend: http://IP:8000$(E)"
	@(trap 'kill 0' INT; cd $(FRONT) && npm run dev -- --host 0.0.0.0 & cd $(BACK) && php artisan serve --host 0.0.0.0 & wait)

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
	@echo "$(Y)¿Deseas reiniciar completamente la base de datos? [s/N]: $(E)\c"
	@read confirm; \
	if [ "$$confirm" = "s" ] || [ "$$confirm" = "S" ]; then \
		cd $(BACK) && php artisan migrate:fresh --seed; \
	else \
		echo "$(C)Operación cancelada.$(E)"; \
	fi

clear:
	@cd $(BACK) && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

# Comandos para contenedores PostgreSQL
db-start:
	@echo "$(C)Iniciando contenedor PostgreSQL...$(E)"
	@cd $(CONF)/postgres && ./pg-podman.sh start

db-stop:
	@echo "$(C)Deteniendo contenedor PostgreSQL...$(E)"
	@cd $(CONF)/postgres && ./pg-podman.sh stop

db-logs:
	@echo "$(C)Mostrando logs del contenedor PostgreSQL...$(E)"
	@cd $(CONF)/postgres && ./pg-podman.sh logs

db-shell:
	@echo "$(C)Conectando al shell de PostgreSQL...$(E)"
	@cd $(CONF)/postgres && ./pg-podman.sh shell

db-reset:
	@echo "$(Y)¡ADVERTENCIA! Esto eliminará TODOS los datos. ¿Continuar? [s/N]: $(E)\c"
	@read confirm; \
	if [ "$$confirm" = "s" ] || [ "$$confirm" = "S" ]; then \
		cd $(CONF)/postgres && ./pg-podman.sh reset; \
		echo "$(C)Base de datos reiniciada. Ejecutando migraciones...$(E)"; \
		cd $(BACK) && php artisan migrate:fresh --seed; \
	else \
		echo "$(C)Operación cancelada.$(E)"; \
	fi

full-start: db-start back front
	@echo "$(C)Sistema Kaizen Cinema completamente iniciado!$(E)"

help:
	@echo "$(C)Comandos:$(E)"
	@echo "  make install    - Instalar dependencias"
	@echo "  make run        - Ejecutar front+back (externos)"
	@echo "  make front      - Solo frontend"
	@echo "  make back       - Solo backend"
	@echo "  make build      - Construir para producción"
	@echo "  make db         - Ejecutar migraciones"
	@echo "  make fresh      - Resetear DB con seeds"
	@echo "  make clear      - Limpiar caché"
	@echo ""
	@echo "$(C)Comandos PostgreSQL:$(E)"
	@echo "  make db-start   - Iniciar contenedor PostgreSQL"
	@echo "  make db-stop    - Detener contenedor PostgreSQL"
	@echo "  make db-logs    - Ver logs de PostgreSQL"
	@echo "  make db-shell   - Abrir shell PostgreSQL"
	@echo "  make db-reset   - Resetear completamente la base de datos"
	@echo ""
	@echo "$(C)Comandos combinados:$(E)"
	@echo "  make all        - Iniciar DB, instalar y ejecutar todo"
	@echo "  make full-start - Iniciar DB, backend y frontend"

.PHONY: all install run front back build db fresh clear help db-start db-stop db-logs db-shell db-reset full-start