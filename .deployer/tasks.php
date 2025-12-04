<?php

namespace Deployer;

import('recipe/common.php');

import(__DIR__ . '/tasks/docker.php');
import(__DIR__ . '/tasks/upload_files.php');
import(__DIR__ . '/tasks/doctrine.php');
import(__DIR__ . '/tasks/maintenance.php');
import(__DIR__ . '/tasks/symfony_workers.php');
import(__DIR__ . '/tasks/download_files.php');
import(__DIR__ . '/tasks/restore_volumes.php');

task('deploy:prepare', [
	'deploy:info',
	'deploy:setup',
	'deploy:lock',
	'deploy:release',
	'docker:image:build',
]);
task('deploy:publish', [
	'deploy:symlink',
	'deploy:unlock',
	'maintenance:off',
	'deploy:cleanup',
	'deploy:success',
]);
