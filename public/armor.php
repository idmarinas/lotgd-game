<?php

use Lotgd\Core\Controller\ArmorController;

// translator ready
// addnews ready
// mail ready

require_once 'common.php';

//-- Check new day first
LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(ArmorController::class);

//-- Finalize page
LotgdResponse::pageEnd();
