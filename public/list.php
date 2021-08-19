<?php

// addnews ready
// translator ready
// mail ready

define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

($session['user']['loggedin'] ?? false) && \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
\LotgdResponse::pageStart();

//-- Call controller
\LotgdResponse::callController(\Lotgd\Core\Controller\ListController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
