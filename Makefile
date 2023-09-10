# import env file
include .env
export $(shell pwd)/.env
export USER_ID=$(shell id -u)
export GROUP_ID=$(shell id -g)

#export $(shell sed 's/=.*//' \.env)

# defaults
#APP_PORT:=${APP_PORT:-8082}
#DB_PORT:=${DB_PORT:-3306}

#php artisan key:generate
#php artisan clear-compiled
#composer dump-autoload
#php artisan optimize

# setup a new local development environment
# run this once, after cloning this repo
setup: pull-latest-images env composer-install app-key
	@echo "Setup finished"

wr:
	@echo $(USER_ID)

b:
	@APP_PORT=$(APP_PORT) DB_PORT=$(DB_PORT) USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) XDEBUG_MODE=$(XDEBUG_MODE) \
		docker-compose build

# startup a local development environment
upd:
	@APP_PORT=$(APP_PORT) DB_PORT=$(DB_PORT) \
		docker-compose up -d

up:
	@APP_PORT=$(APP_PORT) DB_PORT=$(DB_PORT) USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) XDEBUG_MODE=$(XDEBUG_MODE) \
		docker-compose up

# watch the docker logs
logs:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose logs --follow --timestamps --tail 1000

# shutdown a local development environment
down:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) \
		docker-compose down

# build all or a specific container image using the cache
build: composer-dumpa
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		build $(SERVICE)

# rebuild all or a specific container image without using the
# cache and without checking for new base images
rebuild-fast:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		build --no-cache $(SERVICE)

# rebuild all or a specific container image
rebuild: pull-latest-images composer-dumpa
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		build --no-cache $(SERVICE)

# get a shell inside a running service container
shell:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		exec travel_api_scheduler sh

# run the unit tests
test:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			vendor/bin/phpunit ./tests/Unit

# run a unit test group
test-group:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			vendor/bin/phpunit ./tests/Unit --group $(GROUP)

# this is for the CI environment to setup the project
# and then run the tests (from scratch). only for CI!
ci-test: setup test
	@echo "CI Testing complete"

# run tinker
tinker:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u 0:0 travel_devtool \
			php artisan tinker

# run arbitrary artisan commands using devtool container image
# eg. `make artisan CMD="travel-api:update-airport-cache --flag=something"`
artisan:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			php artisan $(CMD)

version-info: setup
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_api_task \
			php artisan $(CMD)

info:
	make version-info CMD="env"
	make version-info CMD="env -V"

# get a shell in the devtool container. useful to run various
# commands like composer, phpstan, etc. see devtool Dockerfile
devtool:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool sh

# restart a running service container
restart-service:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		restart $(SERVICE)

# run composer install
composer-install:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			env COMPOSER_CACHE_DIR=/tmp \
			composer install -on --prefer-dist

# run interactive composer require
composer-require:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			env COMPOSER_CACHE_DIR=/tmp \
			composer require $(PACKAGE)

# runs `composer dumpautoload`
composer-dumpa:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			env COMPOSER_CACHE_DIR=/tmp \
			composer dumpautoload

optimize:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			php artisan optimize

# runs `phpstan`
stan:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			phpstan analyse app/ --level 7 || true

# runs `php-cs-fixer`
cs-fix:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			php-cs-fixer fix app/

# run syntax check on php files
syntax-check:
	@find ./app -type f -name '*.php' -exec php -l {} \; >/dev/null

# create a .env file if none exists
env:
	@if [ ! -e .env ]; then cp deploy/resources/env/$(CLIENT)/local.env .env; fi

# generate the Laravel encryption key
app-key:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			php artisan key:generate

# recreate all DB tables and reseed
db-fresh:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			php artisan migrate:fresh --seed

# run pending migrations
db-migrate:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			php artisan migrate

# rollback last migrations
db-rollback:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			php artisan migrate:rollback

# seed database only
db-seed:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			php artisan db:seed

# seed only a dingle class
db-seed-one:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			php artisan db:seed --class=$(CLASS)

# run redis-cli
redis-cli:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			redis-cli -h travel_api_redis

# clear caches, fix things, etc..
refresh: fix-permissions
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool /bin/sh -c \
			"php artisan cache:clear && \
			php artisan config:clear && \
			php artisan route:clear && \
			php artisan view:clear"

# update the cached config from the .env file
cache-config:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool /bin/sh -c \
			"php artisan config:cache"

# make storage world readable. prbly dont do this on prod :/
fix-permissions:
	@find storage -type d -exec chmod 777 {} \;
	@find storage -type f ! -name .gitignore -exec chmod 777 {} \;

# pull latest base docker images
pull-latest-images:
	@docker pull alpine:3.15
	@docker pull nginx:alpine
	@docker pull redis:6-alpine
	@docker pull mariadb:10.6
	@docker pull sonarsource/sonar-scanner-cli:latest

# get a MySQL shell
db-shell:
	@NGINX_PORT=$(NGINX_PORT) DB_PORT=$(DB_PORT) REDIS_PORT=$(REDIS_PORT) \
		docker-compose -f deploy/docker-compose.yml \
		run --rm -u $$(id -u) travel_devtool \
			/usr/bin/mysql \
				-h travel_api_db \
				-u travel_api \
				-pTest1234 \
				-D travel_api

# little hack to run the membersite locally. you need to have
# configured docker to access the quay.io image repository
# they don't tag a "latest", so manually update the version
run-membersite:
	@docker run --rm -it -p 127.0.0.1:8081:8081 \
		-v $$(pwd)/deploy/resources/membersite/local-env.js:/srv/env.js \
		quay.io/engage/travel-membersite:bmo-307

############################################################
##################### Deployment stuff #####################
############################################################

base-build:
	@docker build --no-cache -f deploy/base/Dockerfile \
		-t $(REPO):base .
	@docker push $(REPO):base

nginx-build:
	@docker build --no-cache -f deploy/nginx/Dockerfile \
		-t $(REPO):nginx-$(DEPLOY_VERSION) .
	@docker push $(REPO):nginx-$(DEPLOY_VERSION)

php-build:
	@docker build --no-cache -f deploy/php/Dockerfile \
		-t $(REPO):php-$(DEPLOY_VERSION) .
	@docker push $(REPO):php-$(DEPLOY_VERSION)

scheduler-build:
	@docker build --no-cache -f deploy/scheduler/Dockerfile \
		-t $(REPO):scheduler-$(DEPLOY_VERSION) .
	@docker push $(REPO):scheduler-$(DEPLOY_VERSION)

worker-build:
	@docker build --no-cache -f deploy/worker/Dockerfile \
		-t $(REPO):worker-$(DEPLOY_VERSION) .
	@docker push $(REPO):worker-$(DEPLOY_VERSION)

task-build:
	@docker build --no-cache -f deploy/task/Dockerfile \
		-t $(REPO):task-$(DEPLOY_VERSION) .
	@docker push $(REPO):task-$(DEPLOY_VERSION)

# builds the devtool container image
devtool-build:
	@docker build -f deploy/devtool/Dockerfile \
		-t travel_devtool:latest .

# runs composer install using docker directly (not docker-compose)
install-deps:
	@docker run --rm -it -v $$(pwd):/srv --user $$(id -u):$$(id -g) \
		travel_devtool composer install --no-dev -on --prefer-dist

optimize-build:
	@docker run --rm -it -v $$(pwd):/srv --user $$(id -u):$$(id -g) travel_devtool \
		/bin/sh -c \
		"php artisan route:cache && \
		php artisan view:cache && \
		php artisan event:cache && \
		php artisan config:clear"

# deploy pipeline preparation stage
prepare-build: pull-latest-images set-version set-config base-build devtool-build install-deps optimize-build fix-permissions
	@echo "Deployment built OK"

# group building all images
build-images: nginx-build php-build scheduler-build worker-build task-build
	@echo "Images built OK"

# set the version
set-version:
	sed -i "s/@@version@@/$(DEPLOY_VERSION)/g" config/travel.php

set-config:
	if [ -n "$(PHP)" ]; then sed -i "s/travel_api_php:9000/$(PHP):9000/g" deploy/nginx/default.conf; fi
	sed -i "s|raw/master/phpstan.phar|blob/1.9.x/phpstan.phar|g" deploy/devtool/Dockerfile

	if [ -n "$(REPO)" ]; then sed -i "s|quay.io/engage/travel-api:base|$(REPO):base|g" deploy/devtool/Dockerfile; fi
	if [ -n "$(REPO)" ]; then sed -i "s|quay.io/engage/travel-api:base|$(REPO):base|g" deploy/php/Dockerfile; fi
	if [ -n "$(REPO)" ]; then sed -i "s|quay.io/engage/travel-api:base|$(REPO):base|g" deploy/scheduler/Dockerfile; fi
	if [ -n "$(REPO)" ]; then sed -i "s|quay.io/engage/travel-api:base|$(REPO):base|g" deploy/worker/Dockerfile; fi
	if [ -n "$(REPO)" ]; then sed -i "s|quay.io/engage/travel-api:base|$(REPO):base|g" deploy/task/Dockerfile; fi

# Buildkite Step 1 (Pull Updates)
update:
	@if [ -z "$(BUILDKITE_TAG)" ]; then echo "Missing BUILDKITE_TAG"; exit 1; fi
	@if [ -z "$(BUILDKITE_BUILD_NUMBER)" ]; then echo "Missing BUILDKITE_BUILD_NUMBER"; exit 1; fi
	@echo "BUILDKITE_TAG="$(BUILDKITE_TAG)
	@echo "BUILDKITE_BUILD_NUMBER="$(BUILDKITE_BUILD_NUMBER)
	@echo "DEPLOY_VERSION="$(DEPLOY_VERSION)
	@git fetch --tags --prune --prune-tags
	@git checkout $(BUILDKITE_TAG)
	@sed -i "s|raw/master/phpstan.phar|blob/1.9.x/phpstan.phar|g" deploy/devtool/Dockerfile
	@sed -i "s|quay.io/engage/travel-api|$(REPO)|g" Makefile


# Buildkite Step 2 (Scan Code)
scan:
	@if [ -z "$(SKIP_SCAN)" ]; then sh deploy/scan.sh; fi
# Buildkite Step 3 (Build Images)
images: prepare-build build-images
	@echo "Images built OK"

# Buildkite Step 4 (Deploy)
deploy:
	@sh deploy/deploy.sh

# these are not files, make
.PHONY: up \
	build \
	down \
	test \
	test-group \
	composer-install \
	composer-require \
	composer-dumpa \
	stan \
	cs-fix \
	ci-test \
	env \
	app-key \
	db-fresh \
	db-migrate \
	db-seed \
	db-seed-one \
	refresh \
	fix-permissions \
	nginx-build \
	php-build \
	scheduler-build \
	worker-build \
	devtool-build \
	install-deps \
	prepare-build \
	pull-latest-images \
	build-images \
	update \
	deploy \
	shell \
	db-shell \
	artisan \
	scan \
	cache-config
