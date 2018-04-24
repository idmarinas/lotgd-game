<?php
// addnews ready
// translator ready
// mail ready
require_once 'common.php';
require_once 'lib/sanitize.php';

tlschema('bio');

checkday();

$ret = httpget('ret');
if ($ret == '') { $return = '/list.php'; }
else { $return = cmd_sanitize($ret); }

$char = httpget('char');
$select = DB::select('accounts');
$select->columns(['login', 'name', 'level', 'sex', 'title', 'specialty', 'hashorse', 'acctid', 'resurrections', 'bio', 'dragonkills', 'race', 'race', 'clanrank', 'clanid', 'laston', 'loggedin'])
    ->join('clans', 'accounts.clanid = clans.clanid', ['clanname', 'clanshort'], 'LEFT')
;

//Legacy support
if (is_numeric($char)) { $select->where->equalTo('acctid', $char); }
else { $select->where->equalTo('login', $char); }

$result = DB::execute($select);

if ($result->count() == 1)
{
    $target = $result->current();
    $target['login'] = rawurlencode($target['login']);
    $id = $target['acctid'];
    $target['return_link'] = $return;

    $twig = ['target' => $target];

    page_header('Character Biography: %s', full_sanitize($target['name']));

    tlschema('nav');
    addnav('Return');
    tlschema();

    if ($session['user']['superuser'] & SU_EDIT_USERS)
    {
        addnav("Update", "bio.php?char=$char");
        addnav('Superuser');
        addnav('Edit User', "user.php?op=edit&userid=$id");
    }

    modulehook('biotop', $target);

    if ($target['clanname'] > '' && getsetting('allowclans', false))
    {
        $ranks = [
            CLAN_APPLICANT => '`!Applicant`0',
            CLAN_MEMBER => '`#Member`0',
            CLAN_OFFICER => '`^Officer`0',
            CLAN_LEADER => '`&Leader`0',
            CLAN_FOUNDER => '`$Founder`0'
        ];
        $ranks = modulehook('clanranks', ['ranks' => $ranks, 'clanid' => $target['clanid']]);
        array_push($ranks['ranks'], '`$Founder');
        $twig['ranks'] = $ranks['ranks'];
    }

    $twig['target']['loggedin'] = false;
    if ($target['loggedin'] && (date("U") - strtotime($target['laston']) < getsetting("LOGINTIMEOUT", 900)))
    {
        $twig['target']['loggedin'] = true;
    }

    if (! $target['race']) $twig['target']['race'] = RACE_UNKNOWN;

    $sql = "SELECT * FROM " . DB::prefix("mounts") . " WHERE mountid='{$target['hashorse']}'";
    $result = DB::query_cached($sql, "mountdata-{$target['hashorse']}", 3600);
    $mount = DB::fetch_assoc($result);

    $mount['acctid'] = $target['acctid'];
    $mount = modulehook('bio-mount', $mount);
    $twig['target']['mount'] = $mount;

    $extrastats = modulehook('biostat', ['target' => $target, 'biostats' => []]);
    $twig['biostats'] = $extrastats['biostats'];

    $twig['target']['bio'] = soap($target['bio']);

    $extrainfo = modulehook('bioinfo', ['target' => $target, 'bioinfo' => []]);
    $twig['bioinfo'] = $extrainfo['bioinfo'];

    $result = DB::query("SELECT * FROM " . DB::prefix("news") . " WHERE accountid={$target['acctid']} ORDER BY newsdate DESC,newsid ASC LIMIT 100");

    $odate = '';
    $twig['bionews'] = [];
    while ($row = DB::fetch_assoc($result))
    {
        if ($row['arguments'] > '')
        {
            $arguments = [];
            $base_arguments = unserialize($row['arguments']);
            array_push($arguments, $row['newstext']);
            while(list($key, $val) = each($base_arguments))
            {
                array_push($arguments, $val);
            }
            $row['newstext'] = $arguments;
        }
        $twig['bionews'][date('D, M d', strtotime($row['newsdate']))][] = $row;
    }

    rawoutput($lotgdTpl->renderThemeTemplate('pages/bio.twig', $twig));

    if ($ret == '')
    {
        $return = substr($return, strrpos($return, '/') + 1);
        tlschema('nav');
        addnav('Return');
        addnav('Return to the warrior list', $return);
        tlschema();
    }
    else
    {
        $return = substr($return, strrpos($return, '/')+1);
        tlschema('nav');
        addnav('Return');
        if ($return == 'list.php')
        {
            addnav('Return to the warrior list', $return);
        }
        else
        {
            addnav('Return whence you came', $return);
            addnav('Return to village','village.php');
        }
        tlschema();
    }

    modulehook('bioend', $target);

    page_footer();
}
else
{
    page_header('Character has been deleted');

    rawoutput($lotgdTpl->renderThemeTemplate('pages/bio/deleted.twig', []));

    if ($ret == '')
    {
        $return = substr($return, strrpos($return, '/') + 1);
        tlschema('nav');
        addnav('Return');
        addnav('Return to the warrior list', $return);
        tlschema();
    }
    else
    {
        $return = substr($return, strrpos($return, '/') + 1);
        tlschema('nav');
        addnav('Return');
        if ($return == 'list.php') { addnav('Return to the warrior list', $return); }
        else { addnav('Return whence you came', $return); }
        addnav('Return to village', 'village.php');
        tlschema();
    }
	page_footer();
}
