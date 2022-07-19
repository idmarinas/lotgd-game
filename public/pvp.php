<?php

use Lotgd\Core\Controller\PvpController;
use Lotgd\Core\Events;
// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Http\Request;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';

/** @var Lotgd\Core\Http\Request $request */
$request = LotgdKernel::get(Request::class);
$iname   = LotgdSetting::getSetting('innname', LOCATION_INN);
$battle  = false;

$textDomain = 'page_pvp';

//-- Init page
LotgdResponse::pageStart('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain,
];

$op    = (string) $request->query->get('op');
$act   = (string) $request->query->get('act');
$skill = (string) $request->query->get('skill');

if ('' != $skill)
{
    LotgdResponse::pageAddContent(LotgdTranslator::t('honor', [], $textDomain));
    $skill = '';
    $request->query->set('skill', '');
}

if ('' == $op && 'attack' != $act)
{
    LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();

    $method = 'index';
}
elseif ('attack' == $act)
{
    $characterId = $request->query->getInt('character_id');

    $badguy       = LotgdKernel::get('Lotgd\Core\Pvp\Support')->setupPvpTarget($characterId);
    $failedattack = true;

    if (\is_array($badguy))
    {
        $failedattack = false;
        $battle       = true;

        if ($badguy['location'] == $iname)
        {
            $badguy['bodyguardlevel'] = $badguy['boughtroomtoday'];
        }

        $attackstack['enemies'][0] = $badguy;
        $attackstack['options']    = ['type' => 'pvp'];
        $session['user']['badguy'] = $attackstack;
        --$session['user']['playerfights'];

        LotgdResponse::pageDebug($session['user']['badguy']);
    }
    elseif (\is_string($badguy))
    {
        LotgdFlashMessages::addErrorMessage(LotgdTranslator::t($badguy, [], $textDomain));
    }

    if ($failedattack)
    {
        $url = 'pvp.php';

        if ($request->query->get('inn'))
        {
            $url = 'inn.php?op=bartender&act=listupstairs';
        }

        LotgdNavigation::addNav('common.nav.listing', $url);
    }
}
elseif ('run' == $op)
{
    LotgdFlashMessages::addWarningMessage(LotgdTranslator::t('flash.message.pvp.run', [], $textDomain));

    $op = 'fight';
    $request->query->set('op', $op);
}

if ('fight' == $op || 'run' == $op)
{
    $battle = true;
}

if ($battle)
{
    /** @var \Lotgd\Core\Combat\Battle $serviceBattle */
    $serviceBattle = LotgdKernel::get('lotgd_core.combat.battle');

    $serviceBattle->initialize();
    $serviceBattle
        ->setBattleZone('pvp')
        ->disableProccessBatteResults()

        ->battleStart() //--* Start the battle.
        ->battleProcess() //--* Proccess the battle rounds.
        ->battleEnd() //--* End the battle for this petition
    ;

    $badguy = $session['user']['badguy']['enemies'][0];

    if ($serviceBattle->isVictory())
    {
        $killedin = $badguy['location'];
        $handled  = LotgdKernel::get('Lotgd\Core\Pvp\Support')->pvpVictory($badguy, $killedin);

        // Handled will be true if a module has already done the addnews or
        // whatever was needed.
        if ( ! $handled)
        {
            $news = ($killedin == $iname) ? 'inn' : 'other';

            LotgdTool::addNews("pvp.victory.{$news}", [
                'playerName'   => $session['user']['name'],
                'creatureName' => $badguy['creaturename'],
                'location'     => $killedin,
            ]);
        }

        $op = '';
        $request->query->set('op', $op);

        if ($killedin == $iname)
        {
            LotgdNavigation::addNav('common.nav.inn', 'inn.php');
        }
        else
        {
            LotgdNavigation::villageNav();
        }

        if ($session['user']['hitpoints'] <= 0)
        {
            $serviceBattle->addContextToBattleEnd([
                'battle.end',
                [],
                $textDomain,
            ]);
            $session['user']['hitpoints'] = 1;
        }
    }
    elseif ($serviceBattle->isDefeat())
    {
        $killedin = $badguy['location'];
        // This is okay because system mail which is all it's used for is
        // not translated
        $handled = LotgdKernel::get('Lotgd\Core\Pvp\Support')->pvpDefeat($badguy, $killedin);
        // Handled will be true if a module has already done the addnews or
        // whatever was needed.
        if ( ! $handled)
        {
            $taunt = LotgdTool::selectTaunt();

            $news = ($killedin == $iname) ? 'inn' : 'other';

            LotgdTool::addNews('deathmessage', [
                'deathmessage' => [
                    'deathmessage' => "pvp.defeated.{$news}",
                    'params'       => [
                        'playerName'   => $session['user']['name'],
                        'creatureName' => $badguy['creaturename'],
                        'location'     => $killedin,
                    ],
                    'textDomain' => $textDomain,
                ],
                'taunt' => $taunt,
            ], '');
        }
    }
    elseif ( ! $serviceBattle->battleHasWinner())
    {
        $extra = ($request->query->get('inn')) ? '?inn=1' : '';

        $serviceBattle->fightNav(false, false, "pvp.php{$extra}");
    }

    $serviceBattle->battleResults();
}

$params['battle'] = $battle;

//-- This is only for params not use for other purpose
$args = new GenericEvent(null, $params);
LotgdEventDispatcher::dispatch($args, Events::PAGE_PVP_POST);
$params = $args->getArguments();

$request->attributes->set('params', $params);

if ( ! $battle && $method)
{
    LotgdResponse::callController(PvpController::class, $method);
}

//-- Finalize page
LotgdResponse::pageEnd();
