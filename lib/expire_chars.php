<?php

// translator ready
// addnews ready
// mail ready

require_once 'src/constants.php';

\Lotgdkernel::get('Lotgd\Core\Service\Cron\AvatarCleanService')->execute();
