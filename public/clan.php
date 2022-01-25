<?php

use Lotgd\Core\Controller\ClanController;

// translator ready
// addnews ready
// mail ready

require_once 'common.php';

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(ClanController::class);

//-- Finalize page
LotgdResponse::pageEnd();
