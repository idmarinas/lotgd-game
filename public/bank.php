<?php

// translator ready
// addnews ready
// mail ready

require_once 'common.php';

//-- Init page
\LotgdResponse::pageStart();

//-- Call controller
\LotgdResponse::callController(\Lotgd\Core\Controller\BankController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
