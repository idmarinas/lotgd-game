<?php

// addnews ready
// translator ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/pvpwarning.php';
require_once 'lib/buffs.php';
require_once 'lib/events.php';
require_once 'lib/partner.php';

// Don't hook on to this text for your standard modules please, use "inn" instead.
// This hook is specifically to allow modules that do other inns to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_inn', 'textDomainNavigation' => 'navigation_inn']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_INN_PRE);
$result = modulehook('inn-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$skipinndesc = handle_event('inn');

if (! $skipinndesc)
{
    checkday();
}

$params = [
    'textDomain' => $textDomain,
    'innName' => getsetting('innname', LOCATION_INN),
    'villageName' => getsetting('villagename', LOCATION_FIELDS),
    'barkeep' => getsetting('barkeep', '`tCedrik`0'),
    'partner' => get_partner(),
    'showInnDescription' => ! $skipinndesc,
    'includeTemplatesPre' => [], //-- Templates that are in top of content (but below of title)
    'includeTemplatesPost' => [] //-- Templates that are in bottom of content
];

//-- Init page
\LotgdResponse::pageStart('title', ['name' => \LotgdSanitize::fullSanitize($params['innName'])], $textDomain);

$op = (string) \LotgdRequest::getQuery('op');
$subop = (string) \LotgdRequest::getQuery('subop');
$com = \LotgdRequest::getQuery('commentPage');
$commenting = \LotgdRequest::getQuery('commenting');
$comment = \LotgdRequest::getPost('comment');

$params['op'] = $op;

// Correctly reset the location if they fleeing the dragon
// This needs to be done up here because a special could alter your op.
if ('fleedragon' == $op)
{
    $session['user']['location'] = $params['villageName'];
}

//-- Change text domain for navigation
\LotgdNavigation::setTextDomain($textDomainNavigation);

\LotgdNavigation::addHeader('category.other');
\LotgdNavigation::villageNav();

switch ($op)
{
    case 'converse':
        $params['tpl'] = 'converse';

        \LotgdNavigation::addHeader('category.other');
        \LotgdNavigation::addNav('nav.return.inn', 'inn.php');
    break;
    case 'bartender':
        $action = (string) \LotgdRequest::getQuery('act');

        $params['tpl'] = 'bartender';
        $params['action'] = $action;

        require 'lib/inn/inn_bartender.php';
    break;
    case 'room':
        $params['tpl'] = 'room';

        $pay = (int) \LotgdRequest::getQuery('pay');

        \LotgdNavigation::addHeader('category.other');
        \LotgdNavigation::addNav('nav.return.inn', 'inn.php');

        $expense = round(($session['user']['level'] * (10 + log($session['user']['level']))), 0);
        $fee = getsetting('innfee', '5%');
        $fee = (strpos($fee, '%')) ? round($expense * (str_replace('%', '', $fee) / 100), 0) : 0;
        $bankexpense = $expense + $fee;

        $params['fee'] = $fee;
        $params['feePercent'] = (strpos($fee, '%')) ? str_replace('%', '', $fee) / 100 : null;
        $params['expense'] = $expense;
        $params['bankExpense'] = $bankexpense;
        $params['boughtRoomToday'] = $session['user']['boughtroomtoday'];

        if ($pay)
        {
            if (2 == $pay || $session['user']['gold'] >= $expense || $params['boughtRoomToday'])
            {
                if ($session['user']['loggedin'])
                {
                    if (! $params['boughtRoomToday'])
                    {
                        if (2 == $pay)
                        {
                            $session['user']['goldinbank'] -= $expense;
                        }
                        else
                        {
                            $session['user']['gold'] -= $expense;
                        }

                        $session['user']['boughtroomtoday'] = 1;
                        \LotgdLog::debug("spent $expense gold on an inn room");
                    }

                    $session['user']['location'] = $iname;
                    $session['user']['loggedin'] = 0;
                    $session['user']['restorepage'] = 'inn.php?op=strolldown';
                    saveuser();
                }

                $session = [];

                redirect('home.php');
            }

            \LotgdFlashNavigation::addWarningMessage(\LotgdTranslator::t('flash.message.room.not.gold', [ 'barkeep' => $barkeep ], $textDomain));

            redirect('inn.php?op=room');
        }

        if ($params['boughtRoomToday'])
        {
            \LotgdNavigation::addNav('nav.go.room', 'inn.php?op=room&pay=1');
        }

        $bodyguards = ['Butch', 'Bruce', 'Alfonozo', 'Guido', 'Bruno', 'Bubba', 'Al', 'Chuck', 'Brutus', 'Nunzio', 'Terrance', 'Mitch', 'Rocco', 'Spike', 'Gregor', 'Sven', 'Draco'];

        \LotgdEventDispatcher::dispatch(new GenericEvent(), Events::PAGE_INN_ROOMS);
        modulehook('innrooms');

        \LotgdNavigation::addHeader('category.buy.room');
        \LotgdNavigation::addNav('nav.room.buy.hand', 'inn.php?op=room&pay=1', [
            'params' => [
                'expense' => $expense
            ]
        ]);

        if ($session['user']['goldinbank'] >= $bankexpense)
        {
            \LotgdNavigation::addNav('nav.room.buy.bank', 'inn.php?op=room&pay=2', [
                'params' => [
                    'expense' => $bankexpense
                ]
            ]);
        }

    break;
    default:
        $params['tpl'] = 'default';

        \LotgdNavigation::blockLink('inn.php');

        // Don't give people a chance at a special event if they are just browsing
        // the commentary (or talking) or dealing with any of the hooks in the inn.
        if ('fleedragon' != $op && '' == $com && ! $comment && ! $commenting && 0 != module_events('inn', getsetting('innchance', 0)))
        {
            if (\LotgdNavigation::checkNavs())
            {
                \LotgdResponse::pageEnd();
            }

            // Reset the special for good.
            $session['user']['specialinc'] = '';
            $session['user']['specialmisc'] = '';
            $skipinndesc = true;
            $op = '';
            \LotgdRequest::setQuery('op', '');
        }

        \LotgdNavigation::addHeader('category.do');

        $args = new GenericEvent(null, ['section' => 'inn']);
        \LotgdEventDispatcher::dispatch($args, Events::PAGE_INN_BLOCK_COMMENT_AREA);
        $args = modulehook('blockcommentarea', $args->getArguments());

        if (! ($args['block'] ?? false) || ! $args['block'])
        {
            \LotgdNavigation::addNav('nav.converse', 'inn.php?op=converse');
        }
        \LotgdNavigation::addNav('nav.barkeep.talk', 'inn.php?op=bartender', [
            'params' => [
                'barkeep' => $params['barkeep']
            ]
        ]);

        \LotgdNavigation::addHeader('category.other');
        \LotgdNavigation::addNav('nav.room.get', 'inn.php?op=room');

        if ('fleedragon' == $op)
        {
            $session['user']['charm']--;
            $session['user']['charm'] = max(0, $session['user']['charm']);
        }

        $chats = new GenericEvent(null, [
            [
                'chats.dragon', [], $textDomain
            ],
            [
                getsetting('bard', '`^Seth`0'), [], $textDomain
            ],
            [
                getsetting('barmaid', '`%Violet`0'), [], $textDomain
            ],
            [
                '`#MightyE`0', [], $textDomain
            ],
            [
                'chats.drink', [], $textDomain
            ],
            [
                $params['partner'], [], $textDomain
            ],
        ]);

        \LotgdEventDispatcher::dispatch($chats , Events::PAGE_INN_CHATTER);
        $chats = modulehook('innchatter', $chats->getArguments());

        $params['talk'] = $chats[array_rand($chats)];
        $params['gameclock'] = getgametime();
    break;
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_INN_POST);
$params = modulehook('page-inn-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/inn.html.twig', $params));

if ('default' == $params['tpl'])
{
    $args = new GenericEvent();
    \LotgdEventDispatcher::dispatch($args, Events::PAGE_INN);
    modulehook('inn', $args->getArguments());

    module_display_events('inn', 'inn.php');
}

//-- Finalize page
\LotgdResponse::pageEnd();
