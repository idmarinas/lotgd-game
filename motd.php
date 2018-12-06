<?php

// addnews ready
// translator ready
// mail ready
define('ALLOW_ANONYMOUS', true);
define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';
require_once 'lib/commentary.php';
require_once 'lib/nltoappon.php';
require_once 'lib/motd.php';

tlschema('motd');

$op = httpget('op');
$id = httpget('id');

addcommentary();
popup_header('LoGD Message of the Day (MoTD)');

if ($session['user']['superuser'] & SU_POST_MOTD)
{
    $addm = translate_inline('Add MoTD');
    $addp = translate_inline('Add Poll');
    rawoutput(" [ <a href='motd.php?op=add'>$addm</a> | <a href='motd.php?op=addpoll'>$addp</a> ]<br/><br/>");
}

if ('vote' == $op)
{
    $motditem = httppost('motditem');
    $choice = httppost('choice');
    $sql = 'DELETE FROM '.DB::prefix('pollresults')." WHERE motditem='$motditem' AND account='{$session['user']['acctid']}'";
    DB::query($sql);
    $sql = 'INSERT INTO '.DB::prefix('pollresults')." (choice,account,motditem) VALUES ('$choice','{$session['user']['acctid']}','$motditem')";
    DB::query($sql);
    invalidatedatacache("poll-$motditem");
    header('Location: motd.php');

    exit();
}

if ('add' == $op || 'addpoll' == $op || 'del' == $op)
{
    if ($session['user']['superuser'] & SU_POST_MOTD)
    {
        if ('add' == $op)
        {
            motd_form($id);
        }
        elseif ('addpoll' == $op)
        {
            motd_poll_form($id);
        }
        elseif ('del' == $op)
        {
            motd_del($id);
        }
    }
    else
    {
        if ($session['user']['loggedin'])
        {
            $session['user']['experience'] =
                round($session['user']['experience'] * 0.9, 0);
            addnews('%s was penalized for attempting to defile the gods.',
                    $session['user']['name']);
            output("You've attempted to defile the gods.  You are struck with a wand of forgetfulness.  Some of what you knew, you no longer know.");
            saveuser();
        }
    }
}

if ('' == $op)
{
    $count = getsetting('motditems', 5);
    $newcount = (int) httppost('newcount');

    if (! httppost('proceed'))
    {
        $newcount = 0;
    }
    /*
    motditem("Beta!","Please see the beta message below.","","", "");
    */
    $m = httppost('month');

    if ($m > '')
    {
        $sql = 'SELECT motd.*, name AS motdauthorname FROM '.DB::prefix('motd').' AS `motd`LEFT JOIN '.DB::prefix('accounts')." AS `acct` ON acct.acctid = motd.motdauthor WHERE motddate >= '{$m}-01' AND motddate <= '{$m}-31' ORDER BY motddate DESC";
        $result = DB::query($sql);
    }
    else
    {
        $sql = 'SELECT motd.*, name AS motdauthorname FROM '.DB::prefix('motd').' AS `motd` LEFT JOIN '.DB::prefix('accounts')." AS `acct` ON acct.acctid = motd.motdauthor ORDER BY motddate DESC limit $newcount,".($newcount + $count);
        $result = DB::query($sql);
    }
    rawoutput('<div class="ui divided items">');

    foreach ($result as $row)
    {
        $session['user']['lastmotd'] = $session['user']['lastmotd'] ?? 0;
        $row['motdauthorname'] = $row['motdauthorname'] ?: '`@Green Dragon Staff`0';

        if (0 == $row['motdtype'])
        {
            motditem($row['motdtitle'], $row['motdbody'], $row['motdauthorname'], $row['motddate'], $row['motditem']);
        }
        else
        {
            pollitem($row['motditem'], $row['motdtitle'], $row['motdbody'], $row['motdauthorname'], $row['motddate'], $row['motditem']);
        }
    }
    rawoutput('</div>');
    /*
    motditem("Beta!","For those who might be unaware, this website is still in beta mode.  I'm working on it when I have time, which generally means a couple of changes a week.  Feel free to drop suggestions, I'm open to anything :-)","","", "");
    */

    $result = DB::query('SELECT mid(motddate,1,7) AS d, count(*) AS c FROM '.DB::prefix('motd').' GROUP BY d ORDER BY d DESC');
    $row = DB::fetch_assoc($result);
    rawoutput("<form action='motd.php' method='POST' class='ui form'>");
    output('MoTD Archives:');
    rawoutput("<select class='ui dropdown' name='month' onChange='this.form.submit();' >");
    rawoutput("<option value=''>--Current--</option>");

    while ($row = DB::fetch_assoc($result))
    {
        $time = strtotime("{$row['d']}-01");
        $m = translate_inline(date('M', $time));
        rawoutput("<option value='{$row['d']}'".(httpget('month') == $row['d'] ? ' selected' : '').">$m".date(', Y', $time)." ({$row['c']})</option>");
    }
    rawoutput('</select>'.tlbutton_clear());
    $showmore = translate_inline('Show more');
    rawoutput("<input type='hidden' name='newcount' value='".($count + $newcount)."'>");
    rawoutput("<input type='submit' value='$showmore' name='proceed'  class='ui button'>");
    rawoutput("<input type='submit' value='".translate_inline('Submit')."' class='ui button'>");
    rawoutput('</form>');

    //-- Show comentary only if user are online
    if (isset($session['user']['online']) && $session['user']['online'])
    {
        commentdisplay('`n`@Commentary:`0`n', 'motd');
    }
}

$session['needtoviewmotd'] = false;

$sql = 'SELECT motddate FROM '.DB::prefix('motd').' ORDER BY motditem DESC LIMIT 1';
$result = DB::query($sql);
$row = DB::fetch_assoc($result);
$session['user']['lastmotd'] = $row['motddate'];

popup_footer();
