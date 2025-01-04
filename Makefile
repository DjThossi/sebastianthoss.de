.DEFAULT_GOAL:=help

.PHONY: help
help:
	@grep -E '^[0-9a-zA-Z_-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: setup
setup: docker-up composer-install ## Setup project and dependencies

.PHONY: docker-up
docker-up: ## Booting docker env
	docker compose up -d

.PHONY: docker-stop
docker-stop: ## stops docker env
	docker compose stop

.PHONY: composer-install
composer-install: ## Composer install
	docker compose run --rm php bash -c "composer install"

.PHONY: composer-update
composer-update: ## Composer update
	docker compose run --rm php bash -c "composer update"

.PHONY: generate-dev
generate-dev: ## Generates html files for local env
	docker compose run --rm php bash -c "vendor/bin/sculpin generate"

.PHONY: generate-prod
generate-prod: ## Generates html files for production env
	docker compose run --rm php bash -c "vendor/bin/sculpin generate --env=prod --clean --no-interaction"

.PHONY: scripts-image-generator
scripts-image-generator: ## Generates browser optimized images
	docker compose run --rm php bash -c "php _scripts/ImageGenerator/run.php"

.PHONY: scripts-post-generator
scripts-post-generator: ## Generates blog post files based on a single text file.
	docker compose run --rm php bash -c "php _scripts/PostGenerator/run.php"

takeItLive: ## Generates Prod files and commits them
	_scripts/takeItLive.sh