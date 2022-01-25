<?php

use Lotgd\Core\Controller\NewdayController;

// translator ready
// addnews ready
// mail ready

require_once 'common.php';

LotgdResponse::callController(NewdayController::class);

//-- Finalize page
LotgdResponse::pageEnd();
