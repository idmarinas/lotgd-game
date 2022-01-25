<?php

use Lotgd\Core\Controller\BioController;

// addnews ready
// translator ready
// mail ready

require_once 'common.php';

//-- Check new day first
LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(BioController::class);

//-- Finalize page
LotgdResponse::pageEnd();
