<?php

use Lotgd\Core\Controller\TrainController;

//addnews ready
// mail ready
// translator ready

require_once 'common.php';

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(TrainController::class);

//-- Finalize page
LotgdResponse::pageEnd();
