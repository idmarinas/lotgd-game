<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

$args = new GenericEvent(null, ['textDomain' => 'page_bank', 'textDomainNavigation' => 'navigation_bank']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_BANK_PRE);
$result = modulehook('bank-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
    'ownerName' => LotgdSetting::getSetting('bankername', '`@Elessa`0')
];

/** @var Lotgd\Core\Http\Request */
$request = \LotgdKernel::get(\Lotgd\Core\Http\Request::class);
$request->attributes->set('params', $params);

$op = (string) $request->query->get('op');

$method = 'index';
if ('transfer' == $op)
{
    $method = 'transfer';
}
elseif ('transfer2' == $op)
{
    $method = 'transfer2';
}
elseif ('transfer3' == $op)
{
    $method = 'transfer3';
}
elseif ('deposit' == $op)
{
    $method = 'deposit';
}
elseif ('depositfinish' == $op)
{
    $method = 'depositFinish';
}
elseif ('borrow' == $op)
{
    $method = 'borrow';
}
elseif ('withdraw' == $op)
{
    $method = 'withdraw';
}
elseif ('withdrawfinish' == $op)
{
    $method = 'withdrawFinish';
}

\LotgdNavigation::villageNav();

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);
\LotgdNavigation::addHeader('category.money');

if ($session['user']['goldinbank'] >= 0)
{
    \LotgdNavigation::addNav('nav.withdraw', 'bank.php?op=withdraw');
    \LotgdNavigation::addNav('nav.deposit.label', 'bank.php?op=deposit');

    if (LotgdSetting::getSetting('borrowperlevel', 20))
    {
        \LotgdNavigation::addNav('nav.borrow.label', 'bank.php?op=borrow');
    }
}
else
{
    \LotgdNavigation::addNav('nav.deposit.pay', 'bank.php?op=deposit');

    if (LotgdSetting::getSetting('borrowperlevel', 20))
    {
        \LotgdNavigation::addNav('nav.borrow.more', 'bank.php?op=borrow');
    }
}

if (LotgdSetting::getSetting('allowgoldtransfer', 1) && ($session['user']['level'] >= LotgdSetting::getSetting('mintransferlev', 3) || $session['user']['dragonkills'] > 0))
{
    \LotgdNavigation::addNav('nav.transfer', 'bank.php?op=transfer');
}

\LotgdResponse::callController(\Lotgd\Core\Controller\BankController::class, $method);

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- Finalize page
\LotgdResponse::pageEnd();
