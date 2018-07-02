<?php

// translator ready
// addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/sanitize.php';
require_once 'lib/http.php';
require_once 'lib/moderate.php';

tlschema('moderate');

addcommentary();

check_su_access(SU_EDIT_COMMENTS);

require_once 'lib/superusernav.php';
superusernav();

addnav('Other');
addnav('Commentary Overview', 'moderate.php');
addnav('Reset Seen Comments', 'moderate.php?seen='.rawurlencode(date('Y-m-d H:i:s')));
addnav('B?Player Bios', 'bios.php');

if ($session['user']['superuser'] & SU_AUDIT_MODERATION)
{
    addnav('Audit Moderation', 'moderate.php?op=audit');
}
addnav('Review by Moderator');
addnav('Commentary');
addnav('Sections');
addnav('Modules');
addnav('Clan Halls');

$op = httpget('op');

if ('commentdelete' == $op)
{
    $comment = httppost('comment');

    if (httppost('delnban') > '')
    {
        $sql = 'SELECT DISTINCT uniqueid,author FROM '.DB::prefix('commentary').' INNER JOIN '.DB::prefix('accounts')." ON acctid=author WHERE commentid IN ('".join("','", array_keys($comment))."')";
        $result = DB::query($sql);
        $untildate = date('Y-m-d H:i:s', strtotime('+3 days'));
        $reason = httppost('reason');
        $reason0 = httppost('reason0');
        $default = 'Banned for comments you posted.';

        if ($reason0 != $reason && $reason0 != $default)
        {
            $reason = $reason0;
        }

        if ('' == $reason)
        {
            $reason = $default;
        }

        while ($row = DB::fetch_assoc($result))
        {
            $sql = 'SELECT * FROM '.DB::prefix('bans')." WHERE uniqueid = '{$row['uniqueid']}'";
            $result2 = DB::query($sql);
            $sql = 'INSERT INTO '.DB::prefix('bans')." (uniqueid,banexpire,banreason,banner) VALUES ('{$row['uniqueid']}','$untildate','$reason','".addslashes($session['user']['name'])."')";
            $sql2 = 'UPDATE '.DB::prefix('accounts')." SET loggedin=0 WHERE acctid={$row['author']}";

            if (DB::num_rows($result2) > 0)
            {
                $row2 = DB::fetch_assoc($result2);

                if ($row2['banexpire'] < $untildate)
                {
                    //don't enter a new ban if a longer lasting one is
                    //already here.
                    DB::query($sql);
                    DB::query($sql2);
                }
            }
            else
            {
                DB::query($sql);
                DB::query($sql2);
            }
        }
    }

    if (! isset($comment) || ! is_array($comment))
    {
        $comment = [];
    }
    $sql = 'SELECT '.
        DB::prefix('commentary').'.*,'.DB::prefix('accounts').'.name,'.
        DB::prefix('accounts').'.login, '.DB::prefix('accounts').'.clanrank,'.
        DB::prefix('clans').'.clanshort FROM '.DB::prefix('commentary').
        ' INNER JOIN '.DB::prefix('accounts').' ON '.
        DB::prefix('accounts').'.acctid = '.DB::prefix('commentary').
        '.author LEFT JOIN '.DB::prefix('clans').' ON '.
        DB::prefix('clans').'.clanid='.DB::prefix('accounts').
        ".clanid WHERE commentid IN ('".join("','", array_keys($comment))."')";
    $result = DB::query($sql);
    $invalsections = [];

    while ($row = DB::fetch_assoc($result))
    {
        $sql = 'INSERT LOW_PRIORITY INTO '.DB::prefix('moderatedcomments').
            " (moderator,moddate,comment) VALUES ('{$session['user']['acctid']}','".date('Y-m-d H:i:s')."','".addslashes(serialize($row))."')";
        DB::query($sql);
        $invalsections[$row['section']] = 1;
    }
    $sql = 'DELETE FROM '.DB::prefix('commentary')." WHERE commentid IN ('".join("','", array_keys($comment))."')";
    DB::query($sql);
    $return = httpget('return');
    $return = cmd_sanitize($return);
    $return = substr($return, strrpos($return, '/') + 1);

    if (false === strpos($return, '?') && false !== strpos($return, '&'))
    {
        $x = strpos($return, '&');
        $return = substr($return, 0, $x - 1).'?'.substr($return, $x + 1);
    }

    foreach ($invalsections as $key => $dummy)
    {
        invalidatedatacache("comments-$key");
    }
    //update moderation cache
    invalidatedatacache('comments-or11');
    redirect($return);
}

$seen = httpget('seen');

if ($seen > '')
{
    $session['user']['recentcomments'] = $seen;
}

page_header('Comment Moderation');

if ('' == $op)
{
    $area = httpget('area');
    $link = 'moderate.php'.($area ? "?area=$area" : '');
    $refresh = translate_inline('Refresh');
    rawoutput("<form action='$link' method='POST'>");
    rawoutput("<input type='submit' class='ui button' value='$refresh'>");
    rawoutput('</form>');
    addnav('', "$link");

    if ('' == $area)
    {
        talkform('X', 'says');
        commentmoderate('', '', 'X', 100, 'says', false, true);
    }
    else
    {
        commentmoderate('', $area, 'X', 100);
        talkform($area, 'says');
    }
}
elseif ('audit' == $op)
{
    $subop = httpget('subop');

    if ('undelete' == $subop)
    {
        $unkeys = httppost('mod');

        if ($unkeys && is_array($unkeys))
        {
            $sql = 'SELECT * FROM '.DB::prefix('moderatedcomments')." WHERE modid IN ('".join("','", array_keys($unkeys))."')";
            $result = DB::query($sql);

            while ($row = DB::fetch_assoc($result))
            {
                $comment = unserialize($row['comment']);
                $id = addslashes($comment['commentid']);
                $postdate = addslashes($comment['postdate']);
                $section = addslashes($comment['section']);
                $author = addslashes($comment['author']);
                $comment = addslashes($comment['comment']);
                $sql = 'INSERT LOW_PRIORITY INTO '.DB::prefix('commentary')." (commentid,postdate,section,author,comment) VALUES ('$id','$postdate','$section','$author','$comment')";
                DB::query($sql);
                invalidatedatacache("comments-$section");
            }
            $sql = 'DELETE FROM '.DB::prefix('moderatedcomments')." WHERE modid IN ('".join("','", array_keys($unkeys))."')";
            DB::query($sql);
        }
        else
        {
            output('No items selected to undelete -- Please try again`n`n');
        }
    }
    $sql = 'SELECT DISTINCT acctid, name FROM '.DB::prefix('accounts').
        ' INNER JOIN '.DB::prefix('moderatedcomments').
        ' ON acctid=moderator ORDER BY name';
    $result = DB::query($sql);
    addnav('Commentary');
    addnav('Sections');
    addnav('Modules');
    addnav('Clan Halls');
    addnav('Review by Moderator');
    tlschema('notranslate');

    while ($row = DB::fetch_assoc($result))
    {
        addnav(' ?'.$row['name'], "moderate.php?op=audit&moderator={$row['acctid']}");
    }
    tlschema();
    addnav('Commentary');
    output('`c`bComment Auditing`b`c');
    $ops = translate_inline('Ops');
    $mod = translate_inline('Moderator');
    $when = translate_inline('When');
    $com = translate_inline('Comment');
    $unmod = translate_inline('Unmoderate');
    rawoutput("<form action='moderate.php?op=audit&subop=undelete' method='POST'>");
    addnav('', 'moderate.php?op=audit&subop=undelete');
    rawoutput("<table class='ui very compact striped selectable table'>");
    rawoutput("<thead><tr><td>$ops</td><td>$mod</td><td>$when</td><td>$com</td></tr></thead>");
    $limit = '75';
    $where = '1=1 ';
    $moderator = httpget('moderator');

    if ($moderator > '')
    {
        $where .= "AND moderator=$moderator ";
    }
    $sql = 'SELECT name, '.DB::prefix('moderatedcomments').
        '.* FROM '.DB::prefix('moderatedcomments').' LEFT JOIN '.
        DB::prefix('accounts').
        " ON acctid=moderator WHERE $where ORDER BY moddate DESC LIMIT $limit";
    $result = DB::query($sql);
    $i = 0;
    $clanrankcolors = ['`!', '`#', '`^', '`&', '$'];

    while ($row = DB::fetch_assoc($result))
    {
        $i++;
        rawoutput("<tr class='".($i % 2 ? 'trlight' : 'trdark')."'>");
        rawoutput("<td><input type='checkbox' name='mod[{$row['modid']}]' value='1'></td>");
        rawoutput('<td>');
        output_notl('%s', $row['name']);
        rawoutput('</td>');
        rawoutput('<td>');
        output_notl('%s', $row['moddate']);
        rawoutput('</td>');
        rawoutput('<td>');
        $comment = unserialize($row['comment']);
        output_notl('`0(%s)', $comment['section']);

        if ($comment['clanrank'] > 0)
        {
            output_notl('%s<%s%s>`0', $clanrankcolors[ceil($comment['clanrank'] / 10)],
                    $comment['clanshort'],
                    $clanrankcolors[ceil($comment['clanrank'] / 10)]);
        }
        output_notl('%s', $comment['name']);
        output_notl('-');
        output_notl('%s', comment_sanitize($comment['comment']));
        rawoutput('</td>');
        rawoutput('</tr>');
    }
    rawoutput('</table>');
    rawoutput("<input type='submit' class='ui button' value='$unmod'>");
    rawoutput('</form>');
}

addnav('Sections');
tlschema('commentary');
$vname = getsetting('villagename', LOCATION_FIELDS);
addnav(['%s Square', $vname], 'moderate.php?area=village');

if ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO)
{
    addnav('Grotto', 'moderate.php?area=superuser');
}

addnav('Land of the Shades', 'moderate.php?area=shade');
addnav('Grassy Field', 'moderate.php?area=grassyfield');

$iname = getsetting('innname', LOCATION_INN);
// the inn name is a proper name and shouldn't be translated.
tlschema('notranslate');
addnav($iname, 'moderate.php?area=inn');
tlschema();

addnav('MotD', 'moderate.php?area=motd');
addnav('Veterans Club', 'moderate.php?area=veterans');
addnav("Hunter's Lodge", 'moderate.php?area=hunterlodge');
addnav('Gardens', 'moderate.php?area=gardens');
addnav('Clan Hall Waiting Area', 'moderate.php?area=waiting');

if (1 == getsetting('betaperplayer', 1) && @file_exists('pavilion.php'))
{
    addnav('Beta Pavilion', 'moderate.php?area=beta');
}
tlschema();

if ($session['user']['superuser'] & SU_MODERATE_CLANS)
{
    addnav('Clan Halls');
    $sql = 'SELECT clanid,clanname,clanshort FROM '.DB::prefix('clans').' ORDER BY clanid';
    $result = DB::query($sql);
    // these are proper names and shouldn't be translated.
    tlschema('notranslate');

    while ($row = DB::fetch_assoc($result))
    {
        addnav(['<%s> %s', $row['clanshort'], $row['clanname']],
                "moderate.php?area=clan-{$row['clanid']}");
    }
    tlschema();
}
elseif ($session['user']['superuser'] & SU_EDIT_COMMENTS &&
        getsetting('officermoderate', 0))
{
    // the CLAN_OFFICER requirement was chosen so that moderators couldn't
    // just get accepted as a member to any random clan and then proceed to
    // wreak havoc.
    // although this isn't really a big deal on most servers, the choice was
    // made so that staff won't have to have another issue to take into
    // consideration when choosing moderators.  the issue is moot in most
    // cases, as players that are trusted with moderator powers are also
    // often trusted with at least the rank of officer in their respective
    // clans.
    if ((0 != $session['user']['clanid']) &&
            ($session['user']['clanrank'] >= CLAN_OFFICER))
    {
        addnav('Clan Halls');
        $sql = 'SELECT clanid,clanname,clanshort FROM '.DB::prefix('clans')." WHERE clanid='".$session['user']['clanid']."'";
        $result = DB::query($sql);
        // these are proper names and shouldn't be translated.
        tlschema('notranslate');

        if ($row = DB::fetch_assoc($result))
        {
            addnav(['<%s> %s', $row['clanshort'], $row['clanname']],
                    "moderate.php?area=clan-{$row['clanid']}");
        }
        else
        {
            debug('There was an error while trying to access your clan.');
        }
        tlschema();
    }
}
addnav('Modules');
$mods = [];
$mods = modulehook('moderate', $mods);
reset($mods);

// These are already translated in the module.
tlschema('notranslate');

foreach ($mods as $area => $name)
{
    addnav($name, "moderate.php?area=$area");
}
tlschema();

page_footer();
