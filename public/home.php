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

\LotgdNavigation::addHeader('home.category.new');
\LotgdNavigation::addNav('home.nav.create', 'create.php');

\LotgdNavigation::addHeader('home.category.func');
\LotgdNavigation::addNav('home.nav.forgot', 'create.php?op=forgot');
\LotgdNavigation::addNav('home.nav.list', 'list.php');
\LotgdNavigation::addNav('home.nav.news', 'news.php');

\LotgdNavigation::addHeader('home.category.other');
\LotgdNavigation::addNav('home.nav.about', 'about.php');
\LotgdNavigation::addNav('home.nav.setup', 'about.php?op=setup');
\LotgdNavigation::addNav('home.nav.net', 'logdnet.php?op=list');

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
