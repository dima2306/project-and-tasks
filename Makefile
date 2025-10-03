-include docker.env.example

# Export all variables to sub-processes (including docker-compose)
export

DOCKER_COMPOSE = docker compose
DOCKER_EXEC = $(DOCKER_COMPOSE) exec app

up:
	$(DOCKER_COMPOSE) up -d
	@echo "✅ Containers started!"
	@echo "App: http://localhost:8081"
	@echo "Mailhog: http://localhost:8025"
	@echo "DB: localhost:6603"
	@echo "Redis: localhost:6379"

down:
	$(DOCKER_COMPOSE) down
	@echo "🛑 Containers stopped!"

build:
	$(DOCKER_COMPOSE) build --no-cache
	@echo "✅ Containers built!"

restart: down up

migrate:
	$(DOCKER_EXEC) php artisan migrate
	@echo "✅ Migrations completed!"

migrate-fresh:
	$(DOCKER_EXEC) php artisan migrate:fresh
	@echo "✅ Fresh migrations completed!"

seed:
	$(DOCKER_EXEC) php artisan db:seed
	@echo "✅ Seeding completed!"

test:
	$(DOCKER_EXEC) php artisan test
	@echo "✅ Tests completed!"

# Configure environment - create .env if needed and generate key
configure-env:
	$(DOCKER_EXEC) sh -c 'if [ ! -f .env ]; then cp .env.example .env && echo "✅ Created .env from .env.example"; fi'
	$(DOCKER_EXEC) php artisan key:generate --ansi
	@echo "✅ Environment configured!"

install: up
	@echo "📦 Installing dependencies..."
	$(DOCKER_EXEC) composer install
	@echo "🔧 Configuring environment..."
	@make configure-env
	@echo "📦 Installing NPM dependencies..."
	$(DOCKER_EXEC) npm install
	$(DOCKER_EXEC) npm run build
	@echo "🗄️ Running migrations..."
	@make migrate
	@echo "🌱 Seeding database..."
	@make seed
	@echo "✅ Installation complete!"
