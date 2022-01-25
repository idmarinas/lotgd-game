<?php

use Lotgd\Core\Controller\NewsController;

// translator ready
// addnews ready
// mail ready

\define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

//-- Check new day first
LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
LotgdResponse::pageStart('title', [], 'page_news');

//-- Call controller
LotgdResponse::callController(NewsController::class);

//-- Finalize page
LotgdResponse::pageEnd();
