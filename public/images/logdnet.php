<?php

use Lotgd\Core\Controller\LogdnetController;

\define('ALLOW_ANONYMOUS', true);
\define('OVERRIDE_FORCED_NAV', true);

require_once \dirname(__DIR__).'/common_common.php';

LotgdResponse::callController(LogdnetController::class, 'image', true);
