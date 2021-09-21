<?php

require_once 'common.php';

//-- Init page
\LotgdResponse::pageStart();

//-- Call controller
\LotgdResponse::callController(\Lotgd\Core\Controller\DragonController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
