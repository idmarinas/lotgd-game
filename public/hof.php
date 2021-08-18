<?php

// translator ready
// addnews ready
// mail ready

// New Hall of Fame features by anpera
// http://www.anpera.net/forum/viewforum.php?f=27

require_once 'common.php';

//-- Check new day first
\LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
\LotgdResponse::pageStart();

//-- Call controller
\LotgdResponse::callController(\Lotgd\Core\Controller\HofController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
