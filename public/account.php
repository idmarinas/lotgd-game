<?php
// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/datetime.php';

$textDomain = 'page_account';

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

checkday();

\LotgdNavigation::addHeader('account.category.navigation');
\LotgdNavigation::villageNav();

\LotgdNavigation::addHeader('account.category.actions');
\LotgdNavigation::addNav('account.nav.refresh', 'account.php');

$user = $session['user'];

$dragonpointssummary = [];

if ($user['dragonkills'] > 0)
{
    $dragonpointssummary = array_count_values($user['dragonpoints']);
}

//-- Add more statistics using templates
$args = new GenericEvent(null, ['templates' => []]);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_ACCOUNTS_STATS);
$tpl = modulehook('accountstats', $args->getArguments());

$params = [
    'dragonpoints' => $dragonpointssummary,
    'templates' => $tpl['templates']
];

$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_ACCOUNTS_POST);
$params = modulehook('page-account-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/account.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();

