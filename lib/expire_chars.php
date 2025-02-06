<?php

use Lotgd\Core\Service\Cron\AvatarCleanService;

// translator ready
// addnews ready
// mail ready

require_once 'src/constants.php';

Lotgdkernel::get(AvatarCleanService::class)->execute();
