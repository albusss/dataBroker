up:
	docker-compose -f ./docker-compose.yml --env-file ./.env up --build -d

down:
	docker-compose -f ./docker-compose.yml --env-file ./.env down

console:
	docker-compose -f ./docker-compose.yml --env-file ./.env exec -it --user root php /bin/bash

mysql:
	docker-compose -f ./docker-compose.yml --env-file ./.env exec -it mysql mysql -u broker -p123456

migrations-up:
	docker-compose -f ./docker-compose.yml --env-file ./.env exec -it --user root php ./bin/console doctrine:migrations:migrate --no-interaction

nodejs:
	docker-compose -f ./docker-compose.yml --env-file ./.env run -it --user root nodejs /bin/bash
