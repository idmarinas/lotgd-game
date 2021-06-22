<?php

\define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

//-- Init page
\LotgdResponse::pageStart('title', [], 'page_about');

checkday();
$op = \LotgdRequest::getQuery('op');

if ($session['user']['loggedin'])
{
    \LotgdNavigation::addNav('common.nav.news', 'news.php');
}
else
{
    \LotgdNavigation::addHeader('common.category.login');
    \LotgdNavigation::addNav('common.nav.login', 'index.php');
}

\LotgdNavigation::addHeader('about.category.about');
\LotgdNavigation::addNav('about.nav.about', 'about.php');
\LotgdNavigation::addNav('about.nav.setup', 'about.php?op=setup');
\LotgdNavigation::addNav('about.nav.module', 'about.php?op=listmodules');
\LotgdNavigation::addNav('about.nav.bundle', 'about.php?op=bundles');
\LotgdNavigation::addNav('about.nav.license', 'about.php?op=license');

if ('listmodules' == $op)
{
    \LotgdNavigation::blockLink('about.php?op=listmodules');

    $method = 'modules';
}
if ('bundles' == $op)
{
    \LotgdNavigation::blockLink('about.php?op=bundles');

    $method = 'bundles';
}
elseif ('setup' == $op)
{
    \LotgdNavigation::blockLink('about.php?op=setup');

    $method = 'setup';
}
elseif ('license' == $op)
{
    \LotgdNavigation::blockLink('about.php?op=license');

    $method = 'license';
}
else
{
    \LotgdNavigation::blockLink('about.php');

    $method = 'index';
}

\LotgdResponse::callController(\Lotgd\Core\Controller\AboutController::class, $method);

//-- Finalize page
\LotgdResponse::pageEnd();
