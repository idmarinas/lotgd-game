<?php

// addnews ready
// translator ready
// mail ready

require_once 'common.php';

//-- Check new day first
\LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
\LotgdResponse::pageStart();

//-- Call controller
\LotgdResponse::callController(\Lotgd\Core\Controller\HealerController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
