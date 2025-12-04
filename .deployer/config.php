<?php

namespace Deployer;

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

// Obtener las variables .env en $_ENV
new Dotenv()->loadEnv(dirname(__DIR__) . '/.env');

//
// Config
//
set('project_name', $_ENV['APP_TITLE'] ?? 'Your Project Name');
// Release number
set('release_name', fn() => within('{{deploy_path}}', function () {
	$latest = run('cat .dep/latest_release || echo 0');

	return str_pad(strval(intval($latest) + 1), 10, '0', STR_PAD_LEFT);
}));
set('keep_releases', 5);
set('what', get('project_name'));
set('cleanup_use_sudo', true);

set('http_user', 'www-data');
set('http_group', 'www-data');

//
// Project Config
//
set('app/version', $_ENV['APP_VERSION'] ?? '0.0.0');
set('app/version/build', $_ENV['APP_VERSION_BUILD'] ?? 1);
set('docker/project_name', $_ENV['APP_PROJECT_NAME'] ?? 'your_project_name');

// Path to the bin *.
set('bin/webserver', 'docker exec {{docker/project_name}}-webserver-1');
set('bin/php', '{{bin/webserver}} php');
set('bin/composer', '{{bin/webserver}} composer');
set('bin/console', '{{bin/php}} bin/console');
