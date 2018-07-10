<?php
    page_header('Clan Listing');
    $registrar = getsetting('clanregistrar', '`%Karissa');
    addnav('Clan Options');
    $order = (int) httpget('order');

    switch ($order)
    {
        case 1:
            $order = 'clanname ASC';
            break;
        case 2:
            $order = 'clanshort ASC';
            break;
        default:
            $order = 'c DESC';
            break;
    }
    $sql = 'SELECT MAX('.DB::prefix('clans').'.clanid) AS clanid, MAX(clanshort) AS clanshort, MAX(clanname) AS clanname,count('.DB::prefix('accounts').'.acctid) AS c FROM '.DB::prefix('clans').' LEFT JOIN '.DB::prefix('accounts').' ON '.DB::prefix('clans').'.clanid='.DB::prefix('accounts').'.clanid AND clanrank>'.CLAN_APPLICANT.' GROUP BY '.DB::prefix('clans').".clanid ORDER BY $order";
    $result = DB::query($sql);

    if (DB::num_rows($result) > 0)
    {
        output('`7You ask %s`7 for the clan listings.  She points you toward a marquee board near the entrance of the lobby that lists the clans.`0`n`n', $registrar);
        $v = 0;
        $memb_n = translate_inline('(%s members)');
        $memb_1 = translate_inline('(%s member)');
        rawoutput('<table class="ui very compact striped selectable table">');

        while ($row = DB::fetch_assoc($result))
        {
            if (0 == $row['c'])
            {
                $sql = 'DELETE FROM '.DB::prefix('clans')." WHERE clanid={$row['clanid']}";
                DB::query($sql);
            }
            else
            {
                rawoutput('<tr><td>', true);

                if (1 == $row['c'])
                {
                    $memb = sprintf($memb_1, $row['c']);
                }
                else
                {
                    $memb = sprintf($memb_n, $row['c']);
                }
                output_notl("&#149; &#60;%s&#62; <a href='clan.php?detail=%s'>%s</a> %s`n",
                        $row['clanshort'],
                        $row['clanid'],
                        htmlentities(full_sanitize($row['clanname']), ENT_COMPAT, getsetting('charset', 'UTF-8')),
                        $memb, true);
                rawoutput('</td></tr>');
                addnav('', "clan.php?detail={$row['clanid']}");
                $v++;
            }
        }
        rawoutput('</table>', true);
        addnav('Return to the Lobby', 'clan.php');
        addnav('Sorting');
        addnav('Order by Membercount', 'clan.php?op=list&order=0');
        addnav('Order by Clanname', 'clan.php?op=list&order=1');
        addnav('Order by Shortname', 'clan.php?op=list&order=2');
    }
    else
    {
        output('`7You ask %s`7 for the clan listings.  She stares at you blankly for a few moments, then says, "`5Sorry pal, no one has had enough gumption to start up a clan yet.  Maybe that should be you, eh?`7"', $registrar);
        addnav('Apply for a New Clan', 'clan.php?op=new');
        addnav('Return to the Lobby', 'clan.php');
    }

    page_footer();
