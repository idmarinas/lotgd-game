<?php

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

require_once 'common.php';
require_once 'lib/titles.php';
require_once 'lib/names.php';

// Don't hook on to this text for your standard modules please, use "dragon" instead.
// This hook is specifically to allow modules that do other dragons to create ambience.
$args = new GenericEvent(null, ['textDomain' => 'page_dragon', 'textDomainNavigation' => 'navigation_app']);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_DRAGON_PRE);
$result = modulehook('dragon-text-domain', $args->getArguments());
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$params = [
    'textDomain' => $textDomain
];

//-- Init page
\LotgdResponse::pageStart('title', [], $textDomain);

$battle = false;
$op = (string) \LotgdRequest::getQuery('op');

if ('' == $op)
{
    if (! \LotgdRequest::getQuery('nointro'))
    {
        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('battle.combat.run', [], $textDomain));
    }

    $maxlevel = LotgdSetting::getSetting('maxlevel', 15);
    $badguy = [
        'creaturename' => \LotgdTranslator::t('creature.name', [], $textDomain),
        'creaturelevel' => $maxlevel + 2,
        'creatureweapon' => \LotgdTranslator::t('creature.weapon', [], $textDomain),
        'creatureattack' => 30 + $maxlevel,
        'creaturedefense' => 10 + $maxlevel,
        'creaturehealth' => 150 + $maxlevel * 10,
        'creaturespeed' => 2.5 + $maxlevel,
        'diddamage' => 0,
        'type' => 'dragon'
    ];

    //--  Transform Dragon to adapt to player
    \LotgdKernel::get('lotgd_core.combat.buffer')->restoreBuffFields();
    $badguy = LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdTransformCreature($badguy);
    \LotgdKernel::get('lotgd_core.combat.buffer')->calculateBuffFields();

    $args = new GenericEvent(null, $badguy);
    \LotgdEventDispatcher::dispatch($args, Events::PAGE_DRAGON_BUFF);
    $badguy = modulehook('buffdragon', $args->getArguments());

    $session['user']['badguy'] = [
        'enemies' => [$badguy],
        'options' => [
            'type' => 'forest',
        ],
    ];
    $battle = true;
}
elseif ('prologue' == $op)
{
    $flawless = (int) \LotgdRequest::getQuery('flawless');

    $params['flawless'] = $flawless;

    \LotgdKernel::get('lotgd_core.combat.buffer')->stripAllBuffs();
    $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
    $characterEntity = $hydrator->extract(new \Lotgd\Core\Entity\Avatar());
    $dkpoints = 0;

    \LotgdKernel::get('lotgd_core.combat.buffer')->restoreBuffFields();
    $args = new GenericEvent(null, [
        'total' => $session['user']['maxhitpoints'],
        'dkpoints' => $dkpoints,
        'extra' => $session['user']['maxhitpoints'] - $dkpoints - ($session['user']['level'] * 10),
        'base' => $dkpoints + ($session['user']['level'] * 10),
    ]);
    \LotgdEventDispatcher::dispatch($args, Events::PAGE_DRAGON_HP_RECALC);
    $hpgain = modulehook('hprecalc', $args->getArguments());

    \LotgdKernel::get('lotgd_core.combat.buffer')->calculateBuffFields();

    //-- Values that do not change when defeating the Dragon
    $nochange = [
        //-- Basic info
        'dragonkills' => 1,
        'name' => 1,
        'playername' => 1,
        'sex' => 1,
        'title' => 1,
        'ctitle' => 1,
        'bio' => 1,
        'charm' => 1,
        'dragonpoints' => 1,
        'gems' => 1,
        'hashorse' => 1,

        //-- Clan info
        'clanid' => 1,
        'clanrank' => 1,
        'clanjoindate' => 1,

        //-- Attributes
        'strength' => 1,
        'dexterity' => 1,
        'intelligence' => 1,
        'constitution' => 1,
        'wisdom' => 1,

        //-- Other info
        'marriedto' => 1,
        'lastmotd' => 1,
        'bestdragonage' => 1,
        'dragonage' => 1,
    ];

    $args = new GenericEvent(null, $nochange);
    \LotgdEventDispatcher::dispatch($args, Events::PAGE_DRAGON_DK_PRESERVE);
    $nochange = modulehook('dk-preserve', $args->getArguments());
    $session['user']['dragonkills']++;

    $badguys = $session['user']['badguy']; //needed for the dragons name later

    $session['user']['dragonage'] = $session['user']['age'];

    if ($session['user']['dragonage'] < $session['user']['bestdragonage'] || 0 == $session['user']['bestdragonage'])
    {
        $session['user']['bestdragonage'] = $session['user']['dragonage'];
    }

    foreach ($characterEntity as $field => $value)
    {
        if ('id' == $field || 'acct' == $field || ($nochange[$field] ?? 0))
        {
            continue;
        }

        $session['user'][$field] = $value;
    }

    //-- Changed to custom default values for this
    $session['user']['location'] = LotgdSetting::getSetting('villagename', LOCATION_FIELDS);
    $session['user']['armor'] = LotgdSetting::getSetting('startarmor', 'T-Shirt');
    $session['user']['weapon'] = LotgdSetting::getSetting('startweapon', 'Fists');

    $newtitle = LotgdTool::getDkTitle($session['user']['dragonkills'], $session['user']['sex']);

    $restartgold = $session['user']['gold'] + LotgdSetting::getSetting('newplayerstartgold', 50) * $session['user']['dragonkills'];
    $restartgems = 0;

    if ($restartgold > LotgdSetting::getSetting('maxrestartgold', 300))
    {
        $restartgold = LotgdSetting::getSetting('maxrestartgold', 300);
        $restartgems = max(0, ($session['user']['dragonkills'] - (LotgdSetting::getSetting('maxrestartgold', 300) / LotgdSetting::getSetting('newplayerstartgold', 50)) - 1));
        $restartgems = min($restartgems, LotgdSetting::getSetting('maxrestartgems', 10));
    }
    $session['user']['gold'] = $restartgold;
    $session['user']['gems'] += $restartgems;

    if ($flawless !== 0)
    {
        $session['user']['gold'] += 3 * LotgdSetting::getSetting('newplayerstartgold', 50);
        $session['user']['gems']++;
    }

    $session['user']['maxhitpoints'] = 10 + $hpgain['dkpoints'] + $hpgain['extra'];
    $session['user']['hitpoints'] = $session['user']['maxhitpoints'];

    // Set the new title.
    $newname = LotgdTool::changePlayerTitle($newtitle);
    $session['user']['title'] = $newtitle;
    $session['user']['name'] = $newname;

    $session['user']['laston'] = new \DateTime('now');
    $session['user']['laston']->sub(new \DateInterval('P1D')); //-- remove 1 day
    $session['user']['slaydragon'] = 1;
    $companions = [];
    $session['user']['companions'] = [];
    $session['user']['charm'] += 5;

    $regname = LotgdTool::getPlayerBasename();

    foreach ($badguys['enemies'] as $opponent)
    {
        if ('dragon' == $opponent['type'])
        {
            $badguy = $opponent;
            break;
        }
    }

    $params['creatureName'] = $badguy['creaturename'];

    \LotgdTool::addNews('battle.victory.news.title', [
        'playerName' => $regname,
        'title' => $session['user']['title'],
        'times' => $session['user']['dragonkills'],
        'creatureName' => $badguy['creaturename']
    ], $textDomain);

    \LotgdLog::debug("slew the dragon and starts with {$session['user']['gold']} gold and {$session['user']['gems']} gems");

    // Moved this hear to make some things easier.
    $args = new GenericEvent();
    \LotgdEventDispatcher::dispatch($args, Events::PAGE_DRAGON_KILL);
    modulehook('dragonkill', $args->getArguments());

    //-- This is only for params not use for other purpose
    $args = new GenericEvent(null, $params);
    \LotgdEventDispatcher::dispatch($args, Events::PAGE_DRAGON_POST);
    $params = modulehook('page-dragon-tpl-params', $args->getArguments());
    \LotgdResponse::pageAddContent(\LotgdTheme::render('page/dragon.html.twig', $params));

    \LotgdNavigation::addNav('common.nav.newday', 'news.php');

    \LotgdResponse::pageEnd();
}
elseif ('run' == $op)
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('battle.combat.run', [], $textDomain));

    $op = 'fight';
    $battle = true;

    \LotgdRequest::setQuery('op', 'fight');
}
elseif ('fight' == $op)
{
    $battle = true;
}

if ($battle)
{
    //-- Any data for personalize results
    $battleDefeatWhere = 'dragon'; //-- Use for create a news, set to false for not create news
    $battleInForest = 'dragon'; //-- Indicating if is a Forest (true) or Graveyard (false)
    $battleShowResult = false; //-- Show result of battle.

    /** @var \Lotgd\Core\Combat\Battle $serviceBattle */
    $serviceBattle = \LotgdKernel::get('lotgd_core.combat.battle');

    $serviceBattle->initialize();

    $serviceBattle
        ->setBattleZone('dragon')
        // ->disableProccessBatteResults()
        ->battleStart() //--* Start the battle.
        ->battleProcess() //--* Proccess the battle rounds.
        ->battleEnd() //--* End the battle for this petition
    ;

    foreach ($session['user']['badguy']['enemies'] as $opponent)
    {
        if ('dragon' == $opponent['type'])
        {
            $badguy = $opponent;
            break;
        }
    }

    if ($serviceBattle->isVictory())
    {
        $flawless = 0;

        if (1 != $badguy['diddamage'])
        {
            $flawless = 1;
        }

        \LotgdNavigation::addNav('common.nav.continue', "dragon.php?op=prologue&flawless=$flawless");

        $serviceBattle->addContextToBattleEnd([
            'battle.end.victory.blow',
            [
                'creatureName' => $badguy['creaturename']
            ],
            $textDomain
        ]);

        \LotgdTool::addNews('battle.victory.news.slain', [
            'playerName' => $session['user']['name'],
            'creatureName' => $badguy['creaturename']
        ], $textDomain);
    }
    elseif ($serviceBattle->isDefeat())
    {
        \LotgdNavigation::addNav('battle.nav.news', 'news.php', ['textDomain' => 'navigation_app']);

        $args = new GenericEvent();
        \LotgdEventDispatcher::dispatch($args, Events::PAGE_DRAGON_DEATH);
        $result = modulehook('dragondeath', $args->getArguments());

        foreach($result as $msg)
        {
            $serviceBattle->addContextToBattleEnd($msg);
        }

        $serviceBattle->battleResults();

        \LotgdResponse::pageEnd();
    }
    elseif ( ! $serviceBattle->battleHasWinner())
    {
        $serviceBattle->fightNav(true, false, 'dragon.php');
    }

    $serviceBattle->battleResults();
}

//-- Finalize page
\LotgdResponse::pageEnd();
