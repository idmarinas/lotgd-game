<?php

define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

\LotgdResponse::callController(\Lotgd\Core\Controller\LogdnetController::class, 'image', true);
