<?php

// translator ready
// addnews ready
// mail ready

// New Hall of Fame features by anpera
// http://www.anpera.net/forum/viewforum.php?f=27

require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/villagenav.php';

tlschema('hof');

$superusermask = SU_HIDE_FROM_LEADERBOARD;
$standardwhere = "(locked=0 AND (superuser & $superusermask) = 0)";

page_header('Hall of Fame');
checkday();

addnav('Navigation');
villagenav();

$playersperpage = 50;

$op = httpget('op');

if ('' == $op)
{
    $op = 'kills';
}
$subop = httpget('subop');

if ('' == $subop)
{
    $subop = 'most';
}

$sql = 'SELECT count(acctid) AS c FROM '.DB::prefix('accounts')." WHERE $standardwhere";
$extra = '';

if ('kills' == $op)
{
    $extra = ' AND dragonkills > 0';
}
elseif ('days' == $op)
{
    $extra = ' AND dragonkills > 0 AND bestdragonage > 0';
}
$result = DB::query($sql.$extra);
$row = DB::fetch_assoc($result);
$totalplayers = $row['c'];

$page = (int) httpget('page');

if (0 == $page)
{
    $page = 1;
}
$pageoffset = $page;

if ($pageoffset > 0)
{
    $pageoffset--;
}
$pageoffset *= $playersperpage;
$from = $pageoffset + 1;
$to = min($pageoffset + $playersperpage, $totalplayers);
$limit = "$pageoffset,$playersperpage";

addnav('Warrior Rankings');
addnav('Dragon Kills', "hof.php?op=kills&subop=$subop&page=1");
addnav('Gold', "hof.php?op=money&subop=$subop&page=1");
addnav('Gems', "hof.php?op=gems&subop=$subop&page=1");
addnav('Charm', "hof.php?op=charm&subop=$subop&page=1");
addnav('Toughness', "hof.php?op=tough&subop=$subop&page=1");
addnav('Resurrections', "hof.php?op=resurrects&subop=$subop&page=1");
addnav('Dragon Kill Speed', "hof.php?op=days&subop=$subop&page=1");
addnav('Sorting');
addnav('Best', "hof.php?op=$op&subop=most&page=$page");
addnav('Worst', "hof.php?op=$op&subop=least&page=$page");
addnav('Other Stats');
modulehook('hof-add', []);

if ($totalplayers > $playersperpage)
{
    addnav('Pages');

    for ($i = 0; $i < $totalplayers; $i += $playersperpage)
    {
        $pnum = ($i / $playersperpage + 1);
        $min = ($i + 1);
        $max = min($i + $playersperpage, $totalplayers);

        if ($page == $pnum)
        {
            addnav(['`b`#Page %s`0 (%s-%s)`b', $pnum, $min, $max], "hof.php?op=$op&subop=$subop&page=$pnum");
        }
        else
        {
            addnav(['Page %s (%s-%s)', $pnum, $min, $max], "hof.php?op=$op&subop=$subop&page=$pnum");
        }
    }
}

function display_table($title, $sql, $none = false, $foot = false,
        $data_header = false, $tag = false, $translate = false)
{
    global $session, $from, $to, $page, $playersperpage, $totalplayers;

    $title = translate_inline($title);

    if (false !== $foot)
    {
        $foot = translate_inline($foot);
    }

    if (false !== $none)
    {
        $none = translate_inline($none);
    }
    else
    {
        $none = translate_inline('No players found.');
    }

    if (false !== $data_header)
    {
        $data_header = translate_inline($data_header);
        reset($data_header);
    }

    if (false !== $tag)
    {
        $tag = translate_inline($tag);
    }
    $rank = translate_inline('Rank');
    $name = translate_inline('Name');

    if ($totalplayers > $playersperpage)
    {
        output('`c`b`^%s`0`b `7(Page %s: %s-%s of %s)`0`c`n', $title, $page, $from, $to, $totalplayers);
    }
    else
    {
        output_notl('`c`b`^%s`0`b`c`n', $title);
    }
    rawoutput('<table class="ui very compact striped selectable table">');
    rawoutput('<thead><tr>');
    output_notl("<th>`b$rank`b</th><th>`b$name`b</th>", true);

    if (false !== $data_header)
    {
        for ($i = 0; $i < count($data_header); $i++)
        {
            output_notl("<th>`b{$data_header[$i]}`b</th>", true);
        }
    }
    rawoutput('</tr></thead>');
    $result = DB::query($sql);

    if (0 == DB::num_rows($result))
    {
        $size = (false === $data_header) ? 2 : 2 + count($data_header);
        output_notl("<tr class='trlight'><td colspan='$size'>`&$none`0</td></tr>", true);
    }
    else
    {
        $i = -1;

        while ($row = DB::fetch_assoc($result))
        {
            $i++;

            if ($row['name'] == $session['user']['name'])
            {
                rawoutput("<tr class='hilight'>");
            }
            else
            {
                rawoutput("<tr class='".($i % 2 ? 'trlight' : 'trdark')."'>");
            }
            output_notl('<td>%s</td><td>`&%s`0</td>', ($i + $from), $row['name'], true);

            if (false !== $data_header)
            {
                for ($j = 0; $j < count($data_header); $j++)
                {
                    $id = 'data'.($j + 1);
                    $val = $row[$id];

                    if (isset($translate[$id]) &&
                            1 == $translate[$id] && ! is_numeric($val))
                    {
                        $val = translate_inline($val);
                    }

                    if (false !== $tag)
                    {
                        $val = $val.' '.$tag[$j];
                    }
                    output_notl('<td>%s</td>', $val, true);
                }
            }
            rawoutput('</tr>');
        }
    }
    rawoutput('</table>');

    if (false !== $foot)
    {
        output_notl('`n`c%s`c', $foot);
    }
}

if ('days' == $op)
{
    if ('least' == $subop)
    {
        $order = 'DESC';
        $meop = '>=';
    }
    else
    {
        $order = 'ASC';
        $meop = '<=';
    }
}
else
{
    if ('least' == $subop)
    {
        $order = 'ASC';
        $meop = '<=';
    }
    else
    {
        $order = 'DESC';
        $meop = '>=';
    }
}

$sexsel = "IF(sex,'`%Female`0','`!Male`0')";
$racesel = "IF(race!='0' and race!='',race,'".RACE_UNKNOWN."')";

$round_money = '-2';

if ('money' == $op)
{
    // works only in mysql 5+ due to the derived table stuff
    $sql = "SELECT name,(round(
						(CAST(goldinbank as signed)+cast(gold as signed))
						*(1+0.05*(rand())),$round_money
						)) as sort1
		FROM ".DB::prefix('accounts')." WHERE $standardwhere ORDER BY sort1 $order, level $order, experience $order, acctid $order LIMIT $limit";
    // for formatting, we need another query...
    $sql = "SELECT name,format(sort1,0) as data1 FROM ($sql) t";
    $me = 'SELECT count(acctid) AS count FROM '.DB::prefix('accounts')." WHERE $standardwhere
		AND round((CAST(goldinbank as signed)+cast(gold as signed))*(1+0.05*(rand())),$round_money)
		$meop ".($session['user']['goldinbank'] + $session['user']['gold']);
    //edward pointed out that a cast is necessary as signed+unsigned=boffo
    //	$sql = "SELECT name,(goldinbank+gold+round((((rand()*10)-5)/100)*(goldinbank+gold))) AS data1 FROM " . DB::prefix("accounts") . " WHERE $standardwhere ORDER BY data1 $order, level $order, experience $order, acctid $order LIMIT $limit";
    // $me = "SELECT count(acctid) AS count FROM ".DB::prefix("accounts")." WHERE $standardwhere AND (goldinbank+gold+round((((rand()*10)-5)/100)*(goldinbank+gold))) $meop ".($session['user']['goldinbank'] + $session['user']['gold']);
    debug($sql);
    $adverb = 'richest';

    if ('least' == $subop)
    {
        $adverb = 'poorest';
    }
    $title = "The $adverb warriors in the land";
    $foot = '(Gold Amount is accurate to +/- 5%)';
    $headers = ['Estimated Gold'];
    $tags = ['gold'];
    $table = [$title, $sql, false, $foot, $headers, $tags];
}
elseif ('gems' == $op)
{
    $sql = 'SELECT name FROM '.DB::prefix('accounts')." WHERE $standardwhere ORDER BY gems $order, level $order, experience $order, acctid $order LIMIT $limit";
    $me = 'SELECT count(acctid) AS count FROM '.DB::prefix('accounts')." WHERE $standardwhere AND gems $meop {$session['user']['gems']}";

    if ('least' == $subop)
    {
        $adverb = 'least';
    }
    else
    {
        $adverb = 'most';
    }
    $title = "The warriors with the $adverb gems in the land";
    $table = [$title, $sql];
}
elseif ('charm' == $op)
{
    $sql = "SELECT name,$sexsel AS data1, $racesel AS data2 FROM ".DB::prefix('accounts')." WHERE $standardwhere ORDER BY charm $order, level $order, experience $order, acctid $order LIMIT $limit";
    $me = 'SELECT count(acctid) AS count FROM '.DB::prefix('accounts')." WHERE $standardwhere AND charm $meop {$session['user']['charm']}";
    $adverb = 'most beautiful';

    if ('least' == $subop)
    {
        $adverb = 'ugliest';
    }
    $title = "The $adverb warriors in the land.";
    $headers = ['Gender', 'Race'];
    $translate = ['data1' => 1, 'data2' => 1];
    $table = [$title, $sql, false, false, $headers, false, $translate];
}
elseif ('tough' == $op)
{
    $sql = "SELECT name,level AS data2 , $racesel as data1 FROM ".DB::prefix('accounts')." WHERE $standardwhere ORDER BY maxhitpoints $order, level $order, experience $order, acctid $order LIMIT $limit";
    $me = 'SELECT count(acctid) AS count FROM '.DB::prefix('accounts')." WHERE $standardwhere AND maxhitpoints $meop {$session['user']['maxhitpoints']}";
    $adverb = 'toughest';

    if ('least' == $subop)
    {
        $adverb = 'wimpiest';
    }
    $title = "The $adverb warriors in the land";
    $headers = ['Race', 'Level'];
    $translate = ['data1' => 1];
    $table = [$title, $sql, false, false, $headers, false, $translate];
}
elseif ('resurrects' == $op)
{
    $sql = 'SELECT name,level AS data1 FROM '.DB::prefix('accounts')." WHERE $standardwhere ORDER BY resurrections $order, level $order, experience $order, acctid $order LIMIT $limit";
    $me = 'SELECT count(acctid) AS count FROM '.DB::prefix('accounts')." WHERE $standardwhere AND resurrections $meop {$session['user']['resurrections']}";
    $adverb = 'most suicidal';

    if ('least' == $subop)
    {
        $adverb = 'least suicidal';
    }
    $title = "The $adverb warriors in the land";
    $headers = ['Level'];
    $table = [$title, $sql, false, false, $headers, false];
}
elseif ('days' == $op)
{
    $unk = translate_inline('Unknown');
    $sql = "SELECT name, IF(bestdragonage,bestdragonage,'$unk') AS data1 FROM ".DB::prefix('accounts')." WHERE $standardwhere $extra ORDER BY bestdragonage $order, level $order, experience $order, acctid $order LIMIT $limit";
    $me = 'SELECT count(acctid) AS count FROM '.DB::prefix('accounts')." WHERE $standardwhere $extra AND bestdragonage $meop {$session['user']['bestdragonage']}";
    $adverb = 'fastest';

    if ('least' == $subop)
    {
        $adverb = 'slowest';
    }
    $title = "Heroes with the $adverb dragon kills in the land";
    $headers = ['Best Days'];
    $none = 'There are no heroes in the land.';
    $table = [$title, $sql, $none, false, $headers, false];
}
else
{
    $unk = translate_inline('Unknown');
    $sql = "SELECT name,dragonkills AS data1,level AS data2, IF(dragonage,dragonage,'$unk') AS data3, IF(bestdragonage,bestdragonage,'$unk') AS data4 FROM ".DB::prefix('accounts')." WHERE $standardwhere $extra ORDER BY dragonkills $order,level $order,experience $order, acctid $order LIMIT $limit";

    if ($session['user']['dragonkills'] > 0)
    {
        $me = 'SELECT count(acctid) AS count FROM '.DB::prefix('accounts')." WHERE $standardwhere $extra AND dragonkills $meop {$session['user']['dragonkills']}";
    }
    $adverb = 'most';

    if ('least' == $subop)
    {
        $adverb = 'least';
    }
    $title = "Heroes with the $adverb dragon kills in the land";
    $headers = ['Kills', 'Level', 'Days', 'Best Days'];
    $none = 'There are no heroes in the land.';
    $table = [$title, $sql, $none, false, $headers, false];
}

if (isset($table) && is_array($table))
{
    call_user_func_array('display_table', $table);

    if (isset($me) && $me > '' && $totalplayers)
    {
        $meresult = DB::query($me);
        $row = DB::fetch_assoc($meresult);
        $pct = round(100 * $row['count'] / $totalplayers, 0);

        if ($pct < 1)
        {
            $pct = 1;
        }
        output('`c`7You rank within around the top `&%s`7%% in this listing.`0`c', $pct);
    }
}

page_footer();
