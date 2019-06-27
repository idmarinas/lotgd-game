<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/fightnav.php';
require_once 'lib/pvpwarning.php';
require_once 'lib/pvpsupport.php';
require_once 'lib/taunt.php';

tlschema('pvp');

$iname = getsetting('innname', LOCATION_INN);
$battle = false;

$textDomain = 'page-pvp';

page_header('title', [], $textDomain);

$params = [
    'textDomain' => $textDomain
];

$op = (string) \LotgdHttp::getQuery('op');
$act = (string) \LotgdHttp::getQuery('act');

if ('' == $op && 'attack' != $act)
{
    checkday();

    pvpwarning();

    $pvp = \LotgdLocator::get(\Lotgd\Core\Pvp\Listing::class);
    $pvptime = getsetting('pvptimeout', 600);

    $params['tpl'] = 'default';
    $params['paginator'] = $pvp->getPvpList($session['user']['location']);
    $params['sleepers'] = $pvp->getLocationSleepersCount($session['user']['location']);
    $params['returnLink'] = \LotgdHttp::getServer('REQUEST_URI');
    $params['pvpTimeOut'] = new \DateTime(date('Y-m-d H:i:s', strtotime("-$pvptime seconds")));

    \LotgdNavigation::addNav('common.nav.warriors', 'pvp.php');
    \LotgdNavigation::villageNav();
}
elseif ('attack' == $act)
{
    $characterId = (int) \LotgdHttp::getQuery('character_id');

    $badguy = setup_pvp_target($name);
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

        debug($session['user']['badguy']);
    }
    elseif (is_string($badguy))
    {
        \LotgdFlashMessages::addErrorMessage(\LotgdTranslate::t($badguy, [], $textDomain));
    }

    if ($failedattack)
    {
        if (\LotgdHttp::getQuery('inn'))
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
    \LotgdHttp::setQuery('op', $op);
}

$skill = \LotgdHttp::getQuery('skill');

if ('' != $skill)
{
    output('Your honor prevents you from using any special ability');
    $skill = '';
    \LotgdHttp::setQuery('skill', '');
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

            addnews("pvp.victory.{$news}", [
                'playerName' => $session['user']['name'],
                'creatureName' => $badguy['creaturename'],
                'location' => $killedin
            ]);
        }

        $op = '';
        \LotgdHttp::setQuery('op', $op);

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
            $lotgdBattleContent['battleend'][] = '`n`n`&Using a bit of cloth nearby, you manage to staunch your wounds so that you do not die as well.';
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
            require_once 'lib/taunt.php';

            $taunt = select_taunt();

            $news = ($killedin == $iname) ? 'inn' : 'other';

            addnews('deathmessage', [
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

        if (\LotgdHttp::getQuery('inn'))
        {
            $extra = '?inn=1';
        }
        fightnav(false, false, "pvp.php{$extra}");
    }

    battleshowresults($lotgdBattleContent);
}

$params['battle'] = $battle;

//-- This is only for params not use for other purpose
$params = modulehook('page-pvp-tpl-params', $params);
rawoutput(\LotgdTheme::renderThemeTemplate('page/pvp.twig', $params));

page_footer();
