# Docker Makefile for common operations
# Usage: make <target>

.PHONY: help build up down restart logs shell migrate fresh test

# Default target
help:
	@echo "Available commands:"
	@echo "  make build      - Build all containers"
	@echo "  make up         - Start development environment"
	@echo "  make up-prod    - Start production environment"
	@echo "  make down       - Stop all containers"
	@echo "  make restart    - Restart all containers"
	@echo "  make logs       - View container logs"
	@echo "  make shell      - Open shell in app container"
	@echo "  make migrate    - Run database migrations"
	@echo "  make fresh      - Fresh database with seeds"
	@echo "  make test       - Run PHP tests"
	@echo "  make optimize   - Optimize Laravel for production"

# Development
build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans

restart:
	docker-compose restart

logs:
	docker-compose logs -f

shell:
	docker exec -it mr-xies-app sh

# Production
up-prod:
	docker-compose -f docker-compose.prod.yml up -d

down-prod:
	docker-compose -f docker-compose.prod.yml down --remove-orphans

build-prod:
	docker-compose -f docker-compose.prod.yml build

# Laravel Commands
migrate:
	docker exec mr-xies-app php artisan migrate

fresh:
	docker exec mr-xies-app php artisan migrate:fresh --seed

test:
	docker exec mr-xies-app php artisan test

optimize:
	docker exec mr-xies-app php artisan config:cache
	docker exec mr-xies-app php artisan route:cache
	docker exec mr-xies-app php artisan view:cache
	docker exec mr-xies-app php artisan event:cache

clear:
	docker exec mr-xies-app php artisan config:clear
	docker exec mr-xies-app php artisan route:clear
	docker exec mr-xies-app php artisan view:clear
	docker exec mr-xies-app php artisan cache:clear

# Frontend
frontend-build:
	cd xie_vue && npm run build

frontend-dev:
	cd xie_vue && npm run serve

# Database
db-shell:
	docker exec -it mr-xies-db mysql -u laravel -p

db-reset-password:
	@echo "Resetting MySQL password and creating test database..."
	docker exec mr-xies-db mysql -u root -proot -e "ALTER USER 'laravel'@'%' IDENTIFIED BY 'root'; CREATE DATABASE IF NOT EXISTS laravel_testing; GRANT ALL PRIVILEGES ON laravel_testing.* TO 'laravel'@'%'; FLUSH PRIVILEGES;"
	@echo "Done! Run 'make migrate' to apply migrations."

db-setup-test:
	@echo "Creating test database..."
	docker exec mr-xies-db mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS laravel_testing; GRANT ALL PRIVILEGES ON laravel_testing.* TO 'laravel'@'%'; FLUSH PRIVILEGES;"
	@echo "Test database ready!"

# Health check
health:
	@echo "Checking services..."
	@docker-compose ps
	@echo ""
	@echo "API Health:"
	@curl -s http://localhost:8000/health.php || echo "API not responding"
