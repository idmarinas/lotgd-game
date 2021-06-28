<?php

define('ALLOW_ANONYMOUS', true);
define('OVERRIDE_FORCED_NAV', true);

require_once dirname(__DIR__).'/common_common.php';

\LotgdResponse::callController(\Lotgd\Core\Controller\LogdnetController::class, 'image', true);
