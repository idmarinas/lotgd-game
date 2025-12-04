# Limpiar contenedores antiguos con el mismo prefijo
docker ps -a --filter "name=init_env_docker" --format "{{.Names}}" | ForEach-Object {
  docker rm -f $_
}

[String]$ContainerName = "init_env_docker_$((Get-Date).ToString('yyyyMMdd_HHmmss') )"

docker run --detach -w /app --name $ContainerName --volume ./:/app idmarinas/php:8.4-xdebug

docker exec $ContainerName composer install --no-interaction --no-scripts --ansi
docker exec $ContainerName php bin/console cache:clear --ansi
docker exec $ContainerName composer dev:dump:env --no-interaction --ansi

Write-Host '.env.docker file created' -BackgroundColor Green

docker rm -f $ContainerName
