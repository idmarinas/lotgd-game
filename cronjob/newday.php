<?php

define('ALLOW_ANONYMOUS', true);

require_once 'public/common.php';

LotgdSetting::saveSetting('newdaySemaphore', gmdate('Y-m-d H:i:s'));

require 'lib/newday/newday_runonce.php';
