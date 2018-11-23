<?php

if (1 == $display)
{
    $q = '';

    if ($query)
    {
        $q = "&q=$query";
    }
    $ops = translate_inline('Ops');
    $acid = translate_inline('ID');
    $nm = translate_inline('Name');
    $lon = translate_inline('Last On');
    $hits = translate_inline('Hits');
    $lip = translate_inline('Last IP');
    $email = translate_inline('Email');
    $ed = translate_inline('Edit');
    $del = translate_inline('Del');
    $conf = translate_inline('Are you sure you wish to delete this user?');
    $ban = translate_inline('Ban');
    $log = translate_inline('Log');
    rawoutput("<table class='ui very compact striped selectable table'>");
    rawoutput("<thead><tr><th>$ops</th><th><a href='user.php?sort=acctid$q'>$acid</a></th><th><a href='user.php?sort=name$q'>$nm</a></th><th><a href='user.php?sort=laston$q'>$lon</a></th><th><a href='user.php?sort=gentimecount$q'>$hits</a></th><th><a href='user.php?sort=lastip$q'>$lip</a></th><th><a href='user.php?sort=emailaddress$q'>$email</a></th></tr></thead>");
    addnav('', "user.php?sort=acctid$q");
    addnav('', "user.php?sort=name$q");
    addnav('', "user.php?sort=laston$q");
    addnav('', "user.php?sort=gentimecount$q");
    addnav('', "user.php?sort=lastip$q");

    while ($row = DB::fetch_assoc($searchresult))
    {
        $laston = LotgdFormat::relativedate($row['laston']);
        $loggedin = (date('U') - strtotime($row['laston']) < getsetting('LOGINTIMEOUT', 900) && $row['loggedin']);

        if ($loggedin)
        {
            $laston = translate_inline('`#Online`0');
        }
        $row['laston'] = $laston;

        rawoutput('<tr>');
        rawoutput('<td nowrap>');
        rawoutput("[ <a href='user.php?op=edit&userid={$row['acctid']}$m' class='ui tooltip' data-content='$ed'><i class='icon edit'></i></a> | <a href='user.php?op=del&userid={$row['acctid']}' onClick=\"return confirm('$conf');\" class='ui tooltip' data-content='$del'><i class='icon trash'></i></a> | <a href='bans.php?op=setupban&userid={$row['acctid']}' class='ui tooltip' data-content='$ban'><i class='icon ban'></i></a> | <a href='user.php?op=debuglog&userid={$row['acctid']}' class='ui tooltip' data-content='$log'><i class='icon file outline'></i></a>]");
        addnav('', "user.php?op=edit&userid={$row['acctid']}$m");
        addnav('', "user.php?op=del&userid={$row['acctid']}");
        addnav('', "bans.php?op=setupban&userid={$row['acctid']}");
        addnav('', "user.php?op=debuglog&userid={$row['acctid']}");
        rawoutput('</td><td>');
        output_notl('%s', $row['acctid']);
        rawoutput('</td><td>');
        output_notl('(`^%s`0) `&%s`0', $row['level'], $row['name']);
        rawoutput('</td><td>');
        output_notl($row['laston']);
        rawoutput('</td><td>');
        output_notl('%s', $row['gentimecount']);
        rawoutput('</td><td>');
        output_notl('%s', $row['lastip']);
        rawoutput('</td><td>');
        output_notl('%s', $row['emailaddress']);
        rawoutput('</td></tr>');
        $gentimecount += $row['gentimecount'];
        $gentime += $row['gentime'];
    }
    rawoutput('</table>');
    output('Total hits: %s`n', $gentimecount);
    output('Total CPU time: %s seconds`n', round($gentime, 3));
    output('Average page gen time is %s seconds`n', round($gentime / max($gentimecount, 1), 4));
}
