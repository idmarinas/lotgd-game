<?php
/**
 * Copyright 2025 (C) IDMarinas - All Rights Reserved
 *
 * Last modified by "IDMarinas" on 17/10/2025, 18:36
 *
 * @project IDMarinas Template Symfony
 * @see     https://github.com/idmarinas/template-symfony
 *
 * @file    common.php
 * @date    17/10/2025
 * @time    18:36
 *
 * @author  Iván Diaz Marinas (IDMarinas)
 * @license BSD 3-Clause License
 *
 * @since   1.0.0
 */

namespace Deployer;

import(__DIR__ . '/tasks.php');
import(__DIR__ . '/config.php');

use Exception;

//
// Variables de texto personalizadas
//

set('text_prod', function () {
	$name = currentHost()->getLabels()['server_name'] ?? 'unknown';

	return "<fg=green>$name</> en <fg=magenta;options=bold>PROD</>";
});
set('text_dev', '<fg=yellow>localhost</> en <fg=red;options=bold>DEV</>');

//
// Funciones reutilizables
//

/**
 * @throws Exception
 */
function parseServicesToContainers (array $services): array
{
	if ([] === $services) {
		throw error('No hay servicios para iniciar.');
	}

	return array_map(fn($service) => parse("{{docker/project_name}}-$service-1"), $services);
}

/**
 * @throws Exception
 */
function getVolumeDirs (string $container): string
{
	$volumes = run("docker inspect $container");
	$volumes = array_filter(json_decode($volumes, true)[0]['Mounts'], fn($mount) => 'volume' === $mount['Type']);

	if ([] === $volumes) {
		throw error("No se han encontrado volúmenes para <fg=yellow;options=bold>$container</>");
	}

	return implode(' ', array_map(fn($v) => $v['Destination'], $volumes));
}
