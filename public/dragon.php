<?php

require_once 'common.php';
require_once 'lib/fightnav.php';
require_once 'lib/titles.php';
require_once 'lib/buffs.php';
require_once 'lib/names.php';
require_once 'lib/creaturefunctions.php';

tlschema('dragon');

// Don't hook on to this text for your standard modules please, use "dragon" instead.
// This hook is specifically to allow modules that do other dragons to create ambience.
$result = modulehook('dragon-text-domain', ['textDomain' => 'page-dragon', 'textDomainNavigation' => 'navigation-app']);
$textDomain = $result['textDomain'];
$textDomainNavigation = $result['textDomainNavigation'];
unset($result);

$params = [
    'textDomain' => $textDomain
];

page_header('title', [], $textDomain);

$battle = false;
$op = (string) \LotgdHttp::getQuery('op');

if ('' == $op)
{
    if (! \LotgdHttp::getQuery('nointro'))
    {
        \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('battle.combat.run', [], $textDomain));
    }

    $maxlevel = getsetting('maxlevel', 15);
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
    restore_buff_fields();
    $badguy = lotgd_transform_creature($badguy);
    calculate_buff_fields();

    $badguy = modulehook('buffdragon', $badguy);

    $session['user']['badguy'] = $badguy;
    $battle = true;
}
elseif ('prologue' == $op)
{
    $flawless = (int) \LotgdHttp::getQuery('flawless');

    $params['flawless'] = $flawless;
    $params['creatureName'] = $badguy['creaturename'];

    \LotgdNavigation::addNav('common.nav.newday', 'news.php');

    strip_all_buffs();
    $hydrator = new \Zend\Hydrator\ClassMethods();
    $characterEntity = $hydrator->extract(new \Lotgd\Core\Entity\Characters());
    $dkpoints = 0;

    restore_buff_fields();
    $hpgain = [
        'total' => $session['user']['maxhitpoints'],
        'dkpoints' => $dkpoints,
        'extra' => $session['user']['maxhitpoints'] - $dkpoints - ($session['user']['level'] * 10),
        'base' => $dkpoints + ($session['user']['level'] * 10),
    ];
    $hpgain = modulehook('hprecalc', $hpgain);

    calculate_buff_fields();

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

    $nochange = modulehook('dk-preserve', $nochange);
    $session['user']['dragonkills']++;

    $badguys = $session['user']['badguy']; //needed for the dragons name later

    $session['user']['dragonage'] = $session['user']['age'];

    if ($session['user']['dragonage'] < $session['user']['bestdragonage'] || 0 == $session['user']['bestdragonage'])
    {
        $session['user']['bestdragonage'] = $session['user']['dragonage'];
    }

    foreach ($characterEntity as $field => $value)
    {
        if ($nochange[$field] ?? 0)
        {
            continue;
        }

        $session['user'][$field] = $value;
    }

    //-- Changed to custom default values for this
    $session['user']['location'] = getsetting('villagename', LOCATION_FIELDS);
    $session['user']['armor'] = getsetting('startarmor', 'T-Shirt');
    $session['user']['weapon'] = getsetting('startweapon', 'Fists');

    $newtitle = get_dk_title($session['user']['dragonkills'], $session['user']['sex']);

    $restartgold = $session['user']['gold'] + getsetting('newplayerstartgold', 50) * $session['user']['dragonkills'];
    $restartgems = 0;

    if ($restartgold > getsetting('maxrestartgold', 300))
    {
        $restartgold = getsetting('maxrestartgold', 300);
        $restartgems = max(0, ($session['user']['dragonkills'] - (getsetting('maxrestartgold', 300) / getsetting('newplayerstartgold', 50)) - 1));
        $restartgems = min($restartgems, getsetting('maxrestartgems', 10));
    }
    $session['user']['gold'] = $restartgold;
    $session['user']['gems'] += $restartgems;

    if ($flawless)
    {
        $session['user']['gold'] += 3 * getsetting('newplayerstartgold', 50);
        $session['user']['gems']++;
    }

    $session['user']['maxhitpoints'] = 10 + $hpgain['dkpoints'] + $hpgain['extra'];
    $session['user']['hitpoints'] = $session['user']['maxhitpoints'];

    // Set the new title.
    $newname = change_player_title($newtitle);
    $session['user']['title'] = $newtitle;
    $session['user']['name'] = $newname;

    $session['user']['laston'] = new \DateTime('now');
    $session['user']['laston']->sub(new \DateInterval('P1D')); //-- remove 1 day
    $session['user']['slaydragon'] = 1;
    $companions = [];
    $session['user']['companions'] = [];
    $session['user']['charm'] += 5;

    $regname = get_player_basename();
    $badguys = ! is_array($badguys) ? @unserialize($badguys) : $badguys;

    foreach ($badguys['enemies'] as $opponent)
    {
        if ('dragon' == $opponent['type'])
        {
            $badguy = $opponent;
            break;
        }
    }

    addnews('battle.victory.news.title', [
        'playerName' => $regname,
        'title' => $session['user']['title'],
        'times' => $session['user']['dragonkills'],
        'creatureName' => $badguy['creaturename']
    ], $textDomain);

    debuglog("slew the dragon and starts with {$session['user']['gold']} gold and {$session['user']['gems']} gems");

    // Moved this hear to make some things easier.
    modulehook('dragonkill', []);

    invalidatedatacache('list.php-warsonline');

    //-- This is only for params not use for other purpose
    $params = modulehook('page-dragon-tpl-params', $params);
    rawoutput(\LotgdTheme::renderThemeTemplate('page/dragon.twig', $params));

    page_footer();
}
elseif ('run' == $op)
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('battle.combat.run', [], $textDomain));

    $op = 'fight';
    $battle = true;

    \LotgdHttp::setQuery('op', 'fight');
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

    require_once 'battle.php';

    if ($victory)
    {
        $flawless = 0;

        if (1 != $badguy['diddamage'])
        {
            $flawless = 1;
        }

        \LotgdNavigation::addNav('common.nav.continue', "dragon.php?op=prologue&flawless=$flawless");

        $lotgdBattleContent['battleend'][] = [
            'battle.end.victory.slain',
            [
                'creatureName' => $badguy['creaturename']
            ]
        ];
        $lotgdBattleContent['battleend'][] = [
            'battle.end.victory.blow.',
            [
                'creatureName' => $badguy['creaturename']
            ]
        ];

        addnews('battle.victory.news.slain', [
            'playerName' => $session['user']['name'],
            'creatureName' => $badguy['creaturename']
        ], $textDomain);
    }
    elseif ($defeat)
    {
        \LotgdNavigation::addNav('battle.nav.news', 'news.php');

        $result = modulehook('dragondeath', []);
        $lotgdBattleContent['battleend'] = array_merge($lotgdBattleContent['battleend'], $result);

        battleshowresults($lotgdBattleContent);

        page_footer();
    }
    else
    {
        fightnav(true, false);
    }

    battleshowresults($lotgdBattleContent);
}

page_footer();
