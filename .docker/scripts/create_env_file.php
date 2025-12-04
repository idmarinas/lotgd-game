<?php

// Incluye el archivo de configuración
$dir = dirname(__DIR__, 2);
$config = include "$dir/.env.local.php";
ksort($config, SORT_NATURAL);

// Abre (o crea) el archivo .env para escribir
$envFile = fopen("$dir/.env.docker", 'w');

// Recorre el array y escribe cada clave-valor en el archivo .env
foreach ($config as $key => $value) {
	if ('DATABASE_NAME' === $key || 'DATABASE_USER' === $key) {
		$value = str_replace(['_dev', '_test'], '', $value);
	}

	if (str_contains($value, ' ')) {
		$value = '"' . $value . '"';
	}

	fwrite($envFile, "$key=$value\n");
}

echo '.env.docker file has been created successfully.';
echo "\n";

// Cierra el archivo
fclose($envFile);
