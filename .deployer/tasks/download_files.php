<?php
/**
 * Copyright 2025 (C) IDMarinas - All Rights Reserved
 *
 * Last modified by "IDMarinas" on 17/10/2025, 18:36
 *
 * @project IDMarinas Template Symfony
 * @see     https://github.com/idmarinas/template-symfony
 *
 * @file    download_files.php
 * @date    07/05/2025
 * @time    22:07
 *
 * @author  Iván Diaz Marinas (IDMarinas)
 * @license BSD 3-Clause License
 *
 * @since   1.0.0
 */

namespace Deployer;

use Exception;

set('local/storage/backup', '.storage/{{app/version}}/' . date('Y-m-d'));

//
// Task
//
task('backup:logs', function () {
	writeln('<info>Descargando los "logs" del contenedor web a <fg=blue>{{local/storage/backup}}</>.</>');

	run('mkdir -p {{deploy_path}}/backups/log');

	if (test('{{bin/webserver}} sh -c "[ -d "/app/var/log" ]"')) {
		run('docker cp {{docker/project_name}}-webserver-1:/app/var/log {{deploy_path}}/backups/');
		download('{{deploy_path}}/backups/log/', '{{local/storage/backup}}/logs/', ['options' => ['--mkpath']]);
		run('rm -r {{deploy_path}}/backups/log');
	} else {
		writeln('<fg=red>El contenedor web no tiene un directorio de logs.</>');
	}
})->desc('Descargar los archivos logs del contenedor web.');

task('backup:volumes', function () {
	info('Creando una copia de los volúmenes Docker.');

	$services = get('docker/services/start');
	$services = explode(' ', $services);

	try {
		$containers = parseServicesToContainers($services);
	} catch (Exception $exception) {
		warning($exception->getMessage());

		return;
	}

	foreach ($containers as $container) {
		info("<options=bold>Procesando el contenedor $container</>");

		try {
			doBackupVolumes($container);
		} catch (Exception $exception) {
			warning($exception->getMessage());
		}
	}
})->desc('Descargar una copia de los volúmenes Docker.');

task('download:backups', ['backup:logs', 'backup:volumes'])->hidden();

/**
 * @throws Exception
 */
function doBackupVolumes (string $container): void
{
	$fileName = date('H.i.s') . '_' . $container . '_backup.tar';
	$localFile = "{{local/storage/backup}}/$fileName";
	$backupFile = "/backup/$fileName";

	writeln('Creando la copia de los volúmenes de ' . currentHost()->getTag());

	$dirs = getVolumeDirs($container);
	writeln('Dirs: ' . $dirs);
	run(
		"docker run --rm --volumes-from $container -v {{deploy_path}}:/backup debian:stable-slim tar cvf $backupFile $dirs --ignore-failed-read"
	);

	writeln('Descargando el archivo al sistema local...');
	download("{{deploy_path}}/$fileName", $localFile, ['options' => ['--mkpath']]);
	run("sudo rm -rf {{deploy_path}}/$fileName");
}
