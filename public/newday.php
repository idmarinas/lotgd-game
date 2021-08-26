<?php

// translator ready
// addnews ready
// mail ready

require_once 'common.php';

\LotgdResponse::callController(\Lotgd\Core\Controller\NewdayController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
