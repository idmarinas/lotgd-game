<?php

// translator ready
// addnews ready
// mail ready

define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

//-- Init page
\LotgdResponse::pageStart();

//-- Call controller
\LotgdResponse::callController(\Lotgd\Core\Controller\ReferralController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
