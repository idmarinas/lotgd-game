<?php

if (1 == $display)
{
    $q = '';

    if ($query)
    {
        $q = "&q=$query";
    }
    $ops = translate_inline('Ops');
    $acid = translate_inline('AcctID');
    $login = translate_inline('Login');
    $nm = translate_inline('Name');
    $lev = translate_inline('Level');
    $lon = translate_inline('Last On');
    $hits = translate_inline('Hits');
    $lip = translate_inline('Last IP');
    $lid = translate_inline('Last ID');
    $ban = translate_inline('Ban');
    rawoutput("<table class='ui very compact striped selectable table'>");
    rawoutput("<thead><tr><th>$ops</th><th><a href='bans.php?sort=acctid$q'>$acid</a></th><th><a href='bans.php?sort=login$q'>$login</a></th><th><a href='bans.php?sort=name$q'>$nm</a></th><th><a href='bans.php?sort=level$q'>$lev</a></th><th><a href='bans.php?sort=laston$q'>$lon</a></th><th><a href='bans.php?sort=gentimecount$q'>$hits</a></th><th><a href='bans.php?sort=lastip$q'>$lip</a></th><th><a href='bans.php?sort=uniqueid$q'>$lid</a></th></tr></thead>");
    addnav('', "bans.php?sort=acctid$q");
    addnav('', "bans.php?sort=login$q");
    addnav('', "bans.php?sort=name$q");
    addnav('', "bans.php?sort=level$q");
    addnav('', "bans.php?sort=laston$q");
    addnav('', "bans.php?sort=gentimecount$q");
    addnav('', "bans.php?sort=lastip$q");
    addnav('', "bans.php?sort=uniqueid$q");
    $rn = 0;
    $oorder = '';

    while ($row = DB::fetch_assoc($searchresult))
    {
        $laston = LotgdFormat::relativedate($row['laston']);
        $loggedin =
            (date('U') - strtotime($row['laston']) <
             getsetting('LOGINTIMEOUT', 900) && $row['loggedin']);

        if ($loggedin)
        {
            $laston = translate_inline('`#Online`0');
        }
        $row['laston'] = $laston;

        if ($row[$order] != $oorder)
        {
            $rn++;
        }
        $oorder = $row[$order];
        rawoutput("<tr><td class='collapsing'>");
        rawoutput("[ <a href='bans.php?op=setupban&userid={$row['acctid']}'>$ban</a> ]");
        addnav('', "bans.php?op=setupban&userid={$row['acctid']}");
        rawoutput('</td><td>');
        output_notl('%s', $row['acctid']);
        rawoutput('</td><td>');
        output_notl('%s', $row['login']);
        rawoutput('</td><td>');
        output_notl('`&%s`0', $row['name']);
        rawoutput('</td><td>');
        output_notl('`^%s`0', $row['level']);
        rawoutput('</td><td>');
        output_notl('%s', $row['laston']);
        rawoutput('</td><td>');
        output_notl('%s', $row['gentimecount']);
        rawoutput('</td><td>');
        output_notl('%s', $row['lastip']);
        rawoutput('</td><td>');
        output_notl('%s', $row['uniqueid']);
        rawoutput('</td>');
        $gentimecount += $row['gentimecount'];
        $gentime += $row['gentime'];
    }
    rawoutput('</table>');
    output('Total hits: %s`n', $gentimecount);
    output('Total CPU time: %s seconds`n', round($gentime, 3));
    output('Average page gen time is %s seconds`n', round($gentime / max($gentimecount, 1), 4));
}
