<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/pvpwarning.php';
require_once 'lib/buffs.php';
require_once 'lib/events.php';
require_once 'lib/partner.php';

tlschema('inn');

// Don't hook on to this text for your standard modules please, use "inn" instead.
// This hook is specifically to allow modules that do other inns to create ambience.
$result = modulehook('inn-text-domain', ['textDomain' => 'page-inn', 'textDomainNavigation' => 'navigation-inn']);
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

page_header('title', ['name' => \LotgdSanitize::fullSanitize($params['innName'])], $textDomain);

$op = (string) \LotgdHttp::getQuery('op');
$subop = (string) \LotgdHttp::getQuery('subop');
$com = \LotgdHttp::getQuery('commentPage');
$commenting = \LotgdHttp::getQuery('commenting');
$comment = \LotgdHttp::getPost('comment');

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
    break;
    case 'bartender':
        $action = (string) \LotgdHttp::getQuery('act');

        $params['tpl'] = 'bartender';
        $params['action'] = $action;

        require 'lib/inn/inn_bartender.php';
    break;
    case 'room':
        $params['tpl'] = 'room';

        $pay = (int) \LotgdHttp::getQuery('pay');

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
                        debuglog("spent $expense gold on an inn room");
                    }

                    $session['user']['location'] = $iname;
                    $session['user']['loggedin'] = 0;
                    $session['user']['restorepage'] = 'inn.php?op=strolldown';
                    saveuser();
                }

                $session = [];

                return redirect('home.php');
            }

            \LotgdFlashNavigation::addWarningMessage(\LotgdTranslator::t('flash.message.room.not.gold', [ 'barkeep' => $barkeep ], $textDomain));

            return redirect('inn.php?op=room');
        }

        if ($params['boughtRoomToday'])
        {
            \LotgdNavigation::addNav('nav.go.room', 'inn.php?op=room&pay=1');
        }

        $bodyguards = ['Butch', 'Bruce', 'Alfonozo', 'Guido', 'Bruno', 'Bubba', 'Al', 'Chuck', 'Brutus', 'Nunzio', 'Terrance', 'Mitch', 'Rocco', 'Spike', 'Gregor', 'Sven', 'Draco'];

        modulehook('innrooms');

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
                page_footer();
            }

            // Reset the special for good.
            $session['user']['specialinc'] = '';
            $session['user']['specialmisc'] = '';
            $skipinndesc = true;
            $op = '';
            \LotgdHttp::setQuery('op', '');
        }

        \LotgdNavigation::addHeader('category.do');

        $args = modulehook('blockcommentarea', ['section' => 'inn']);

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

        $chats = [
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
        ];

        $chats = modulehook('innchatter', $chats);

        $params['talk'] = $chats[array_rand($chats)];
        $params['gameclock'] = getgametime();
    break;
}

//-- Restore text domain for navigation
\LotgdNavigation::setTextDomain();

//-- This is only for params not use for other purpose
$params = modulehook('page-inn-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/inn.twig', $params));

if ('default' == $params['tpl'])
{
    modulehook('inn', []);

    module_display_events('inn', 'inn.php');
}

page_footer();
