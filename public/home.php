<?php

// translator ready
// addnews ready
// mail ready

\define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

if ($session['loggedin'] ?? false)
{
    redirect('badnav.php');
}

//-- Init page
\LotgdResponse::pageStart('title', [], 'page_home');

/**
 * First approach to controllers in LoTGD Core
 *
 * LotgdResponse::callController($class, $method);
 *
 * By default $method is 'index'
 */
\LotgdResponse::callController(\Lotgd\Core\Controller\HomeController::class);

//-- Finalize page
\LotgdResponse::pageEnd();
