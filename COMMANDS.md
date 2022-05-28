#### Comandos

php bin/console doctrine:schema:update --force --dump-sql

php bin/console doctrine:migrations:diff

php bin/console doctrine:migrations:migrate 'DoctrineMigrations\Version20210707115250'

<!-- Skip migration -->
php bin/console doctrine:migrations:version 'DoctrineMigrations\Version20210127183022' --add

php bin/console assets:install public

symfony console debug:config BundleName

symfony console config:dump-reference BundleName

php phpDocumentor.phar

php bin/console debug:container

composer dump-env prod

./vendor/bin/phan -m csv -o phan.csv
./vendor/bin/phan --init --init-level=3

composer install --no-dev --no-suggest --optimize-autoloader

vendor/bin/rector process --dry-run
