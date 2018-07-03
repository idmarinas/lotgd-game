<?php

// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/fightnav.php';
require_once 'lib/titles.php';
require_once 'lib/http.php';
require_once 'lib/buffs.php';
require_once 'lib/taunt.php';
require_once 'lib/names.php';
require_once 'lib/creaturefunctions.php';

tlschema('dragon');
$battle = false;
page_header('The Green Dragon!');
$op = httpget('op');

if ('' == $op)
{
    if (! httpget('nointro'))
    {
        output('`$Fighting down every urge to flee, you cautiously enter the cave entrance, intent on catching the great green dragon sleeping, so that you might slay it with a minimum of pain.');
        output('Sadly, this is not to be the case, for as you round a corner within the cave you discover the great beast sitting on its haunches on a huge pile of gold, picking its teeth with a rib.');
    }
    $maxlevel = getsetting('maxlevel', 15);
    $badguy = [
        'creaturename' => translate_inline('`@The Green Dragon`0'),
        'creaturelevel' => $maxlevel + 2,
        'creatureweapon' => translate_inline('Great Flaming Maw'),
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

    $session['user']['badguy'] = createstring($badguy);
    $battle = true;
}
elseif ('prologue1' == $op)
{
    $flawless = (int) httpget('flawless');

    addnav('It is a new day', 'news.php');
    strip_all_buffs();

    $dkpoints = 0;

    restore_buff_fields();
    $hpgain = [
        'total' => $session['user']['maxhitpoints'],
        'dkpoints' => $dkpoints,
        'extra' => $session['user']['maxhitpoints'] - $dkpoints - ($session['user']['level'] * 10),
        'base' => $dkpoints + get_player_hitpoints(),
    ];
    modulehook('hprecalc', $hpgain);
    calculate_buff_fields();

    $nochange = [
        'acctid' => 1,
        'name' => 1,
        'sex' => 1,
        'playername' => 1,
        'strength' => 1,
        'dexterity' => 1,
        'intelligence' => 1,
        'constitution' => 1,
        'wisdom' => 1,
        'password' => 1,
        'marriedto' => 1,
        'title' => 1,
        'login' => 1,
        'dragonkills' => 1,
        'locked' => 1,
        'loggedin' => 1,
        'superuser' => 1,
        'gems' => 1,
        'hashorse' => 1,
        'gentime' => 1,
        'gentimecount' => 1,
        'lastip' => 1,
        'uniqueid' => 1,
        'dragonpoints' => 1,
        'laston' => 1,
        'prefs' => 1,
        'lastmotd' => 1,
        'emailaddress' => 1,
        'emailvalidation' => 1,
        'gensize' => 1,
        'bestdragonage' => 1,
        'dragonage' => 1,
        'donation' => 1,
        'donationspent' => 1,
        'donationconfig' => 1,
        'bio' => 1,
        'charm' => 1,
        'banoverride' => 1,
        'referer' => 1,
        'refererawarded' => 1,
        'ctitle' => 1,
        'beta' => 1,
        'clanid' => 1,
        'clanrank' => 1,
        'clanjoindate' => 1,
        'regdate' => 1,
        'translatorlanguages' => 1,
        'replaceemail' => 1,
        'forgottenpassword' => 1,
    ];

    $nochange = modulehook('dk-preserve', $nochange);
    $session['user']['dragonkills']++;

    $badguys = $session['user']['badguy']; //needed for the dragons name later

    $session['user']['dragonage'] = $session['user']['age'];

    if ($session['user']['dragonage'] < $session['user']['bestdragonage'] || 0 == $session['user']['bestdragonage'])
    {
        $session['user']['bestdragonage'] = $session['user']['dragonage'];
    }

    $sql = 'DESCRIBE '.DB::prefix('accounts');
    $result = DB::query($sql);

    while ($row = DB::fetch_assoc($result))
    {
        if (array_key_exists($row['Field'], $nochange) && $nochange[$row['Field']])
        {
            continue;
        }

        $session['user'][$row['Field']] = $row['Default'];
    }
    $session['user']['gold'] = getsetting('newplayerstartgold', 50);
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

        if ($restartgems > getsetting('maxrestartgems', 10))
        {
            $restartgems = getsetting('maxrestartgems', 10);
        }
    }
    $session['user']['gold'] = $restartgold;
    $session['user']['gems'] += $restartgems;

    if ($flawless)
    {
        $session['user']['gold'] += 3 * getsetting('newplayerstartgold', 50);
        $session['user']['gems']++;
    }

    $session['user']['maxhitpoints'] = get_player_hitpoints();
    $session['user']['hitpoints'] = $session['user']['maxhitpoints'];

    // Set the new title.
    $session['user']['title'] = change_player_title($newtitle);
    $session['user']['name'] = $newname;

    $session['user']['laston'] = date('Y-m-d H:i:s', strtotime('-1 day'));
    $session['user']['slaydragon'] = 1;
    $companions = [];
    $session['user']['companions'] = $companions;

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

    $howoften = translate_inline($session['user']['dragonkills'] > 1 ? 'times' : 'time'); // no translation, we never know who is viewing...
    addnews('`#%s`# has earned the title `&%s`# for having slain `@%s`& `^%s`# %s!`0', $regname, $session['user']['title'], $badguy['creaturename'], $session['user']['dragonkills'], $howoften);

    debuglog("slew the dragon and starts with {$session['user']['gold']} gold and {$session['user']['gems']} gems");

    $twig = [
        'flawless' => $flawless,
        'creaturename' => $badguy['creaturename']
    ];

    rawoutput($lotgd_tpl->renderThemeTemplate('pages/dragon.twig', $twig));

    // Moved this hear to make some things easier.
    modulehook('dragonkill', []);
    invalidatedatacache('list.php-warsonline');
}

if ('run' == $op)
{
    output("The creature's tail blocks the only exit to its lair!");
    $op = 'fight';
    httpset('op', 'fight');
}

if ('fight' == $op || 'run' == $op)
{
    $battle = true;
}

if ($battle)
{
    //-- Any data for personalize results
    $battleDefeatWhere = false; //-- Use for create a news, set to false for not create news
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

        tlschema('nav');
        addnav('Continue', "dragon.php?op=prologue1&flawless=$flawless");
        tlschema();

        $lotgdBattleContent['battleend'][] = ['`b`$You have slain %s!`0`b`n', $badguy['creaturename']];
        $lotgdBattleContent['battleend'][] = ['`&With a mighty final blow, `@%s`& lets out a tremendous bellow and falls at your feet, dead at last.', $badguy['creaturename']];

        addnews('`&%s has slain the hideous creature known as `@%s`&.  All across the land, people rejoice!`0', $session['user']['name'], $badguy['creaturename']);
    }
    elseif ($defeat)
    {
        tlschema('nav');
        addnav('Daily news', 'news.php');
        tlschema();

        $taunt = select_taunt();

        if ($session['user']['sex'])
        {
            addnews('`%%s`5 has been slain when she encountered `@%s`5!!!  Her bones now litter the cave entrance, just like the bones of those who came before.`n%s', $session['user']['name'], $badguy['creaturename'], $taunt);
        }
        else
        {
            addnews('`%%s`5 has been slain when he encountered `@%s`5!!!  His bones now litter the cave entrance, just like the bones of those who came before.`n%s', $session['user']['name'], $badguy['creaturename'], $taunt);
        }

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
