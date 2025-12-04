#!/bin/sh

# Scheduler parece que no es compatible con la opción --limit
docker-php-entrypoint
php bin/console messenger:consume scheduler_default --time-limit=3600 --memory-limit=128M
