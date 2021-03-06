<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/pvpwarning.php';
require_once 'lib/pvpsupport.php';

$iname = LotgdSetting::getSetting('innname', LOCATION_INN);
$battle = false;

$textDomain = 'page_pvp';

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain
];

$op = (string) \LotgdRequest::getQuery('op');
$act = (string) \LotgdRequest::getQuery('act');

if ('' == $op && 'attack' != $act)
{
    \LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

    pvpwarning();

    $pvp = \LotgdKernel::get(\Lotgd\Core\Pvp\Listing::class);
    $pvptime = LotgdSetting::getSetting('pvptimeout', 600);

    $params['tpl'] = 'list';
    $params['paginator'] = $pvp->getPvpList($session['user']['location']);
    $params['sleepers'] = $pvp->getLocationSleepersCount($session['user']['location']);
    $params['returnLink'] = \LotgdRequest::getServer('REQUEST_URI');
    $params['pvpTimeOut'] = new \DateTime(date('Y-m-d H:i:s', strtotime("-$pvptime seconds")));

    \LotgdNavigation::addNav('common.nav.warriors', 'pvp.php');
    \LotgdNavigation::villageNav();
}
elseif ('attack' == $act)
{
    $characterId = (int) \LotgdRequest::getQuery('character_id');

    $badguy = setup_pvp_target($characterId);
    $options['type'] = 'pvp';
    $failedattack = true;

    if (is_array($badguy))
    {
        $failedattack = false;
        $battle = true;

        if ($badguy['location'] == $iname)
        {
            $badguy['bodyguardlevel'] = $badguy['boughtroomtoday'];
        }

        $attackstack['enemies'][0] = $badguy;
        $attackstack['options'] = $options;
        $session['user']['badguy'] = $attackstack;
        $session['user']['playerfights']--;

        \LotgdResponse::pageDebug($session['user']['badguy']);
    }
    elseif (is_string($badguy))
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t($badguy, [], $textDomain));
    }

    if ($failedattack)
    {
        if (\LotgdRequest::getQuery('inn'))
        {
            \LotgdNavigation::addNav('common.nav.listing', 'inn.php?op=bartender&act=listupstairs');
        }
        else
        {
            \LotgdNavigation::addNav('common.nav.listing', 'pvp.php');
        }
    }
}
elseif ('run' == $op)
{
    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.pvp.run', [], $textDomain));

    $op = 'fight';
    \LotgdRequest::setQuery('op', $op);
}

$skill = \LotgdRequest::getQuery('skill');

if ('' != $skill)
{
    \LotgdResponse::pageAddContent(\LotgdTranslator::t('honor', [], $textDomain));
    $skill = '';
    \LotgdRequest::setQuery('skill', '');
}

if ('fight' == $op || 'run' == $op)
{
    $battle = true;
}

if ($battle)
{
    //-- Any data for personalize results
    $battleShowResult = false; //-- Show result of battle.
    $battleProcessVictoryDefeat = false; //-- Process victory or defeat functions when the battle is over

    require_once 'battle.php';

    if ($victory)
    {
        $killedin = $badguy['location'];
        $handled = pvpvictory($badguy, $killedin);

        // Handled will be true if a module has already done the addnews or
        // whatever was needed.
        if (! $handled)
        {
            $news = ($killedin == $iname) ? 'inn' : 'other';

            \LotgdTool::addNews("pvp.victory.{$news}", [
                'playerName' => $session['user']['name'],
                'creatureName' => $badguy['creaturename'],
                'location' => $killedin
            ]);
        }

        $op = '';
        \LotgdRequest::setQuery('op', $op);

        if ($killedin == $iname)
        {
            \LotgdNavigation::addNav('common.nav.inn', 'inn.php');
        }
        else
        {
            \LotgdNavigation::villageNav();
        }

        if ($session['user']['hitpoints'] <= 0)
        {
            $lotgdBattleContent['battleend'][] = [
                'battle.end',
                [],
                $textDomain
            ];
            $session['user']['hitpoints'] = 1;
        }
    }
    elseif ($defeat)
    {
        $killedin = $badguy['location'];
        // This is okay because system mail which is all it's used for is
        // not translated
        $handled = pvpdefeat($badguy, $killedin);
        // Handled will be true if a module has already done the addnews or
        // whatever was needed.
        if (! $handled)
        {
            $taunt = \LotgdTool::selectTaunt();

            $news = ($killedin == $iname) ? 'inn' : 'other';

            \LotgdTool::addNews('deathmessage', [
                'deathmessage' => [
                    'deathmessage' => "pvp.defeated.{$news}",
                    'params' => [
                        'playerName' => $session['user']['name'],
                        'creatureName' => $badguy['creaturename'],
                        'location' => $killedin
                    ],
                    'textDomain' => $textDomain
                ],
                'taunt' => $taunt
            ], '');
        }
    }
    else
    {
        $extra = '';

        if (\LotgdRequest::getQuery('inn'))
        {
            $extra = '?inn=1';
        }
        \LotgdNavigation::fightNav(false, false, "pvp.php{$extra}");
    }

    \LotgdKernel::get('lotgd_core.combat.battle')->battleShowResults($lotgdBattleContent);
}

$params['battle'] = $battle;

//-- This is only for params not use for other purpose
$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_PVP_POST);
$params = modulehook('page-pvp-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('page/pvp.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
