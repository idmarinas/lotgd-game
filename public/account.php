<?php

use Lotgd\Core\Controller\AccountController;

// translator ready
// addnews ready
// mail ready

require_once 'common.php';

$textDomain = 'page_account';

//-- Check new day first
LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

//-- Call controller
LotgdResponse::callController(AccountController::class);

//-- Finalize page
LotgdResponse::pageEnd();
