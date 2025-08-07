<?php

/**
 * Copyright 2024 (C) IDMarinas - All Rights Reserved
 *
 * @file    create_env_file.php
 * @project LoTGD Template
 * @see     https://github.com/idmarinas/lotgd-template
 *
 * @author  Iván Diaz Marinas (IDMarinas)
 * @license BSD 3-Clause License
 * @date    2024
 *
 * @since   1.0.0
 */

// TODO: trasladar a composer-plugin
// Incluye el archivo de configuración
$dir = dirname(__DIR__, 2);
$config = include "$dir/.env.local.php";
ksort($config, SORT_NATURAL);

// Abre (o crea) el archivo .env para escribir
$envFile = fopen("$dir/.env.docker", 'w');

// Recorre el array y escribe cada clave-valor en el archivo .env
foreach ($config as $key => $value) {
    if ('DATABASE_NAME' === $key || 'DATABASE_USER' === $key) {
        $value = str_replace('_dev', '', $value);
    }

    if (str_contains($value, ' ')) {
        $value = '"' . $value . '"';
    }

    fwrite($envFile, "$key=$value\n");
}

// Cierra el archivo
fclose($envFile);

echo '.env.docker file has been created successfully.';
echo "\n";
