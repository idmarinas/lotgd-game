<?php

// translator ready
// addnews ready
// mail ready

require_once 'common.php';
require_once 'lib/commentary.php';
require_once 'lib/superusernav.php';

tlschema('petition');

check_su_access(SU_EDIT_PETITIONS);

addcommentary();

//WHEN 0 THEN 2 WHEN 1 THEN 3 WHEN 2 THEN 7 WHEN 3 THEN 5 WHEN 4 THEN 1 WHEN 5 THEN 0 WHEN 6 THEN 4 WHEN 7 THEN 6
$statuses = [
    5 => '`$Top Level`0',
    4 => '`^Escalated`0',
    0 => '`bUnhandled`b',
    1 => 'In-Progress',
    6 => '`%Bug`0',
    7 => '`#Awaiting Points`0',
    3 => '`!Informational`0',
    2 => '`iClosed`i',
];

$statuses = modulehook('petition-status', $statuses);
$statuses = translate_inline($statuses);

$op = httpget('op');
$id = httpget('id');

if ('' != trim(httppost('insertcommentary')))
{
    /* Update the bug if someone adds comments as well */
    $sql = 'UPDATE '.DB::prefix('petitions')." SET closeuserid='{$session['user']['acctid']}',closedate='".date('Y-m-d H:i:s')."' WHERE petitionid='$id'";
    DB::query($sql);
}

// Eric decide he didn't want petitions to be manually deleted
//
//if ($op=="del"){
//	$sql = "DELETE FROM " . DB::prefix("petitions") . " WHERE petitionid='$id'";
//	DB::query($sql);
//	$sql = "DELETE FROM " . DB::prefix("commentary") . " WHERE section='pet-$id'";
//	DB::query($sql);
//	invalidatedatacache("petition_counts");
//	$op="";
//}
page_header('Petition Viewer');
superusernav();

if ('' == $op)
{
    $sql = 'DELETE FROM '.DB::prefix('petitions')." WHERE status=2 AND closedate<'".date('Y-m-d H:i:s', strtotime('-7 days'))."'";
    DB::query($sql);

    if (DB::affected_rows())
    {
        invalidatedatacache('petition_counts');
    }
    $setstat = httpget('setstat');

    if ('' != $setstat)
    {
        $sql = 'SELECT status FROM '.DB::prefix('petitions')." WHERE petitionid='$id'";
        $result = DB::query($sql);
        $row = DB::fetch_assoc($result);

        if ($row['status'] != $setstat)
        {
            $sql = 'UPDATE '.DB::prefix('petitions')." SET status='$setstat',closeuserid='{$session['user']['acctid']}',closedate='".date('Y-m-d H:i:s')."' WHERE petitionid='$id'";
            DB::query($sql);
            invalidatedatacache('petition_counts');
        }
    }
    reset($statuses);
    $sort = '';
    $pos = 0;

    foreach ($statuses as $key => $val)
    {
        $sort .= " WHEN $key THEN $pos";
        $pos++;
    }

    $petitionsperpage = 50;
    $sql = 'SELECT count(petitionid) AS c from '.DB::prefix('petitions');
    $result = DB::query($sql);
    $row = DB::fetch_assoc($result);
    $totalpages = ceil($row['c'] / $petitionsperpage);

    $page = httpget('page');

    if ('' == $page)
    {
        if (isset($session['petitionPage']))
        {
            $page = (int) $session['petitionPage'];
        }
        else
        {
            $page = 1;
        }
    }

    if ($page < 1)
    {
        $page = 1;
    }

    if ($page > $totalpages)
    {
        $page = $totalpages;
    }
    $session['petitionPage'] = $page;

    // No need to show the pages if there is only one.
    if (1 != $totalpages)
    {
        addnav('Page');

        for ($x = 1; $x <= $totalpages; $x++)
        {
            if ($page == $x)
            {
                addnav(['`b`#Page %s`0`b', $x], "viewpetition.php?page=$x");
            }
            else
            {
                addnav(['Page %s', $x], "viewpetition.php?page=$x");
            }
        }
    }

    if ($page > 1)
    {
        $limit = (($page - 1) * $petitionsperpage).','.$petitionsperpage;
    }
    else
    {
        $limit = "$petitionsperpage";
    }

    $sql =
    'SELECT
		petitionid,
		'.DB::prefix('accounts').'.name,
		'.DB::prefix('petitions').'.date,
		'.DB::prefix('petitions').'.status,
		'.DB::prefix('petitions').'.body,
		'.DB::prefix('petitions').".closedate,
		accts.name AS closer,
		CASE status $sort END AS sortorder
	FROM
		".DB::prefix('petitions').'
	LEFT JOIN
		'.DB::prefix('accounts').'
	ON	'.DB::prefix('accounts').'.acctid='.DB::prefix('petitions').'.author
	LEFT JOIN
		'.DB::prefix('accounts').' AS accts
	ON	accts.acctid='.DB::prefix('petitions').".closeuserid
	ORDER BY
		sortorder ASC,
		date ASC
	LIMIT $limit";
    $result = DB::query($sql);
    addnav('Petitions');
    addnav('Refresh', 'viewpetition.php');
    $num = translate_inline('Num');
    $ops = translate_inline('Ops');
    $from = translate_inline('From');
    $sent = translate_inline('Sent');
    $com = translate_inline('Com');
    $last = translate_inline('Last Updater');
    $when = translate_inline('Updated');
    $view = translate_inline('View');
    $close = translate_inline('Close');
    $mark = translate_inline('Mark');

    rawoutput("<table class='ui very compact striped selectable table'><thead><tr><th>$num</th><th>$ops</th><th>$from</th><th>$sent</th><th>$com</th><th>$last</th><th>$when</th></tr></thead>");
    $laststatus = -1;
    $catcount = [];

    while ($row = DB::fetch_assoc($result))
    {
        if (isset($catcount[$row['status']]))
        {
            $catcount[$row['status']]++;
        }
        else
        {
            $catcount[$row['status']] = 1;
        }
        $sql = 'SELECT count(commentid) AS c FROM '.DB::prefix('commentary')." WHERE section='pet-{$row['petitionid']}'";
        $res = DB::query($sql);
        $counter = DB::fetch_assoc($res);

        if (array_key_exists('status', $row) && $row['status'] != $laststatus)
        {
            rawoutput('<tr>');
            rawoutput("<td colspan='7' class='".strtolower(color_sanitize($statuses[$row['status']]))."'>");
            output_notl('%s', color_sanitize($statuses[$row['status']]), true);
            rawoutput('</td></tr>');
            $laststatus = $row['status'];
        }
        rawoutput('<tr>');
        rawoutput("<td class='collapsing'>");
        output_notl('%s', $row['petitionid']);
        rawoutput('</td>');
        rawoutput("<td class='collapsing'>[ ");
        rawoutput("<a data-tooltip='$view' href='viewpetition.php?op=view&id={$row['petitionid']}'><i class='unhide icon'></i></a>", true);
        rawoutput(" | <a data-tooltip='$close' href='viewpetition.php?setstat=2&id={$row['petitionid']}'><i class='green remove icon'></i></a>");
        output_notl(' | %s: ', $mark);
        output_notl("<a data-tooltip='".color_sanitize($statuses[0])."' href='viewpetition.php?setstat=0&id={$row['petitionid']}'>`b`&U`0`b</a>/", true);
        output_notl("<a data-tooltip='".color_sanitize($statuses[1])."' href='viewpetition.php?setstat=1&id={$row['petitionid']}'>`7P`0</a>/", true);
        //output_notl("<a data-tooltip='".color_sanitize($statuses[3])."' href='viewpetition.php?setstat=3&id={$row['petitionid']}'>`!I`0</a>/",true);
        output_notl("<a data-tooltip='".color_sanitize($statuses[4])."' href='viewpetition.php?setstat=4&id={$row['petitionid']}'>`^E`0</a>", true);
        //output_notl("<a data-tooltip='".color_sanitize($statuses[5])."' href='viewpetition.php?setstat=5&id={$row['petitionid']}'>`\$T`0</a>/",true);
        //output_notl("<a data-tooltip='".color_sanitize($statuses[6])."' href='viewpetition.php?setstat=6&id={$row['petitionid']}'>`%B`0</a>/",true);
        //output_notl("<a data-tooltip='".color_sanitize($statuses[7])."' href='viewpetition.php?setstat=7&id={$row['petitionid']}'>`#A`0</a>",true);
        rawoutput(' ]</td>');
        addnav('', "viewpetition.php?op=view&id={$row['petitionid']}");
        addnav('', "viewpetition.php?setstat=2&id={$row['petitionid']}");
        addnav('', "viewpetition.php?setstat=0&id={$row['petitionid']}");
        addnav('', "viewpetition.php?setstat=1&id={$row['petitionid']}");
        //addnav("","viewpetition.php?setstat=3&id={$row['petitionid']}");
        addnav('', "viewpetition.php?setstat=4&id={$row['petitionid']}");
        //addnav("","viewpetition.php?setstat=5&id={$row['petitionid']}");
        //addnav("","viewpetition.php?setstat=6&id={$row['petitionid']}");
        //addnav("","viewpetition.php?setstat=7&id={$row['petitionid']}");
        rawoutput('<td>');

        if ('' == $row['name'])
        {
            $v = substr($row['body'], 0, strpos($row['body'], '[email'));
            $v = preg_replace("'\\[PHPSESSID\\] = .*'", '', $v);
            $v = preg_replace("'[^a-zA-Z0-91234567890\\[\\]= @.!,?-]'", '', $v);
            // Make sure we don't get something too large.. 50 chars max
            $v = substr($v, 0, 50);
            output_notl('`$%s`0', $v);
        }
        else
        {
            output_notl('`&%s`0', $row['name']);
        }
        rawoutput('</td>');
        rawoutput('<td>');
        output_notl('`7%s`0', reltime(strtotime($row['date'])));
        rawoutput('</td>');
        rawoutput('<td>');
        output_notl('`#%s`0', $counter['c']);
        rawoutput('</td>');
        rawoutput('<td>');
        output_notl('`^%s`0', $row['closer']);
        rawoutput('</td>');
        rawoutput('<td>');

        if (0 != $row['closedate'])
        {
            output_notl('`7%s`0', reltime(strtotime($row['closedate'])));
        }
        rawoutput('</td>');
        rawoutput('</tr>');
    }
    rawoutput('</table>');
    //navlist with counter
    if ($catcount != [])
    {
        addnav('Overview');
    }

    foreach ($catcount as $categorynumber => $amount)
    {
        addnav(['`t%s`t(%s)', $statuses[$categorynumber], $amount], 'viewpetition.php?page='.((int) httpget('page')));
    }

    output('`i(Closed petitions will automatically delete themselves when they have been closed for 7 days)`i');
    output('`n`bKey:`b`n');
    rawoutput('<ul><li>');
    output('`$T = Top Level`0 petitions are for petitions that only server operators can take care of.');
    rawoutput('</li><li>');
    output("`^E = Escalated`0 petitions deal with an issue you can't handle for yourself.");
    output('Mark it escalated so someone with more permissions than you can deal with it.');
    rawoutput('</li><li>');
    output('`b`&U = Unhandled`0`b: No one is currently working on this problem, and it has not been dealt with yet.');
    rawoutput('</li><li>');
    output('P = In-Progress petitions are probably being worked on by someone else, so please leave them be unless they have been around for some time.');
    rawoutput('</li><li>');
    output('`%B = Bug/Suggestion`0 petitions are petitions that detail mistakes, bugs, misspellings, or suggestions for the game.');
    rawoutput('</li><li>');
    output('`#A = Awaiting Points`0 stuff wot is dun and needz teh points added (this is mostly for lotgd.net).');
    rawoutput('</li><li>');
    output('`!I = Informational`0 petitions are just around for others to view, either nothing needed to be done with them, or their issue has been dealt with, but you feel other admins could benefit from reading it.');
    rawoutput('</li><li>');
    output('`iClosed`i petitions are for you have dealt with an issue, these will auto delete when they have been closed for 7 days.');
    modulehook('petitions-descriptions', []);
    rawoutput('</li></ul>');
}
elseif ('view' == $op)
{
    addnav('Petitions');
    addnav('Details');
    $viewpageinfo = (int) httpget('viewpageinfo');

    if (1 == $viewpageinfo)
    {
        addnav('Hide Details', "viewpetition.php?op=view&id=$id}");
    }
    else
    {
        addnav('D?Show Details', "viewpetition.php?op=view&id=$id&viewpageinfo=1");
    }
    addnav('Navigation');
    addnav('V?Petition Viewer', 'viewpetition.php');

    addnav('User Ops');

    addnav('Petition Ops');

    if (count($statuses))
    {
        reset($statuses);
        foreach($statuses as $key => $val)
        {
            $plain = full_sanitize($val);
            addnav(['%s?Mark %s', substr($plain, 0, 1), $val], "viewpetition.php?setstat=$key&id=$id");
        }
    }

    $sql = 'SELECT '.DB::prefix('accounts').'.name,'.DB::prefix('accounts').'.login,'.DB::prefix('accounts').'.acctid,'.'author,date,closedate,status,petitionid,ip,body,pageinfo,'.'accts.name AS closer FROM '.DB::prefix('petitions').' LEFT JOIN '.DB::prefix('accounts ').'ON '.DB::prefix('accounts').'.acctid=author LEFT JOIN '.DB::prefix('accounts').' AS accts ON accts.acctid='."closeuserid WHERE petitionid='$id' ORDER BY date ASC";
    $result = DB::query($sql);
    $row = DB::fetch_assoc($result);
    addnav('User Ops');

    if (isset($row['login']))
    {
        addnav('View User Biography', 'bio.php?char='.$row['acctid'] .'&ret=%2Fviewpetition.php%3Fop%3Dview%26id='.$id);
    }

    if ($row['acctid'] > 0 && $session['user']['superuser'] & SU_EDIT_USERS)
    {
        addnav('User Ops');
        addnav('R?Edit User Record', "user.php?op=edit&userid={$row['acctid']}&returnpetition=$id");
    }

    if ($row['acctid'] > 0 && $session['user']['superuser'] & SU_EDIT_DONATIONS)
    {
        addnav('User Ops');
        addnav('Edit User Donations', 'donators.php?op=add1&name='.rawurlencode($row['login']).'&ret='.urlencode($_SERVER['REQUEST_URI']));
    }
    $write = translate_inline('Write Mail');
    // We assume that petitions are handled in default language
    $yourpeti = translate_mail('Your Petition', 0);
    $peti = translate_mail('Petition', 0);
    $row['body'] = str_replace('[charname]', translate_mail('[charname]', 0), $row['body']);
    $row['body'] = str_replace('[email]', translate_mail('[email]', 0), $row['body']);
    $row['body'] = str_replace('[description]', translate_mail('[description]', 0), $row['body']);
    // For email replies, make sure we don't overflow the URI buffer.
    $reppet = substr(stripslashes($row['body']), 0, 2000);
    //display given category, if any
    $array_body = explode("\n", $row['body']);
    $category_check = '[problem_type] = ';

    foreach ($array_body as $line)
    {
        $catpos = strpos($line, $category_check);

        if (false !== $catpos)
        {
            //cat found
            rawoutput('<h2>');
            output('`2Category: %s`n', substr($line, strlen($category_check) - 1));
            rawoutput('</h2>');
        }
    }
    output('`@From: ');

    if ($row['login'] > '')
    {
        rawoutput('<a href="mail.php?op=write&to='.rawurlencode($row['login']).'&body='.rawurlencode("\n\n----- $yourpeti -----\n$reppet")."&subject=RE:+$peti\" target=\"_blank\" onClick=\"Lotgd.embed(this)\"><img src='images/newscroll.GIF' width='16' height='16' alt='$write' border='0'></a>");
    }
    output_notl('`^`b%s`b`n', $row['name']);
    output('`@Date: `^`b%s`b (%s)`n', $row['date'], reltime(strtotime($row['date'])));
    output('`@Status: %s`n', $statuses[$row['status']]);

    if ($row['closedate'])
    {
        output('`@Last Update: `^%s`@ on `^%s (%s)`n', $row['closer'], $row['closedate'], reltime(strtotime($row['closedate'])));
    }
    output('`@Body:`^`n');
    output('`$[ipaddress] `^= `#%s`^`n', $row['ip']);
    $body = htmlentities(stripslashes($row['body']), ENT_COMPAT, getsetting('charset', 'ISO-8859-1'));
    $body = preg_replace("'([[:alnum:]_.-]+[@][[:alnum:]_.-]{2,}([.][[:alnum:]_.-]{2,})+)'i", "<a href='mailto:\\1?subject=RE: $peti&body=".str_replace('+', ' ', urlencode("\n\n----- $yourpeti -----\n".stripslashes($row['body'])))."'>\\1</a>", $body);
    $body = preg_replace("'([\\[][[:alnum:]_.-]+[\\]])'i", "<span class='colLtRed'>\\1</span>", $body);
    rawoutput("<span style='font-family: fixed-width'>".nl2br($body).'</span>');
    //position tracking
    if ('' != $row['body'])
    {
        $pos = strpos($row['body'], '[abuseplayer]') + 16;
        $endpos = strpos($row['body'], chr(10), $pos);

        if (false !== $pos)
        {
            $search = substr($row['body'], $pos, $endpos - $pos);
            $search = (int) $search;

            if (0 != $search)
            {
                modulehook('petition-abuse', ['acctid' => $search, 'abused' => $row['author']]);
            }
        }
    }
    commentdisplay('`n`@Commentary:`0`n', "pet-$id", 'Add information', 200);

    if ($viewpageinfo)
    {
        output('`n`n`@Page Info:`&`n');
        $row['pageinfo'] = stripslashes($row['pageinfo']);
        $body = htmlentities($row['pageinfo'], ENT_COMPAT, getsetting('charset', 'UTF-8'));
        $body = preg_replace("'([[:alnum:]_.-]+[@][[:alnum:]_.-]{2,}([.][[:alnum:]_.-]{2,})+)'i", "<a href='mailto:\\1?subject=RE: $peti&body=".str_replace('+', ' ', urlencode("\n\n----- $yourpeti -----\n".$row['body']))."'>\\1</a>", $body);
        $body = preg_replace("'([\\[][[:alnum:]_.-]+[\\]])'i", "<span class='colLtRed'>\\1</span>", $body);
        rawoutput("<span style='font-family: fixed-width'>".nl2br($body).'</span>');
    }
}

if ($id && '' != $op)
{
    $prevsql = 'SELECT p1.petitionid, p1.status FROM '.DB::prefix('petitions').' AS p1, '.DB::prefix('petitions')." AS p2
			WHERE p1.petitionid<'$id' AND p2.petitionid='$id' AND p1.status=p2.status ORDER BY p1.petitionid DESC LIMIT 1";
    $prevresult = DB::query($prevsql);
    $prevrow = DB::fetch_assoc($prevresult);

    if ($prevrow)
    {
        $previd = $prevrow['petitionid'];
        $s = $prevrow['status'];
        $status = $statuses[$s];
        addnav('Petitions');
        addnav(['Previous %s', $status], "viewpetition.php?op=view&id=$previd");
    }
    $nextsql = 'SELECT p1.petitionid, p1.status FROM '.DB::prefix('petitions').' AS p1, '.DB::prefix('petitions')." AS p2
			WHERE p1.petitionid>'$id' AND p2.petitionid='$id' AND p1.status=p2.status ORDER BY p1.petitionid ASC LIMIT 1";
    $nextresult = DB::query($nextsql);
    $nextrow = DB::fetch_assoc($nextresult);

    if ($nextrow)
    {
        $nextid = $nextrow['petitionid'];
        $s = $nextrow['status'];
        $status = $statuses[$s];
        addnav('Petitions');
        addnav(['Next %s', $status], "viewpetition.php?op=view&id=$nextid");
    }
}
page_footer();
