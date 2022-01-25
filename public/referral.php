<?php

use Lotgd\Core\Controller\ReferralController;

// translator ready
// addnews ready
// mail ready

\define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(ReferralController::class);

//-- Finalize page
LotgdResponse::pageEnd();
