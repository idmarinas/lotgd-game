<?php
// translator ready
// addnews ready
// mail ready

require_once 'common.php';

$textDomain = 'page_account';

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

\LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

\LotgdNavigation::addHeader('account.category.navigation');
\LotgdNavigation::villageNav();

\LotgdNavigation::addHeader('account.category.actions');
\LotgdNavigation::addNav('account.nav.refresh', 'account.php');

\LotgdResponse::callController(\Lotgd\Core\Controller\AccountController::class);

//-- Finalize page
\LotgdResponse::pageEnd();

