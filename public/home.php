<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

\define('ALLOW_ANONYMOUS', true);

require_once 'common.php';

$params = [];

if ($session['loggedin'] ?? false)
{
    return redirect('badnav.php');
}

//-- Init page
\LotgdResponse::pageStart('title', [], 'page_home');

$op = \LotgdRequest::getQuery('op');

//-- Parameters to be passed to the template
$params = array_merge([
    'villagename'           => getsetting('villagename', LOCATION_FIELDS),
    'includeTemplatesPre'   => [], //-- Templates that are in top of content (but below of title)
    'includeTemplatesIndex' => [], //-- Templates that are in index below of new player
    'includeTemplatesPost'  => [], //-- Templates that are in bottom of content
    'gameclock'             => getsetting('homecurtime', 1) ? getgametime() : null,
    'newdaytimer'           => getsetting('homenewdaytime', 1) ? secondstonextgameday() : null,
], $params);

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

//-- Get newest player name if show in home page
if (getsetting('homenewestplayer', 1))
{
    $name = (string) getsetting('newestPlayerName', '');
    $old  = (int) getsetting('newestPlayerOld', 0);
    $new  = (int) getsetting('newestplayer', 0);

    if (0 != $new && $old != $new)
    {
        $character = Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);
        $name      = $character->getCharacterNameFromAcctId($new);
        savesetting('newestPlayerName', $name);
        savesetting('newestPlayerOld', $new);
    }

    $params['newestplayer'] = $name;
}

if (\abs(getsetting('OnlineCountLast', 0) - \strtotime('now')) > 60)
{
    $account = Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);

    savesetting('OnlineCount', $account->getCountAcctsOnline((int) getsetting('LOGINTIMEOUT', 900)));
    savesetting('OnlineCountLast', \strtotime('now'));
}

$params['OnlineCount'] = getsetting('OnlineCount', 0);

$args = new GenericEvent();
\LotgdEventDispatcher::dispatch($args, Events::PAGE_HOME_TEXT);
$results = modulehook('hometext', $args->getArguments());

if (\is_array($results) && \count($results))
{
    $params['hookHomeText'] = $results;
}

if ( ! \LotgdRequest::getCookie('lgi'))
{
    $translator = \LotgdKernel::get('translator');
    \LotgdFlashMessages::addWarningMessage($translator->trans('session.cookies.unactive', [], 'app_default'));
    \LotgdFlashMessages::addInfoMessage($translator->trans('session.cookies.info', [], 'app_default'));
}

$params['serverFull'] = true;

if ($params['OnlineCount'] < getsetting('maxonline', 0) || 0 == getsetting('maxonline', 0))
{
    $params['serverFull'] = false;
}

$args = new GenericEvent();
\LotgdEventDispatcher::dispatch($args, Events::PAGE_HOME_MIDDLE);
$results = modulehook('homemiddle', $args->getArguments());

if (\is_array($results) && \count($results))
{
    $params['hookHomeMiddle'] = $results;
}

//-- By default not have banner are
$params['loginBanner'] = getsetting('loginbanner');
//-- Version of the game the server is running
$params['serverVersion'] = \Lotgd\Core\Kernel::VERSION;

$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_HOME_POST);
$params = modulehook('page-home-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/home.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
