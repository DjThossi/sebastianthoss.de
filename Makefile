.DEFAULT_GOAL:=help

.PHONY: help
help:
	@grep -E '^[0-9a-zA-Z_-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: setup
setup: docker-up composer-install ## Setup project and dependencies

.PHONY: docker-up
docker-up: ## Booting docker env
	docker compose up -d

.PHONY: composer-install
composer-install: ## Composer install
	docker compose run --rm php bash -c "composer install"

.PHONY: composer-update
composer-update: ## Composer update
	docker compose run --rm php bash -c "composer update"

.PHONY: generate-dev
generate-dev: ## Composer update
	docker compose run --rm php bash -c "vendor/bin/sculpin generate"

.PHONY: generate-prod
generate-prod: ## Composer update
	docker compose run --rm php bash -c "vendor/bin/sculpin generate --env=prod"