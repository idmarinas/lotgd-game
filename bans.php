<?php
//addnews ready
// mail ready
require_once("common.php");
require_once("lib/showform.php");
require_once("lib/datetime.php");
require_once("lib/sanitize.php");
require_once("lib/names.php");

tlschema("bans");
check_su_access(SU_EDIT_BANS);

$op = httpget('op');
$userid=httpget("userid");

page_header("Ban Editor");

$sort = httpget('sort');

$gentime = 0;
$gentimecount = 0;

$order = "acctid";
if ($sort!="") $order = "$sort";
$display = 0;
$query = httppost('q');
if ($query === false) $query = httpget('q');
if (!$query && $sort) $query = "%";

if ($op=="search" || $op== ""){
	require_once("lib/lookup_user.php");
	list($searchresult, $err) = lookup_user($query, $order);
	$op = "";
	if ($err) {
		output($err);
	} else {
		if ($searchresult) $display = 1;
	}
}

output("`\$`cWelcome to the Ban Editor`c`0`n`n");

rawoutput("<form action='bans.php?op=search$m' method='POST'>");
output("Search users by any field: ");
rawoutput("<input name='q' id='q'>");
$se = translate_inline("Search");
rawoutput("<input type='submit' class='button' value='$se'>");
rawoutput("</form>");
rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>");
addnav("","bans.php?op=search$m");



require_once("lib/superusernav.php");
superusernav();
addnav("Bans");
addnav("Add a ban","bans.php?op=setupban");
addnav("List/Remove bans","bans.php?op=removeban");
addnav("Search for banned user","bans.php?op=searchban");


switch($op) {
	case "setupban":
		require("lib/bans/case_setupban.php");
		break;
	case "saveban":
		require("lib/bans/case_saveban.php");
		break;
	case "delban":
		require("lib/bans/case_delban.php");
		break;
	case "removeban":
		require("lib/bans/case_removeban.php");
		break;
	case "searchban":
		require("lib/bans/case_searchban.php");
		break;
	default:
		output("From here, you can issue bans for players from being able to play.`n`nBased on the ID = cookie on the machine AND/OR on the IP they accessed the char last the ban takes effect.`n`nNote: Locked chars stay locked, even after they delete their cookie / change their IP.`n`nHowever, they can make new chars and login in that case. You cannot control this.");
		require("lib/bans/case_.php");
}
page_footer();
?>
