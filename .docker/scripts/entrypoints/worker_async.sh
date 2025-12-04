#!/bin/sh

docker-php-entrypoint
php bin/console messenger:consume async --time-limit=3600 --memory-limit=128M --limit=100
