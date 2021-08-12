<?php
// translator ready
// addnews ready
// mail ready

require_once 'common.php';

$textDomain = 'page_account';

//-- Check new day first
\LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

//-- Call controller
\LotgdResponse::callController(\Lotgd\Core\Controller\AccountController::class);

//-- Finalize page
\LotgdResponse::pageEnd();

