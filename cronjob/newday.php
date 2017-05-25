<?php

chdir(realpath(__DIR__ . '/..'));

define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

savesetting('newdaySemaphore', gmdate('Y-m-d H:i:s'));

require 'lib/newday/newday_runonce.php';
