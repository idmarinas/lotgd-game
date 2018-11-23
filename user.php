<?php

//addnews ready
// mail ready
require_once 'common.php';
require_once 'lib/showform.php';
require_once 'lib/datetime.php';
require_once 'lib/sanitize.php';
require_once 'lib/names.php';

tlschema('user');
check_su_access(SU_EDIT_USERS);

$op = httpget('op');
$userid = httpget('userid');

if ('lasthit' == $op)
{
    // Try and keep user editor and captcha from breaking each other.
    $_POST['i_am_a_hack'] = 'true';
}
page_header('User Editor');

$sort = httpget('sort');
$petition = httpget('returnpetition');
$returnpetition = '';

if ('' != $petition)
{
    $returnpetition = "&returnpetition=$petition";
}

$gentime = 0;
$gentimecount = 0;

$order = 'acctid';

if ('' != $sort)
{
    $order = "$sort";
}
$display = 0;
$query = httppost('q');

if (false === $query)
{
    $query = httpget('q');
}

if (! $query && $sort)
{
    $query = '%';
}

if ('search' == $op || '' == $op)
{
    require_once 'lib/lookup_user.php';
    list($searchresult, $err) = lookup_user($query, $order);
    $op = '';

    if ($err)
    {
        output($err);
    }
    else
    {
        $display = 1;
    }
}

$m = httpget('module');

if ($m)
{
    $m = "&module=$m&subop=module";
}
rawoutput("<form action='user.php?op=search$m' method='POST'>");
output('Search by any field below: ');
rawoutput("<div class='ui action input'><input name='q' id='q'>");
$se = translate_inline('Search');
rawoutput("<button type='submit' class='ui button'>$se</button>");
rawoutput('</div></form>');
rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>");
addnav('', "user.php?op=search$m");
require_once 'lib/superusernav.php';
superusernav();
addnav('Bans');
addnav('Add a ban', 'bans.php?op=setupban');
addnav('List/Remove bans', 'bans.php?op=removeban');
addnav('Search for banned user', 'bans.php?op=searchban');

// Collect a list of the mounts
$mounts = '0,'.translate_inline('None');
$sql = 'SELECT mountid,mountname,mountcategory FROM '.DB::prefix('mounts').' ORDER BY mountcategory';
$result = DB::query($sql);

while ($row = DB::fetch_assoc($result))
{
    $mounts .= ",{$row['mountid']},{$row['mountcategory']}: ".color_sanitize($row['mountname']);
}

$specialties = ['' => translate_inline('Undecided')];
$specialties = modulehook('specialtynames', $specialties);
$enum = '';

foreach ($specialties as $key => $name)
{
    if ($enum)
    {
        $enum .= ',';
    }
    $enum .= "$key,$name";
}

//Inserted for v1.1.0 Dragonprime Edition to extend clan possibilities
$ranks = [CLAN_APPLICANT => '`!Applicant`0', CLAN_MEMBER => '`#Member`0', CLAN_OFFICER => '`^Officer`0', CLAN_LEADER => '`&Leader`0', CLAN_FOUNDER => '`$Founder'];
$ranks = modulehook('clanranks', ['ranks' => $ranks, 'clanid' => null, 'userid' => $userid]);
$ranks = $ranks['ranks'];
$rankstring = '';

foreach ($ranks as $rankid => $rankname)
{
    if ('' != $rankstring)
    {
        $rankstring .= ',';
    }
    $rankstring .= $rankid.','.sanitize($rankname);
}
$races = modulehook('racenames');
//all races here expect such ones no module covers, so we add the users race first.
if ('edit' == $op || 'save' == $op)
{
    //add the race
    $sql = 'SELECT race FROM '.DB::prefix('accounts')." WHERE acctid=$userid LIMIT 1;";
    $result = DB::query($sql);
    $row = DB::fetch_assoc($result);
    $racesenum = ','.translate_inline('Undecided', 'race').',';

    foreach ($races as $race)
    {
        $racesenum .= $race.','.$race.',';
    }

    if (! in_array($row['race'], $races))
    {
        $racesenum .= $row['race'].','.$row['race'].',';
    }
    $racesenum = substr($racesenum, 0, strlen($racesenum) - 1);
//later on: enumpretrans, because races are already translated in a way...
}
else
{
    $racesenum = '';
}
$userinfo = include_once 'lib/data/user_account.php';
$sql = 'SELECT clanid,clanname,clanshort FROM '.DB::prefix('clans').' ORDER BY clanshort';
$result = DB::query($sql);

while ($row = DB::fetch_assoc($result))
{
    $userinfo['clanid'] .= ",{$row['clanid']},".str_replace(',', ';', "<{$row['clanshort']}> {$row['clanname']}");
}

switch ($op)
{
    case 'lasthit':
        require 'lib/user/user_lasthit.php';
        break;
    case 'savemodule':
        require 'lib/user/user_savemodule.php';
        break;
    case 'special':
        require 'lib/user/user_special.php';
        break;
    case 'save':
        require 'lib/user/user_save.php';
        break;
}

switch ($op)
{
    case 'edit':
        require 'lib/user/user_edit.php';
        break;
    case 'setupban':
        require 'lib/user/user_setupban.php';
        break;
    case 'del':
        require 'lib/user/user_del.php';
        break;
    case 'saveban':
        require 'lib/user/user_saveban.php';
        break;
    case 'delban':
        require 'lib/user/user_delban.php';
        break;
    case 'removeban':
        require 'lib/user/user_removeban.php';
        break;
    case 'searchban':
        require 'lib/user/user_searchban.php';
        break;
    case 'debuglog':
        require 'lib/user/user_debuglog.php';
        break;
    case '':
        require 'lib/user/user_.php';
        break;
}
page_footer();

function show_bitfield($val)
{
    $out = '';
    $v = 1;

    for ($i = 0; $i < 32; $i++)
    {
        $out .= (int) $val & (int) $v ? '1' : '0';
        $v *= 2;
    }

    return $out;
}
