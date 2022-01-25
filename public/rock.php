<?php

use Lotgd\Core\Controller\RockController;

require_once 'common.php';

LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(RockController::class);

//-- Finalize page
LotgdResponse::pageEnd();
