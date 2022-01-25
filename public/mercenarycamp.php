<?php

use Lotgd\Core\Controller\MercenaryCampController;

// translator ready
// addnews ready
// mail ready

require_once 'common.php';

LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(MercenaryCampController::class);

//-- Finalize page
LotgdResponse::pageEnd();
