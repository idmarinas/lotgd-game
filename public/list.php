<?php

use Lotgd\Core\Controller\ListController;

// addnews ready
// translator ready
// mail ready

\define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

($session['user']['loggedin'] ?? false) && LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(ListController::class);

//-- Finalize page
LotgdResponse::pageEnd();
