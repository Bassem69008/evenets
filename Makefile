.PHONY: install update rebuild build logs start stop down uninstall console-php run-quality-gates run-tests cc

#---DOCKER---#
DOCKER = docker
DOCKER_RUN = $(DOCKER) run

#---PHPQA---#
PHPQA = jakzal/phpqa
PHPQA_RUN = $(DOCKER_RUN) --init -it --rm -v $(PWD):/project -w /project $(PHPQA)

###########################################
# Docker helpers commands
###########################################
# start all containers
start-dev:
	@docker-compose --env-file ./.env.local up -d;
	@docker-compose exec webserver php bin/console doctrine:migrations:migrate --no-interaction;
	@docker-compose exec webserver php bin/console doctrine:fixtures:load --no-interaction;
	@docker-compose exec webserver php bin/console cache:clear --no-optional-warmers;


start:
	@docker-compose up -d;
	@docker-compose --env-file ./.env.local up -d;
	@docker-compose exec webserver php bin/console doctrine:migrations:migrate --no-interaction;
	@docker-compose exec webserver php bin/console doctrine:fixtures:load --no-interaction;
	@docker-compose exec webserver php bin/console cache:clear --no-optional-warmers;



# build and (re)start all containers
build:
	@docker-compose build --no-cache --pull;
	@docker-compose up -d --force-recreate --build;


# stop all running containers
stop:
	@docker-compose stop;

# down all containers
down:
	@docker-compose down --rmi all -v --remove-orphans;

kill:
	@docker-compose kill
	@docker-compose down --remove-orphan;

###########################################
# Install commands
###########################################

# generate env files
setup:
	@./bin/local-env-setup;

# install
install:
	@docker-compose exec webserver composer install --optimize-autoloader --no-scripts --no-interaction --no-progress --prefer-dist
	@docker-compose exec webserver php bin/console doctrine:migrations:migrate --no-interaction;
	@docker-compose exec webserver php bin/console doctrine:fixtures:load --no-interaction;
	@docker-compose exec webserver php bin/console cache:clear --no-optional-warmers;


# update docker stack
update:
	@docker-compose exec webserver composer self-update --no-interaction;
	@docker-compose exec webserver composer install --optimize-autoloader --no-scripts --no-interaction --no-progress --prefer-dist;
	@docker-compose exec webserver php bin/console doctrine:migrations:migrate --no-interaction;
	@docker-compose exec webserver php bin/console cache:clear --no-optional-warmers;

# uninstall current project (docker stack with volumes, env files)
uninstall:
	@docker-compose down --rmi all -v --remove-orphans;
	@sudo git clean -dfX;


###########################################
# NIJI commands
###########################################
# run php
console-php:
	@docker-compose exec webserver bash;

console-mysql:
	@docker-compose exec database bash;

# run quality-gates scripts
run-quality-gates:
	@docker-compose exec webserver vendor/bin/php-cs-fixer fix src;
	@docker-compose exec webserver vendor/bin/php-cs-fixer fix tests;
	@docker-compose exec webserver vendor/bin/php-cs-fixer fix migrations;
	@docker-compose exec webserver vendor/bin/phpstan analyse -c phpstan.dist.neon --memory-limit=-1;



tests: #tests
	@docker-compose exec webserver php bin/console doctrine:database:drop --force --env=test || true
	@docker-compose exec webserver php bin/console doctrine:database:create --env=test
	@docker-compose exec webserver php bin/console doctrine:migrations:migrate -n --env=test
	@docker-compose exec webserver php bin/console doctrine:fixtures:load -n --env=test
	@docker-compose exec webserver  ./bin/phpunit
.PHONY: tests


run-tests-with-coverage: # run tests with coverage
	@docker-compose exec webserver ./bin/phpunit --coverage-html=coverage --testdox-html=coverage/testdox.html
.PHONY: run-tests-with-coverage

# run symfony clear:cache
cc:
	@symfony console cache:clear --no-optional-warmers;




