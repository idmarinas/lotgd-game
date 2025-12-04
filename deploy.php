<?php

namespace Deployer;

import(__DIR__ . '/.deployer/common.php');

//
// Config
//
set('user', 'IDMarinas');

//
// Hosts
//
host('sN.production')
	->setHostname('1.1.1.1')
	->setPort(22)
	->setRemoteUser('username')
	->setDeployPath('/var/www/html')
	->setLabels(['stage' => 'prod', 'role' => 'web', 'server_name' => 'Sn - Docker Server'])
;

task('docker:volume:restore')->disable();

//
// Deploy Task - Upload a new version
//
task('deploy', [
	'deploy:prepare',
	'download:backups',
	'deploy:upload_files',
	'docker:image:load',
	'docker:copy:env_docker',
	'deploy:symfony:workers:stop',
	'docker:service:start',
	'doctrine:migrations',
	'deploy:publish',
]);
