DOCKER_COMP = docker compose

PHP_CONT = $(DOCKER_COMP) exec php
COMPOSER = $(PHP_CONT) composer
ARTISAN  = $(PHP_CONT) php artisan
FRONTEND_CONT = $(DOCKER_COMP) exec vite
NPM = $(FRONTEND_CONT) npm

up:
	docker compose up -d

cc:
	@$(ARTISAN) optimize:clear

migrate:
	@$(ARTISAN) migrate

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

stop: down

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

start: build up

bash:
	@$(PHP_CONT) bash

composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

composer-install: ## Run composer install
	$(COMPOSER) install

npm-install: ## Install frontend dependencies using npm
	$(NPM) install

#ssh-tunnel:
#	ssh -L 3307:127.0.0.1:3306 -N -f 138.199.170.179
#
#start:
#	php artisan serve
