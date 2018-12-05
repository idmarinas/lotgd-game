<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/sanitize.php';
require_once 'lib/buffs.php';

tlschema('newday');
//mass_module_prepare(array("newday-intercept", "newday"));
modulehook('newday-intercept', []);

/***************
 **  SETTINGS **
 ***************/
$turnsperday = getsetting('turns', 10);
$maxinterest = ((float) getsetting('maxinterest', 10) / 100) + 1; //1.1;
$mininterest = ((float) getsetting('mininterest', 1) / 100) + 1; //1.1;
$dailypvpfights = getsetting('pvpday', 3);

$resline = ('true' == httpget('resurrection')) ? '&resurrection=true' : '';
/******************
 ** End Settings **
 ******************/
$dk = httpget('dk');

if ((count($session['user']['dragonpoints']) <
            $session['user']['dragonkills']) && '' != $dk)
{
    array_push($session['user']['dragonpoints'], $dk);

    switch ($dk){
        case 'str':
            $session['user']['strength']++;
            break;
        case 'dex':
            $session['user']['dexterity']++;
            break;
        case 'con':
            $session['user']['constitution']++;
            break;
        case 'int':
            $session['user']['intelligence']++;
            break;
        case 'wis':
            $session['user']['wisdom']++;
            break;
        //legacy support
        case 'hp':
            $session['user']['maxhitpoints'] += 5;
            break;
        case 'at':
            $session['user']['attack']++;
            break;
        case 'de':
            $session['user']['defense']++;
            break;
    }
}

$labels = [
    'General Stuff,title',
    'ff' => 'Forest Fights + 1',
    'hp' => 'Max Hitpoints + 5', //-- Legacy
    'at' => 'Attack + 1', //-- Legacy
    'de' => 'Defense + 1', //-- Legacy
    'Attributes,title',
    'str' => 'Strength +1',
    'dex' => 'Dexterity +1',
    'con' => 'Constitution +1',
    'int' => 'Intelligence +1',
    'wis' => 'Wisdom +1',
    'unknown' => 'Unknown Spends (contact an admin to investigate!)',
];
/**
 * Use modulehook dkpointlabels to activate desactivate labels or add more labels.
 */
$canbuy = [
    'hp' => 0,
    'ff' => 1,
    'str' => 1,
    'dex' => 1,
    'con' => 1,
    'int' => 1,
    'wis' => 1,
    'at' => 0,
    'de' => 0,
    'unknown' => 0,
];

if (is_module_active('staminasystem'))
{
    $canbuy['ff'] = 0;
}
$retargs = modulehook('dkpointlabels', ['desc' => $labels, 'buy' => $canbuy]);
$labels = $retargs['desc'];
$canbuy = $retargs['buy'];

$pdk = httpget('pdk');

$dp = count($session['user']['dragonpoints']);
$dkills = $session['user']['dragonkills'];

if (1 == $pdk)
{
    require_once 'lib/newday/dp_recalc.php';
}

if ($dp < $dkills)
{
    require_once 'lib/newday/dragonpointspend.php';
}
elseif (! $session['user']['race'] || RACE_UNKNOWN == $session['user']['race'])
{
    require_once 'lib/newday/setrace.php';
}
elseif ('' == $session['user']['specialty'])
{
    require_once 'lib/newday/setspecialty.php';
}
else
{
    page_header('It is a new day!');
    rawoutput("<font size='+1'>");
    output('`c`b`#It is a New Day!`0´b´c');
    rawoutput('</font>');
    $resurrection = httpget('resurrection');

    if (true != $session['user']['alive'])
    {
        $session['user']['resurrections']++;
        output('`@You are resurrected!  This is resurrection number %s.`0`n', $session['user']['resurrections']);
        $session['user']['alive'] = true;
        invalidatedatacache('list.php-warsonline');
    }
    $session['user']['age']++;
    $session['user']['seenmaster'] = 0;
    output('You open your eyes to discover that a new day has been bestowed upon you. It is day number `^%s.`0', $session['user']['age']);
    output('You feel refreshed enough to take on the world!`n');

    if (! is_module_active('staminasystem'))
    {
        output('`2Turns for today set to `^%s`2.`n', $turnsperday);
    }

    $turnstoday = "Base: $turnsperday";
    $args = modulehook('pre-newday', ['resurrection' => $resurrection, 'turnstoday' => $turnstoday]);
    $turnstoday = $args['turnstoday'];

    $interestrate = e_rand($mininterest * 100, $maxinterest * 100) / (float) 100;

    if (is_module_active('staminasystem'))
    {
        require_once 'modules/staminasystem/lib/lib.php';
        $stamina = get_stamina(3);
        $canGetInterest = ($stamina <= 40);
    }
    else
    {
        $canGetInterest = ($session['user']['turns'] > getsetting('fightsforinterest', 4) && $session['user']['goldinbank'] >= 0);
    }

    if ($canGetInterest)
    {
        $interestrate = 1;
        output("`2Today's interest rate: `^0% (Bankers in this village only give interest to those who work for it)`2.`n");
    }
    elseif (getsetting('maxgoldforinterest', 100000) && $session['user']['goldinbank'] >= getsetting('maxgoldforinterest', 100000))
    {
        $interestrate = 1;
        output("`2Today's interest rate: `^0%% (The bank will not pay interest on accounts equal or greater than %s to retain solvency)`2.`n", getsetting('maxgoldforinterest', 100000));
    }
    else
    {
        output("`2Today's interest rate: `^%s%% `n", ($interestrate - 1) * 100);

        if ($session['user']['goldinbank'] >= 0)
        {
            output('`2Gold earned from interest: `^%s`2.`n', (int) ($session['user']['goldinbank'] * ($interestrate - 1)));
        }
        else
        {
            output('`2Interest Accrued on Debt: `^%s`2 gold.`n', -(int) ($session['user']['goldinbank'] * ($interestrate - 1)));
        }
    }

    //clear all standard buffs
    $tempbuf = unserialize($session['user']['bufflist']);
    $session['user']['bufflist'] = '';
    strip_all_buffs();
    tlschema('buffs');

    foreach ($tempbuf as $key => $val)
    {
        if (array_key_exists('survivenewday', $val) &&
                1 == $val['survivenewday'])
        {
            //$session['bufflist'][$key]=$val;
            if (array_key_exists('schema', $val) && $val['schema'])
            {
                tlschema($val['schema']);
            }
            apply_buff($key, $val);

            if (array_key_exists('newdaymessage', $val) &&
                    $val['newdaymessage'])
            {
                output($val['newdaymessage']);
                output_notl('`n');
            }

            if (array_key_exists('schema', $val) && $val['schema'])
            {
                tlschema();
            }
        }
    }
    tlschema();

    output('`2Hitpoints have been restored to `^%s`2.`n', $session['user']['maxhitpoints']);

    reset($session['user']['dragonpoints']);
    $dkff = 0;

    if (! is_module_active('staminasystem'))
    {
        while (list($key, $val) = each($session['user']['dragonpoints']))
        {
            if ('ff' == $val)
            {
                $dkff++;
            }
        }

        if ($dkff > 0)
        {
            output('`n`2You gain `^%s`2 forest %s from spent dragon points!', $dkff, translate_inline(1 == $dkff ? 'fight' : 'fights'));
        }
    }

    if ($session['user']['hashorse'])
    {
        $buff = unserialize($playermount['mountbuff']);

        if (! isset($buff['schema']) || '' == $buff['schema'])
        {
            $buff['schema'] = 'mounts';
        }
        apply_buff('mount', $buff);
    }

    $r1 = e_rand(-1, 1);
    $r2 = e_rand(-1, 1);
    $spirits = $r1 + $r2;
    $resurrectionturns = $spirits;

    if ('true' == $resurrection)
    {
        addnews('`&%s`& has been resurrected by %s`&.', $session['user']['name'], getsetting('deathoverlord', '`$Ramius'));
        $spirits = -6;
        $resurrectionturns = getsetting('resurrectionturns', -6);

        if (strstr($resurrectionturns, '%'))
        {
            $resurrectionturns = strtok($resurrectionturns, '%');
            $resurrectionturns = (int) $resurrectionturns;

            if ($resurrectionturns < -100)
            {
                $resurrectionturns = -100;
            }
            $resurrectionturns = round(($turnsperday + $dkff) * ($resurrectionturns / 100), 0);
        }
        else
        {
            if ($resurrectionturns < -($turnsperday + $dkff))
            {
                $resurrectionturns = -($turnsperday + $dkff);
            }
        }
        $session['user']['deathpower'] -= getsetting('resurrectioncost', 100);
        $session['user']['restorepage'] = 'village.php?c=1';
    }

    $sp = [(-6) => 'Resurrected', (-2) => 'Very Low', (-1) => 'Low', (0) => 'Normal', 1 => 'High', 2 => 'Very High'];
    $sp = translate_inline($sp);
    output('`n`2You are in `^%s`2 spirits today!`n', $sp[$spirits]);

    if (abs($spirits) > 0)
    {
        if ($resurrectionturns > 0)
        {
            $gain = translate_inline('gain');
        }
        else
        {
            $gain = translate_inline('lose');
        }

        if (is_module_active('staminasystem'))
        {
            output('`2As a result, you `^%s some Stamina`2 for today!`n', $gain);
        }
        else
        {
            $sff = abs($resurrectionturns);
            output('`2As a result, you `^%s %s forest %s`2 for today!`n', $gain, $sff, translate_inline(1 == $sff ? 'fight' : 'fights'));
        }
    }
    $rp = $session['user']['restorepage'];
    $x = max(strrpos('&', $rp), strrpos('?', $rp));

    if ($x > 0)
    {
        $rp = substr($rp, 0, $x);
    }

    if ('badnav.php' == substr($rp, 0, 10) || '' == $rp)
    {
        addnav('Continue', 'news.php');
    }
    else
    {
        addnav('Continue', cmd_sanitize($rp));
    }

    $session['user']['laston'] = date('Y-m-d H:i:s');
    $bgold = $session['user']['goldinbank'];
    $session['user']['goldinbank'] = round($session['user']['goldinbank'] * $interestrate);
    $nbgold = $session['user']['goldinbank'] - $bgold;

    if (0 != $nbgold)
    {
        debuglog(($nbgold >= 0 ? 'earned ' : 'paid ').abs($nbgold).' gold in interest');
    }
    $turnstoday .= ", Spirits: $resurrectionturns, DK: $dkff";
    $session['user']['turns'] = $turnsperday + $resurrectionturns + $dkff;
    $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
    $session['user']['spirits'] = $spirits;

    if ('true' != $resurrection)
    {
        $session['user']['playerfights'] = $dailypvpfights;
    }
    $session['user']['transferredtoday'] = 0;
    $session['user']['amountouttoday'] = 0;
    $session['user']['seendragon'] = 0;
    $session['user']['seenmaster'] = 0;
    $session['user']['fedmount'] = 0;

    if ('true' != $resurrection)
    {
        $session['user']['soulpoints'] = 50 + 10 * $session['user']['level'] + $session['user']['dragonkills'] * 2;
        $session['user']['gravefights'] = getsetting('gravefightsperday', 10);
    }
    $session['user']['boughtroomtoday'] = 0;
    $session['user']['recentcomments'] = $session['user']['lasthit'];
    $session['user']['lasthit'] = gmdate('Y-m-d H:i:s');

    if ($session['user']['hashorse'])
    {
        $msg = $playermount['newday'];
        require_once 'lib/substitute.php';
        $msg = substitute_array('`n`&'.$msg.'`0`n');
        output($msg);
        require_once 'lib/mountname.php';
        list($name, $lcname) = getmountname();

        $mff = (int) $playermount['mountforestfights'];
        $session['user']['turns'] += $mff;
        $turnstoday .= ", Mount: $mff";

        if ($mff > 0)
        {
            $state = translate_inline('gain');
            $color = '`^';
        }
        elseif ($mff < 0)
        {
            $state = translate_inline('lose');
            $color = '`$';
        }
        $mff = abs($mff);

        if (0 != $mff)
        {
            output('`n`&Because of %s`&, you %s%s %s`& forest %s for today!`n`0', $lcname, $color, $state, $mff, translate_inline(1 == $mff ? 'fight' : 'fights'));
        }
    }
    else
    {
        output('`n`&You strap your `%%s`& to your back and head out for some adventure.`0', $session['user']['weapon']);
    }

    if ($session['user']['hauntedby'] > '')
    {
        if (is_module_active('staminasystem'))
        {
            require_once 'modules/staminasystem/lib/lib.php';

            removestamina(25000);
            output('`n`n`)You have been haunted by %s`); as a result, you lose some Stamina!', $session['user']['hauntedby']);
            $session['user']['hauntedby'] = '';
            $turnstoday .= ', Haunted: Stamina reduction';
        }
        else
        {
            output('`n`n`)You have been haunted by %s`); as a result, you lose a forest fight!', $session['user']['hauntedby']);
            $session['user']['turns']--;
            $session['user']['hauntedby'] = '';
            $turnstoday .= ', Haunted: -1';
        }
    }

    require_once 'lib/battle/extended.php';
    unsuspend_companions('allowinshades');

    if (! getsetting('newdaycron', 0))
    {
        //check last time we did this vs now to see if it was a different game day.
        $lastnewdaysemaphore = convertgametime(strtotime(getsetting('newdaySemaphore', '0000-00-00 00:00:00').' +0000'));
        $gametoday = gametime();

        if (gmdate('Ymd', $gametoday) != gmdate('Ymd', $lastnewdaysemaphore))
        {
            // it appears to be a different game day, acquire semaphore and
            // check again.
            $sql = 'LOCK TABLES '.DB::prefix('settings').' WRITE';
            DB::query($sql);
            clearsettings();
            $lastnewdaysemaphore = convertgametime(strtotime(getsetting('newdaySemaphore', '0000-00-00 00:00:00').' +0000'));
            $gametoday = gametime();

            if (gmdate('Ymd', $gametoday) != gmdate('Ymd', $lastnewdaysemaphore))
            {
                //we need to run the hook, update the setting, and unlock.
                savesetting('newdaySemaphore', gmdate('Y-m-d H:i:s'));
                $sql = 'UNLOCK TABLES';
                DB::query($sql);
                require 'lib/newday/newday_runonce.php';
            }
            else
            {
                //someone else beat us to it, unlock.
                $sql = 'UNLOCK TABLES';
                DB::query($sql);
            }
        }
    }
    $args = modulehook('newday', ['resurrection' => $resurrection, 'turnstoday' => $turnstoday]);
    $turnstoday = $args['turnstoday'];
    //## Process stamina for spirit
    modulehook('stamina-newday', ['spirits' => $spirits]);

    debuglog("New Day Turns: $turnstoday");

    //legacy support if you have no playername set
    if ('' == $session['user']['playername'])
    {
        //set it
        require_once 'lib/names.php';
        $session['user']['playername'] = get_player_basename(false);
    }
}

$session['user']['sentnotice'] = 0;

page_footer();
