<?php

//addnews ready
// mail ready
// translator ready

require_once 'common.php';

//-- Init page
\LotgdResponse::pageStart();

//-- Call controller
\LotgdResponse::callController(\Lotgd\Core\Controller\TrainController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
