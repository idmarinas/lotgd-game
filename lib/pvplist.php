<?php

// translator ready
// addnews ready
// mail ready

//put these outside the function since they're used by scripts outside of
//this function.
$pvptime = getsetting('pvptimeout', 600);
$pvptimeout = date('Y-m-d H:i:s', strtotime("-$pvptime seconds"));

function pvplist($location = false, $link = false, $extra = false, $sql = false)
{
    global $session,$pvptime,$pvptimeout;

    if (false === $location)
    {
        $location = $session['user']['location'];
    }

    if (false === $link)
    {
        $link = basename($_SERVER['SCRIPT_NAME']);
    }

    if (false === $extra)
    {
        $extra = '?act=attack';
    }

    $days = getsetting('pvpimmunity', 5);
    $exp = getsetting('pvpminexp', 1500);
    $clanrankcolors = ['`!', '`#', '`^', '`&', '`$'];

    if (false === $sql)
    {
        $levdiff = getsetting('pvprange', 2);
        $lev1 = $session['user']['level'] - 1;
        $lev2 = $session['user']['level'] + 2;
        $last = date('Y-m-d H:i:s', strtotime('-'.getsetting('LOGINTIMEOUT', 900).' sec'));
        $id = $session['user']['acctid'];
        $loc = addslashes($location);

        $sql = 'SELECT acctid, name, race, alive, location, sex, level, laston, '.
            'loggedin, login, pvpflag, clanshort, clanrank, dragonkills, '.
            DB::prefix('accounts').'.clanid FROM '.
            DB::prefix('accounts').' LEFT JOIN '.
            DB::prefix('clans').' ON '.DB::prefix('clans').'.clanid='.
            DB::prefix('accounts').'.clanid WHERE (locked=0) '.
            'AND (slaydragon=0) AND '.
            "(age>$days OR dragonkills>0 OR pk>0 OR experience>$exp) ".
            (-1 == $levdiff ? '' : "AND (level>=$lev1 AND level<=$lev2)").' AND (alive=1) '.
            "AND (laston<'$last' OR loggedin=0) AND (acctid<>$id) ".
            "ORDER BY location='$loc' DESC, location, level DESC, ".
            'experience DESC, dragonkills DESC';
    }
    $result = DB::query($sql);

    $pvp = [];

    while ($row = DB::fetch_assoc($result))
    {
        $pvp[] = $row;
    }

    $pvp = modulehook('pvpmodifytargets', $pvp);

    tlschema('pvp');
    $n = translate_inline('Name');
    $l = translate_inline('Level');
    $loc = translate_inline('Location');
    $ops = translate_inline('Ops');
    $bio = translate_inline('Bio');
    $att = translate_inline('Attack');

    rawoutput("<table class='ui very compact striped selectable table'>");
    rawoutput("<thead><tr><th>$n</th><th>$l</th><th>$loc</th><th>$ops</th></tr></thead>");
    $loc_counts = [];
    $num = count($pvp);
    $j = 0;

    for ($i = 0; $i < $num; $i++)
    {
        $row = $pvp[$i];

        if (isset($row['invalid']) && $row['invalid'])
        {
            continue;
        }

        if (! isset($loc_counts[$row['location']]))
        {
            $loc_counts[$row['location']] = 0;
        }
        $loc_counts[$row['location']]++;

        if ($row['location'] != $location)
        {
            continue;
        }
        $j++;
        $biolink = 'bio.php?char='.$row['acctid'].'&ret='.urlencode($_SERVER['REQUEST_URI']);
        addnav('', $biolink);
        rawoutput("<tr class='".($j % 2 ? 'trlight' : 'trdark')."'>");
        rawoutput('<td>');

        if ($row['clanshort'] > '' && $row['clanrank'] > CLAN_APPLICANT)
        {
            output_notl('%s&lt;`2%s%s&gt;`0 ',
                    $clanrankcolors[ceil($row['clanrank'] / 10)], $row['clanshort'],
                    $clanrankcolors[ceil($row['clanrank'] / 10)], true);
        }
        output_notl('`@%s`0', $row['name']);
        rawoutput('</td>');
        rawoutput('<td>');
        output_notl('%s', $row['level']);
        rawoutput('</td>');
        rawoutput('<td>');
        output_notl('%s', $row['location']);
        rawoutput('</td>');
        rawoutput("<td>[ <a href='$biolink'>$bio</a> | ");

        if ($row['pvpflag'] > $pvptimeout)
        {
            output('`i(Attacked too recently)`i');
        }
        elseif ($location != $row['location'])
        {
            output("`i(Can't reach them from here)`i");
        }
        elseif (isset($row['invalid']) && '' != $row['invalid'])
        {
            if (1 == $row['invalid'])
            {
                $row['invalid'] = translate_inline('Unable to attack');
            }
            output_not('`i`4(%s`4)`i', $row['invalid']);
        }
        else
        {
            rawoutput("<a href='$link$extra&name=".$row['acctid']."'>$att</a>");
            addnav('', "$link$extra&name=".$row['acctid']);
        }
        rawoutput(' ]</td>');
        rawoutput('</tr>');
    }

    if (! isset($loc_counts[$location]) || 0 == $loc_counts[$location])
    {
        $noone = translate_inline('`iThere are no available targets.`i');
        output_notl("<tr><td align='center' colspan='4'>$noone</td></tr>", true);
    }
    rawoutput('</table>', true);

    if (0 != $num && (! isset($loc_counts[$location]) || $loc_counts[$location] != $num))
    {
        output('`n`n`&As you listen to different people around you talking, you glean the following additional information:`n');

        foreach ($loc_counts as $loc => $count)
        {
            if ($loc == $location)
            {
                continue;
            }
            $args = modulehook('pvpcount', ['count' => $count, 'loc' => $loc]);

            if (isset($args['handled']) && $args['handled'])
            {
                continue;
            }

            if (1 == $count)
            {
                output('`&There is `^%s`& person sleeping in %s whom you might find interesting.`0`n', $count, $loc);
            }
            else
            {
                output('`&There are `^%s`& people sleeping in %s whom you might find interesting.`0`n', $count, $loc);
            }
        }
    }
    tlschema();
}
