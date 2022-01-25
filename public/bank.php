<?php

use Lotgd\Core\Controller\BankController;

// translator ready
// addnews ready
// mail ready

require_once 'common.php';

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(BankController::class);

//-- Finalize page
LotgdResponse::pageEnd();
