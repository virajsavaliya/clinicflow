.PHONY: help up down restart logs shell db-shell seed migrate cache-clear

help:
	@echo "ClinicFlow Docker Commands"
	@echo "=========================="
	@echo ""
	@echo "Container Management:"
	@echo "  make up              - Start all containers"
	@echo "  make down            - Stop all containers"
	@echo "  make restart         - Restart all containers"
	@echo "  make logs            - View logs from all services"
	@echo ""
	@echo "Shell Access:"
	@echo "  make shell           - Access PHP container bash shell"
	@echo "  make db-shell        - Access MySQL command line"
	@echo ""
	@echo "Database:"
	@echo "  make migrate         - Run database migrations"
	@echo "  make seed            - Seed database with sample data"
	@echo "  make fresh           - Drop all tables and migrate fresh"
	@echo ""
	@echo "Cache:"
	@echo "  make cache-clear     - Clear all application cache"
	@echo ""
	@echo "Utilities:"
	@echo "  make tinker          - Access Laravel Tinker REPL"
	@echo "  make install         - Install Composer dependencies"
	@echo "  make key-gen         - Generate new APP_KEY"
	@echo "  make build           - Rebuild Docker images"
	@echo ""

up:
	docker-compose up -d
	@echo "✓ Containers started"

down:
	docker-compose down
	@echo "✓ Containers stopped"

restart:
	docker-compose restart
	@echo "✓ Containers restarted"

logs:
	docker-compose logs -f

shell:
	docker-compose exec php bash

db-shell:
	docker-compose exec mysql mysql -u clinicflow -pclinicflow123 clinicflow

migrate:
	docker-compose exec php php artisan migrate --force
	@echo "✓ Migrations completed"

seed:
	docker-compose exec php php seed-simple.php
	@echo "✓ Database seeded"

fresh:
	docker-compose exec php php artisan migrate:fresh --force --seed
	@echo "✓ Database refreshed"

cache-clear:
	docker-compose exec php php artisan cache:clear
	docker-compose exec php php artisan config:clear
	docker-compose exec php php artisan view:clear
	@echo "✓ Cache cleared"

tinker:
	docker-compose exec php php artisan tinker

install:
	docker-compose exec php composer install
	@echo "✓ Dependencies installed"

key-gen:
	docker-compose exec php php artisan key:generate
	@echo "✓ New APP_KEY generated"

build:
	docker-compose up -d --build
	@echo "✓ Images rebuilt and containers started"

clean:
	@echo "⚠️  This will remove all containers and data!"
	@read -p "Are you sure? [y/N] " -n 1 -r; \
	echo; \
	if [[ $$REPLY =~ ^[Yy]$$ ]]; then \
		docker-compose down -v; \
		echo "✓ All containers and data removed"; \
	fi

status:
	docker-compose ps

ps:
	docker-compose ps
