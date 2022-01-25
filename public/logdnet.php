<?php

use Lotgd\Core\Controller\LogdnetController;

// translator ready
// addnews ready
// mail ready

\define('ALLOW_ANONYMOUS', true);

if ( ! isset($_GET['op']) || 'list' != $_GET['op'])
{
    //don't want people to be able to visit the list while logged in -- breaks their navs.
    \define('OVERRIDE_FORCED_NAV', true);
}

require_once 'common.php';

$op = LotgdRequest::getQuery('op');

if ('' == $op)
{
    LotgdResponse::callController(LogdnetController::class, 'register', true);
}
elseif ('net' == $op)
{
    // Someone is requesting our list of servers, so give it to them.

    $version = (int) LotgdRequest::getQuery('version', 0);

    //-- New format JSON
    if (1 == $version)
    {
        LotgdResponse::callController(LogdnetController::class, 'net');
    }
    //-- Old format
    else
    {
        // I'm going to do a slightly niftier sort manually in a bit which always
        // pops the most recent 'official' versions to the top of the list.
        $repository = Doctrine::getRepository('LotgdCore:Logdnet');
        $entities   = $repository->getNetServerList();

        // Okay, they are now sorted, so output them
        foreach ($entities as $value)
        {
            $entity = serialize($value);
            echo $entity."\n";
        }
    }
}
else
{
    //-- Init page
    LotgdResponse::pageStart('title', [], 'page_logdnet');

    LotgdNavigation::addHeader('common.category.login');
    LotgdNavigation::addNav('common.nav.login', 'index.php');

    LotgdResponse::callController(LogdnetController::class, 'list');

    //-- Finalize page
    LotgdResponse::pageEnd();
}
