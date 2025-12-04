<?php
/**
 * Copyright 2025 (C) IDMarinas - All Rights Reserved
 *
 * Last modified by "IDMarinas" on 17/10/2025, 18:36
 *
 * @project IDMarinas Template Symfony
 * @see     https://github.com/idmarinas/template-symfony
 *
 * @file    restore_volumes.php
 * @date    17/10/2025
 * @time    18:36
 *
 * @author  Iván Diaz Marinas (IDMarinas)
 * @license BSD 3-Clause License
 *
 * @since   1.0.0
 */

namespace Deployer;

//
// Restore volumes
//
use Exception;

task('docker:volume:restore', function () {
	$services = get('docker/services/start');
	$services = explode(' ', $services);

	try {
		$containers = parseServicesToContainers($services);
	} catch (Exception $exception) {
		warning($exception->getMessage());

		return;
	}

	$ask = sprintf(
		'¿Restaurar los volúmenes de los servicios "%s" del proyecto "{{docker/project_name}}", desde el antiguo servidor?',
		implode(', ', $services),
	);
	if (askConfirmation(parse($ask), false)) {
		run('docker stop ' . implode(' ', $containers));

		foreach ($containers as $container) {
			info("<options=bold>Procesando el contenedor $container</>");
			try {
				doDockerRestore($container);
			} catch (Exception $exception) {
				warning($exception->getMessage());
			}
		}

		run('docker start ' . implode(' ', $containers));
	}
})
	->desc('Restaure los volúmenes de los servicios que se inician.')
	->once()->hidden()
;

function doDockerRestore (string $container): void
{
	$fileName = $container . '_backup.tar';
	$localFile = ".restore/$container/$fileName";
	$backupFile = "/backup/$fileName";

	// backup
	// docker run --rm --volumes-from dbstore -v $(pwd):/backup ubuntu tar cvf /backup/backup.tar /dbdata
	on(select('restore=old_docker_server'), function () use ($container, $fileName, $localFile, $backupFile) {
		$dirs = getVolumeDirs($container);

		writeln('Creando la copia de los volúmenes de ' . currentHost()->getTag());

		writeln('Dirs: ' . $dirs);
		run(
			"docker run --rm --volumes-from $container -v {{deploy_path}}:/backup debian:stable-slim tar cvf $backupFile $dirs --ignore-failed-read"
		);

		writeln('Descargando el archivo al sistema local...');
		download("{{deploy_path}}/$fileName", $localFile, ['options' => ['--mkpath']]);
		run("sudo rm -rf {{deploy_path}}/$fileName");
	});

	// restore
	// docker run --rm --volumes-from dbstore2 -v $(pwd):/backup ubuntu bash -c 'cd /dbdata && tar xvf /backup/backup.tar --strip 1'
	on(select('restore=new_docker_server'), function () use ($container, $fileName, $localFile, $backupFile) {
		writeln('Subiendo archivo a ' . currentHost()->getTag());
		upload($localFile, "{{deploy_path}}/$fileName", ['options' => ['--mkpath']]);
		runLocally('rm -rf .restore/');

		writeln('Restaurando los volúmenes...');
		run(
			"docker run --rm --volumes-from $container -v {{deploy_path}}:/backup debian:stable-slim tar xvf $backupFile"
		);
		run("sudo rm -f {{deploy_path}}/$fileName");
	});
}
