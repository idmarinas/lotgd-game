<?php

// addnews ready
// translator ready
// mail ready

define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

//-- Init page
\LotgdResponse::pageStart('title', [], 'page_list');

if ($session['user']['loggedin'])
{
    \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

    if ($session['user']['alive'])
    {
        \LotgdNavigation::villageNav();
    }
    else
    {
        LotgdNavigation::addNav('list.nav.graveyard', 'graveyard.php');
    }
    LotgdNavigation::addNav('list.nav.online', 'list.php');
    LotgdNavigation::addNav('list.nav.full', 'list.php?page=1');

    if ($session['user']['clanid'] > 0)
    {
        LotgdNavigation::addNav('Online Clan Members', 'list.php?op=clan');

        if ($session['user']['alive'])
        {
            LotgdNavigation::addNav('Clan Hall', 'clan.php');
        }
    }
}
else
{
    \LotgdNavigation::addHeader('common.category.login');
    \LotgdNavigation::addNav('common.nav.login', 'index.php');
    \LotgdNavigation::addNav('list.nav.online', 'list.php');
    \LotgdNavigation::addNav('list.nav.full', 'list.php?page=1');
}

$op = \LotgdRequest::getQuery('op');
$page = (int) \LotgdRequest::getQuery('page');

$method = 'page';
if (! $page && '' == $op)
{
    $method = 'index';
}
elseif ('clan' == $op)
{
    $method = 'clan';
}

\LotgdResponse::callController(\Lotgd\Core\Controller\ListController::class, $method);

//-- Finalize page
\LotgdResponse::pageEnd();
