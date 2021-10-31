#!/usr/bin/make
# Makefile readme (ru): <http://linux.yaroslavl.ru/docs/prog/gnu_make_3-79_russian_manual.html>
# Makefile readme (en): <https://www.gnu.org/software/make/manual/html_node/index.html#SEC_Contents>

SHELL = /bin/sh

php_container_name := php
docker_bin := $(shell command -v docker 2> /dev/null)
docker_compose_bin := $(shell command -v docker-compose 2> /dev/null)
docker_compose_yml := docker/docker-compose.yml
user_id := $(shell id -u)

.PHONY : help pull build push login test clean \
         app-pull app app-push\
         sources-pull sources sources-push\
         nginx-pull nginx nginx-push\
         up down restart shell install
.DEFAULT_GOAL := help

# --- [ Development tasks ] -------------------------------------------------------------------------------------------
help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

build: check-environment ## Build containers
	$(docker_compose_bin) --file "$(docker_compose_yml)" build
	$(docker_compose_bin) --file "$(docker_compose_yml)" run -e XDEBUG_MODE=off "$(php_container_name)" composer install

up: check-environment build## Up service
	$(docker_compose_bin) --file "$(docker_compose_yml)" up -d

update: check-environment ## Update dependencies
	$(docker_compose_bin) --file "$(docker_compose_yml)" run --rm -e XDEBUG_MODE=off "$(php_container_name)" composer update

infection: check-environment ## Run infection
	$(docker_compose_bin) --file "$(docker_compose_yml)" run --rm -e XDEBUG_MODE=coverage "$(php_container_name)" vendor/bin/infection -v

test: check-environment ## Execute tests
	$(docker_compose_bin) --file "$(docker_compose_yml)" run --rm -e XDEBUG_MODE=off "$(php_container_name)" /app/vendor/bin/phpunit

phpstan: check-environment ## Execute tests
	$(docker_compose_bin) --file "$(docker_compose_yml)" run --rm -e XDEBUG_MODE=off "$(php_container_name)" /app/vendor/bin/phpstan

composer-validate: ## Validate composer file
	$(docker_compose_bin) --file "$(docker_compose_yml)" run --rm -e XDEBUG_MODE=off "$(php_container_name)" composer validate --strict

composer-require-check: ## Check soft dependencies
	$(docker_compose_bin) --file "$(docker_compose_yml)" run --rm -e XDEBUG_MODE=off "$(php_container_name)" composer-require-checker check --config-file=composer-require-checker.json composer.json

composer-unused: ## Check soft dependencies
	$(docker_compose_bin) --file "$(docker_compose_yml)" run --rm -e XDEBUG_MODE=off "$(php_container_name)" composer-unused

check: check-environment test phpstan composer-validate composer-unused composer-require-check ## Run tests and code analysis

shell: check-environment ## Run shell environment in container
	$(docker_compose_bin) --file "$(docker_compose_yml)" run --rm -u $(user_id) "$(php_container_name)" /bin/bash

stop: ## Stop all containers
	$(docker_compose_bin) --file "$(docker_compose_yml)" down

# Check whether the environment file exists
check-environment:
ifeq ("$(wildcard .env)","")
	- @echo Copying ".env.example";
	- cp .env.example .env
endif

# Prompt to continue
prompt-continue:
	@while [ -z "$$CONTINUE" ]; do \
		read -r -p "Would you like to continue? [y]" CONTINUE; \
	done ; \
	if [ ! $$CONTINUE == "y" ]; then \
        echo "Exiting." ; \
        exit 1 ; \
    fi
