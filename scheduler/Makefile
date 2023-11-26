NAME="defaultController"

help:
	@echo "targets:"
	@echo "    run"
	@echo "    updateEnv"
	@echo "    migrateDatabase"
	@echo "    updateDatabase"
	@echo "    redoDatabase"
	@echo "    createController Name=\"...\""


run:
	symfony server:start

updateEnv:
	composer install

migrateDatabase:
	symfony console --no-interaction doctrine:migrations:migrate

updateDatabase:
	symfony console --no-interaction doctrine:fixtures:load

redoDatabase:
	symfony console --force doctrine:database:drop
	symfony console doctrine:database:create
	symfony console --no-interaction doctrine:migrations:migrate
	symfony console --no-interaction doctrine:fixtures:load

createController $(NAME):
	symfony console make:controller $(NAME)
