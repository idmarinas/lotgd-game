<?php
// addnews ready
// translator ready
// mail ready
define("ALLOW_ANONYMOUS",true);
require_once 'common.php';
require_once 'lib/http.php';
require_once 'lib/villagenav.php';

tlschema("list");

page_header("List Warriors");
if ($session['user']['loggedin']) {
	checkday();
	if ($session['user']['alive']) {
		villagenav();
	} else {
		addnav("Return to the Graveyard", "graveyard.php");
	}
	addnav("Currently Online","list.php");
	if ($session['user']['clanid']>0){
		addnav("Online Clan Members","list.php?op=clan");
		if ($session['user']['alive']) {
			addnav("Clan Hall","clan.php");
		}
	}
}else{
	addnav('Login page');
	addnav("Login Screen","index.php");
	addnav("Currently Online","list.php");
}

$playersperpage=50;

$sql = "SELECT count(acctid) AS c FROM " . DB::prefix("accounts") . " WHERE locked=0";
$result = DB::query($sql);
$row = DB::fetch_assoc($result);
$totalplayers = $row['c'];

$op = httpget('op');
$page = httpget('page');
$search = "";
$limit = "";

if ($op=="search"){
	$search="%";
	$n = DB::quoteValue(httppost('name'));
	for ($x=0;$x<strlen($n);$x++){
		$search .= substr($n,$x,1)."%";
	}
	$search=" AND name LIKE '".addslashes($search)."' ";
}else{
	$pageoffset = (int)$page;
	if ($pageoffset>0) $pageoffset--;
	$pageoffset*=$playersperpage;
	$from = $pageoffset+1;
	$to = min($pageoffset+$playersperpage,$totalplayers);

	$limit=" LIMIT $pageoffset,$playersperpage ";
}
addnav("Pages");
for ($i=0;$i<$totalplayers;$i+=$playersperpage){
	$pnum = $i/$playersperpage+1;
	if ($page == $pnum) {
		addnav(array(" ?`b`#Page %s`0 (%s-%s)`b", $pnum, $i+1, min($i+$playersperpage,$totalplayers)), "list.php?page=$pnum");
	} else {
		addnav(array(" ?Page %s (%s-%s)", $pnum, $i+1, min($i+$playersperpage,$totalplayers)), "list.php?page=$pnum");
	}
}

// Order the list by level, dragonkills, name so that the ordering is total!
// Without this, some users would show up on multiple pages and some users
// wouldn't show up
if ($page=="" && $op=="")
{
	$title = translate_inline("Warriors Currently Online");
	$sql = "SELECT acctid,name,login,alive,hitpoints,location,race,sex,level,laston,loggedin,lastip,uniqueid FROM " . DB::prefix("accounts") . " WHERE locked=0 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC, dragonkills DESC, login ASC";
	$result = DB::query_cached($sql,"list.php-warsonline");
}elseif($op=='clan'){
	$title = translate_inline("Clan Members Online");
	$sql = "SELECT acctid,name,login,alive,hitpoints,location,race,sex,level,laston,loggedin,lastip,uniqueid FROM " . DB::prefix("accounts") . " WHERE locked=0 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' AND clanid='{$session['user']['clanid']}' ORDER BY level DESC, dragonkills DESC, login ASC";
	$result = DB::query($sql);
}else{
	if ($totalplayers > $playersperpage && $op != "search") {
		$title = sprintf_translate("Warriors of the realm (Page %s: %s-%s of %s)", ($pageoffset/$playersperpage+1), $from, $to, $totalplayers);
	} else {
		$title = sprintf_translate("Warriors of the realm");
	}
	rawoutput(tlbutton_clear());
	$sql = "SELECT acctid,name,login,alive,hitpoints,location,race,sex,level,laston,loggedin,lastip,uniqueid FROM " . DB::prefix("accounts") . " WHERE locked=0 $search ORDER BY level DESC, dragonkills DESC, login ASC $limit";
	$result = DB::query($sql);
}
if ($session['user']['loggedin']){
	$search = translate_inline("Search by name: ");
	$search2 = translate_inline("Search");

	rawoutput("<form action='list.php?op=search' method='POST'><div class='ui action input'>$search<input name='name'><button type='submit' class='ui button'> $search2</button></div></form>");
	addnav("","list.php?op=search");
}

$max = DB::num_rows($result);
if ($max>getsetting("maxlistsize", 100)) {
	output("`\$Too many names match that search.  Showing only the first %s.`0`n", getsetting("maxlistsize", 100));
	$max = getsetting("maxlistsize", 100);
}

if ($page=="" && $op==""){
	$title .= sprintf_translate(" (%s warriors)", $max);
}
output_notl("`c`b".$title."`b");

$alive = translate_inline("Alive");
$level = translate_inline("Level");
$name = translate_inline("Name");
$loc = translate_inline("Location");
$race = translate_inline("Race");
$sex = translate_inline("Sex");
$last = translate_inline("Last On");

rawoutput("<table class='ui very compact striped selectable table'>",true);
rawoutput("<thead><tr><th>$alive</th><th>$level</th><th>$name</th><th>$loc</th><th>$race</th><th>$sex</th><th>$last</tr></thead>");
$writemail = translate_inline("Write Mail");
$alive = translate_inline("`1Yes`0");
$dead = translate_inline("`4No`0");
$unconscious = translate_inline("`6Unconscious`0");
for($i=0; $i<$max; $i++)
{
	$row = DB::fetch_assoc($result);
	rawoutput("<tr><td>",true);
	if ($row['alive'] == true) {
		$a = $alive;
	} else if ($row['hitpoints'] > 0) {
		$a = $unconscious;
	} else {
		$a = $dead;
	}

	output_notl("%s", $a);
	rawoutput("</td><td>");
	output_notl("`^%s`0", $row['level']);
	rawoutput("</td><td>");
	if ($session['user']['loggedin']) {
		rawoutput("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\">");
		// rawoutput("<img src='images/newscroll.GIF' width='16' height='16' alt='$writemail' border='0'></a>");
        rawoutput("<i class='fa fa-fw fa-envelope-o' data-uk-tooltip title='$writemail'><img src='images/newscroll.GIF' width='16' height='16' alt='$writemail' border='0'></i></a>");
		rawoutput("<a href='bio.php?char=".$row['acctid']."'>");
		addnav("","bio.php?char=".$row['acctid']."");
	}
	output_notl("`&%s`0", $row['name']);
	if ($session['user']['loggedin'])
		rawoutput("</a>");
	rawoutput("</td><td>");
	$loggedin=(date("U") - strtotime($row['laston']) < getsetting("LOGINTIMEOUT",900) && $row['loggedin']);
	output_notl("`&%s`0", $row['location']);
	if ($loggedin) {
		$online = translate_inline("`#(Online)");
		output_notl("%s", $online);
	}
	rawoutput("</td><td>");
	if (!$row['race']) $row['race'] = RACE_UNKNOWN;
	tlschema("race");
	output($row['race']);
	tlschema();
	rawoutput("</td><td>");
	$sex = translate_inline($row['sex']?"`%Female`0":"`!Male`0");
	output_notl("%s", $sex);
	rawoutput("</td><td>");
	$laston = relativedate($row['laston']);
	output_notl("%s", $laston);
	rawoutput("</td></tr>");
}
rawoutput("</table>");
output_notl("`c");
page_footer();
?>
