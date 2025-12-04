<?php
/**
 * Copyright 2025 (C) IDMarinas - All Rights Reserved
 *
 * Last modified by "IDMarinas" on 24/09/2025, 12:43
 *
 * @project IDMarinas Template Symfony
 * @see     https://github.com/idmarinas/template-symfony
 *
 * @file    doctrine.php
 * @date    09/09/2025
 * @time    13:27
 *
 * @author  Iván Diaz Marinas (IDMarinas)
 * @license BSD 3-Clause License
 *
 * @since   1.0.0
 */

namespace Deployer;

use Throwable;

set('need_db_migration', false);
set('doctrine_schema_validate_config', '--skip-mapping');
set('doctrine/migration/duration', '1m');
set('migrations/options', '--no-interaction --allow-no-migration');

//desc('Crear contenedor temporal de la base de datos');
//task('doctrine:docker:container:db', function () {
//	run(
//		'docker run --rm -d --name deployer_{{docker/project_name}}_db_temp idmarinas/{{docker/project_name}}:{{app/version}}'
//	);
//	run('docker cp deployer_{{docker/project_name}}_db_temp:/app/var/db.sql {{release_path}}/var/db.sql');
//	run('docker rm deployer_{{docker/project_name}}_db_temp');
//});

desc('Comprobar si se necesitan migraciones de Doctrine');
task('doctrine:check', function () {
	writeln('<info>Comprobando si se necesitan migraciones de Doctrine</>');

	try {
		run('{{bin/console}} doctrine:migrations:up-to-date', real_time_output: true);
		writeln('<info>No se necesitan migraciones de Doctrine</>');
		set('need_db_migration', false);
	} catch (Throwable) {
		writeln('<fg=yellow>Se necesitan migraciones de Doctrine</>');
		set('need_db_migration', true);
	}
});

desc('Ejecutar migraciones de Doctrine');
task('doctrine:migrate', function () {
	if (get('need_db_migration')) {
		writeln('<info>Activando el modo mantenimiento</>');
		invoke('migration:estimate:time');
		invoke('maintenance:on');

		writeln('<info>Ejecutando migraciones de Doctrine</>');
		run('{{bin/console}} doctrine:migrations:migrate {{migrations/options}}', real_time_output: true);
	}
});

desc('Estimar tiempo de migración real');
task('migration:estimate:time', function () {
	// 1. Obtener estadísticas de la base de datos
	$tableStats = run(
		'{{bin/console}} doctrine:query:sql "SELECT table_name, table_rows, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb FROM information_schema.TABLES WHERE table_schema = DATABASE() ORDER BY table_rows DESC"'
	);

	// 2. Analizar migraciones pendientes
	$migrationsList = run('{{bin/console}} doctrine:migrations:list --no-interaction --no-ansi');

	preg_match_all('#(DoctrineMigrations\\\\Version[0-9]+)\s+\|\s+not migrated#im', $migrationsList, $matches);
	$pendingMigrations = $matches[1] ?? [];

	// 3. Estimar basado en contenido y volumen
	$estimation = analyzeRealMigrationTime($tableStats, $pendingMigrations);

	writeln('<info>Estimación basada en datos reales:</info>');
	writeln("<comment>Tiempo estimado: <options=bold>{$estimation['duration']}</></comment>");
	writeln("<comment>Factores considerados: <options=bold>{$estimation['factors']}</></comment>");

	set('doctrine/migration/duration', $estimation['duration']);
});

desc('Ejecutar migraciones de Doctrine');
task('doctrine:migrations', [
	'doctrine:check',
	'doctrine:migrate',
]);

function analyzeRealMigrationTime (string $tableStats, array $migrations): array
{
	$factors = [];
	$estimatedSeconds = 5;

	// Parsear estadísticas de tablas para obtener volumen de datos
	$largeTableThreshold = 100000; // 100k registros
	$hugTableThreshold = 1000000;  // 1M registros

	// Factores de tiempo basados en operaciones y volumen
	$operationFactors = [
		// Operaciones en tablas vacías/pequeñas
		'CREATE TABLE'            => 2,
		'DROP TABLE'              => 2,
		'ADD COLUMN'              => 5,
		'DROP COLUMN'             => 3,

		// Operaciones que escalan con datos
		'ALTER TABLE.*ADD.*INDEX' => 30, // Base + factor por cada registro
		'CREATE INDEX'            => 45,
		'UPDATE.*SET'             => 20, // Muy dependiente del volumen
		'INSERT INTO.*SELECT'     => 25,
	];

	foreach ($migrations as $version) {
		$preview = run("{{bin/console}} doctrine:migrations:execute --up '$version' --dry-run -n --no-ansi -vv");

		// Analizar cada operación de las migraciones pendientes
		foreach ($operationFactors as $pattern => $baseTime) {
			if (preg_match_all("/$pattern/i", $preview, $matches)) {
				$occurrences = count($matches[0]);
				$estimatedSeconds += $occurrences * $baseTime;
				$factors[] = "{$occurrences}x $pattern operaciones";
			}
		}
	}

	// Ajustar por volumen de datos (esto es aproximado)
	if (str_contains($tableStats, 'users') && preg_match('/users.*?(\d+)/', $tableStats, $matches)) {
		$userCount = (int)$matches[1];
		if ($userCount > $largeTableThreshold) {
			$estimatedSeconds *= 2; // Duplicar tiempo para tablas grandes
			$factors[] = "Tabla de usuarios grande ($userCount registros)";
		}
		if ($userCount > $hugTableThreshold) {
			$estimatedSeconds *= 2; // Factor adicional para tablas muy grandes
			$factors[] = 'Detectada una tabla de usuarios enorme.';
		}
	}

	$estimatedSeconds *= 2;
	$factors[] = '100% de margen de seguridad aplicado'; // Las migraciones siempre tardan más de lo esperado

	writeln("<comment>Tiempo estimado segundos: <options=bold>$estimatedSeconds</></comment>");
	// Mínimo 30 segundos para cualquier migración
	$estimatedSeconds = max($estimatedSeconds, 90);

	// Convertir a formato legible
	if ($estimatedSeconds < 3600) {
		$duration = ceil($estimatedSeconds / 60) . 'm';
	} else {
		$duration = ceil($estimatedSeconds / 3600) . 'h';
	}

	return [
		'duration' => $duration,
		'factors'  => implode(', ', $factors),
	];
}
