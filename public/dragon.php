<?php

use Lotgd\Core\Controller\DragonController;

require_once 'common.php';

//-- Init page
LotgdResponse::pageStart();

//-- Call controller
LotgdResponse::callController(DragonController::class);

//-- Finalize page
LotgdResponse::pageEnd();
