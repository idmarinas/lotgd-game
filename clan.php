<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/nltoappon.php");
require_once("lib/commentary.php");
require_once("lib/systemmail.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");
require_once("lib/villagenav.php");

tlschema("clans");


addnav("Village");
villagenav();
addnav("Clan Options");
addnav("C?List Clans","clan.php?op=list");
addcommentary();
$gold = getsetting("goldtostartclan",10000);
$gems = getsetting("gemstostartclan",15);
$ranks = array(CLAN_APPLICANT=>"`!Applicant`0",CLAN_MEMBER=>"`#Member`0",CLAN_OFFICER=>"`^Officer`0",CLAN_ADMINISTRATIVE=>"`\$Administrative`0",CLAN_LEADER=>"`&Leader`0", CLAN_FOUNDER=>"`\$Founder");
$args = modulehook("clanranks", array("ranks"=>$ranks, "clanid"=>$session['user']['clanid']));
$ranks = translate_inline($args['ranks']);

$apply_short = "`@Clan App: `&%s`0";
$apply_subj = array($apply_short, $session['user']['name']);

$op = httpget('op');

$detail = httpget('detail');
if ($detail>0){
	require_once("lib/clan/detail.php");
}elseif ($op=="list"){
	require_once("lib/clan/list.php");
} elseif ($op == "waiting") {
	require_once("lib/clan/waiting.php");
}elseif ($session['user']['clanrank']==CLAN_APPLICANT){
	require_once("lib/clan/applicant.php");
}else{
	require_once("lib/clan/clan_start.php");
}


page_footer();

function clanform()
{
    output_notl($lotgd_tpl->renderThemeTemplate('pages/clan/new.twig', $data), true);
}
