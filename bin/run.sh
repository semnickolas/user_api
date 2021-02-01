#!/bin/bash

docker-compose up -d --build
sleep 20

docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction
docker-compose exec php php bin/console fos:elastica:populate

docker-compose exec php php bin/phpunit

docker-compose exec php  yarn encore dev
docker-compose exec php php bin/console messenger:consume create_users -vv