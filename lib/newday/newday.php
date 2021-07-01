<?php

//-- Init page

use Lotgd\Core\Event\Core;
use Lotgd\Core\Event\Other;

\LotgdResponse::pageStart('title.default', [], $textDomain);

$params['tpl']                 = 'default';
$params['moduleStaminaSystem'] = is_module_active('staminasystem');
$params['turnPerDay']          = $turnsperday;
$params['maxGoldForInterest']  = LotgdSetting::getSetting('maxgoldforinterest', 100000);

$params['resurrected'] = false;

if ( ! $session['user']['alive'])
{
    $params['resurrected'] = true;
    ++$session['user']['resurrections'];
    $session['user']['alive'] = true;
}

++$session['user']['age'];
$session['user']['seenmaster'] = 0;

$turnstoday = "Base: {$turnsperday}";
$args       = new Core(['resurrection' => $resurrection, 'turnstoday' => $turnstoday]);
\LotgdEventDispatcher::dispatch($args, Core::NEWDAY_PRE);
$args       = modulehook('pre-newday', $args->getData());
$turnstoday = $args['turnstoday'];

$interestrate = e_rand($mininterest * 100, $maxinterest * 100) / (float) 100;

if ($params['moduleStaminaSystem'])
{
    require_once 'modules/staminasystem/lib/lib.php';
    $stamina        = get_stamina(3);
    $canGetInterest = ($stamina <= 40);
}
else
{
    $canGetInterest = ($session['user']['turns'] > LotgdSetting::getSetting('fightsforinterest', 4) && $session['user']['goldinbank'] >= 0);
}

$params['canGetInterest'] = $canGetInterest;
$params['maxInterest']    = false;

if ($canGetInterest)
{
    $interestrate = 1;
}
elseif ($params['maxGoldForInterest'] && $session['user']['goldinbank'] >= $params['maxGoldForInterest'])
{
    $interestrate          = 1;
    $params['maxInterest'] = true;
}

$params['interestRate'] = $interestrate;

//clear all standard buffs
$tempbuf                     = $session['user']['bufflist'] ?? [];
$session['user']['bufflist'] = [];
\LotgdKernel::get('lotgd_core.combat.buffer')->stripAllBuffs();

$params['buffMessages'] = [];

foreach ($tempbuf as $key => $val)
{
    if (\array_key_exists('survivenewday', $val) && 1 == $val['survivenewday'])
    {
        \LotgdKernel::get('lotgd_core.combat.buffer')->applyBuff($key, $val);

        if (\array_key_exists('newdaymessage', $val) && $val['newdaymessage'])
        {
            $params['buffMessages'][] = $val['newdaymessage'];
        }
    }
}

\reset($session['user']['dragonpoints']);
$dkff = 0;

$params['forestTurnDragonKill'] = $dkff;

if ( ! $params['moduleStaminaSystem'])
{
    foreach ($session['user']['dragonpoints'] as $key => $val)
    {
        if ('ff' == $val)
        {
            ++$dkff;
        }
    }

    $params['forestTurnDragonKill'] = $dkff;
}

if ($session['user']['hashorse'])
{
    $buff = $playermount['mountbuff'];

    if ( ! isset($buff['schema']) || '' == $buff['schema'])
    {
        $buff['schema'] = 'mounts';
    }
    \LotgdKernel::get('lotgd_core.combat.buffer')->applyBuff('mount', $buff);
}

$r1                = e_rand(-1, 1);
$r2                = e_rand(-1, 1);
$spirits           = $r1 + $r2;
$resurrectionturns = $spirits;

if ('true' == $resurrection)
{
    \LotgdTool::addNews('news.resurrected', [
        'playerName'    => $session['user']['name'],
        'deathOverlord' => LotgdSetting::getSetting('deathoverlord', '`$Ramius`0'),
    ], $textDomain);

    $spirits           = -6;
    $resurrectionturns = LotgdSetting::getSetting('resurrectionturns', -6);

    if (\strstr($resurrectionturns, '%'))
    {
        $resurrectionturns = \strtok($resurrectionturns, '%');
        $resurrectionturns = (int) $resurrectionturns;

        if ($resurrectionturns < -100)
        {
            $resurrectionturns = -100;
        }
        $resurrectionturns = \round(($turnsperday + $dkff) * ($resurrectionturns / 100), 0);
    }
    else
    {
        if ($resurrectionturns < -($turnsperday + $dkff))
        {
            $resurrectionturns = -($turnsperday + $dkff);
        }
    }
    $session['user']['deathpower'] -= LotgdSetting::getSetting('resurrectioncost', 100);
    $session['user']['restorepage'] = 'village.php?c=1';
}

$params['spirits'] = [
    (-6) => 'spirits.00',
    (-2) => 'spirits.01',
    (-1) => 'spirits.02',
    (0)  => 'spirits.03',
    1    => 'spirits.04',
    2    => 'spirits.05',
];
$params['spirit']            = $spirits;
$params['resurrectionTurns'] = $resurrectionturns;

$rp = $session['user']['restorepage'];
$x  = \max(\strrpos('&', $rp), \strrpos('?', $rp));

if ($x > 0)
{
    $rp = \substr($rp, 0, $x);
}

if ('badnav.php' == \substr($rp, 0, 10) || '' == $rp)
{
    \LotgdNavigation::addNav('nav.continue', 'news.php');
}
else
{
    \LotgdNavigation::addNav('nav.continue', \LotgdSanitize::cmdSanitize($rp));
}

$session['user']['laston']     = new DateTime('now');
$bgold                         = $session['user']['goldinbank'];
$session['user']['goldinbank'] = \round($session['user']['goldinbank'] * $interestrate);
$nbgold                        = $session['user']['goldinbank'] - $bgold;

if (0 != $nbgold)
{
    \LotgdLog::debug(($nbgold >= 0 ? 'earned ' : 'paid ').\abs($nbgold).' gold in interest');
}
$turnstoday .= ", Spirits: {$resurrectionturns}, DK: {$dkff}";
$session['user']['turns']     = $turnsperday + $resurrectionturns + $dkff;
$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
$session['user']['spirits']   = $spirits;

if ('true' != $resurrection)
{
    $session['user']['playerfights'] = $dailypvpfights;
}
$session['user']['transferredtoday'] = 0;
$session['user']['amountouttoday']   = 0;
$session['user']['seendragon']       = 0;
$session['user']['seenmaster']       = 0;
$session['user']['fedmount']         = 0;

if ('true' != $resurrection)
{
    $session['user']['soulpoints']  = 50 + 10 * $session['user']['level'] + $session['user']['dragonkills'] * 2;
    $session['user']['gravefights'] = LotgdSetting::getSetting('gravefightsperday', 10);
}
$session['user']['boughtroomtoday'] = 0;
$session['user']['recentcomments']  = $session['user']['lasthit'];
$session['user']['lasthit']         = new \DateTime('now');

if ($session['user']['hashorse'])
{
    $msg                 = $playermount['newday'];
    $params['mountName'] = $playermount['mountname'] ?? '';

    $params['mountMessage'] = \LotgdTool::substituteArray('`n`&'.$playermount['newday'].'`0`n');

    $mff = (int) $playermount['mountforestfights'];
    $session['user']['turns'] += $mff;
    $turnstoday .= ", Mount: {$mff}";

    $params['mountTurns'] = $mff;
}

$params['haunted'] = false;

if ($session['user']['hauntedby'] > '')
{
    $params['haunted'] = $session['user']['hauntedby'];

    if ($params['moduleStaminaSystem'])
    {
        require_once 'modules/staminasystem/lib/lib.php';

        removestamina(25000);
        $turnstoday .= ', Haunted: Stamina reduction';
    }
    else
    {
        --$session['user']['turns'];
        $turnstoday .= ', Haunted: -1';
    }

    $session['user']['hauntedby'] = '';
}

require_once 'lib/battle/extended.php';
unsuspend_companions('allowinshades');

//-- Run new day if not run by cronjob
if ( ! LotgdSetting::getSetting('newdaycron', 0))
{
    //check last time we did this vs now to see if it was a different game day.
    $lastnewdaysemaphore = \LotgdKernel::get('lotgd_core.tool.date_time')->convertGameTime(\strtotime(LotgdSetting::getSetting('newdaySemaphore', '0000-00-00 00:00:00').' +0000'));
    $gametoday           = \LotgdKernel::get('lotgd_core.tool.date_time')->gameTime();

    if (\gmdate('Ymd', $gametoday) != \gmdate('Ymd', $lastnewdaysemaphore))
    {
        // it appears to be a different game day, acquire semaphore and
        // check again.
        clearsettings();
        $lastnewdaysemaphore = \LotgdKernel::get('lotgd_core.tool.date_time')->convertGameTime(\strtotime(LotgdSetting::getSetting('newdaySemaphore', '0000-00-00 00:00:00').' +0000'));
        $gametoday           = \LotgdKernel::get('lotgd_core.tool.date_time')->gameTime();

        if (\gmdate('Ymd', $gametoday) != \gmdate('Ymd', $lastnewdaysemaphore))
        {
            //we need to run the hook, update the setting, and unlock.
            LotgdSetting::saveSetting('newdaySemaphore', \gmdate('Y-m-d H:i:s'));

            require 'lib/newday/newday_runonce.php';
        }
    }
}

$args = new Core([
    'resurrection'         => $resurrection,
    'turnstoday'           => $turnstoday,
    'includeTemplatesPre'  => $params['includeTemplatesPre'],
    'includeTemplatesPost' => $params['includeTemplatesPost'],
]);
\LotgdEventDispatcher::dispatch($args, Core::NEWDAY);
$args = modulehook('newday', $args->getData());

$turnstoday                     = $args['turnstoday'];
$params['includeTemplatesPre']  = $args['includeTemplatesPre'];
$params['includeTemplatesPost'] = $args['includeTemplatesPost'];

//## Process stamina for spirit
$args = new Other(['spirits' => $spirits]);
\LotgdEventDispatcher::dispatch($args, Other::STAMINA_NEWDAY);
modulehook('stamina-newday', $args->getData());

\LotgdLog::debug("New Day Turns: {$turnstoday}");

$session['user']['sentnotice'] = 0;
