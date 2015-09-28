<?php
page_header("Most Wanted");
$accounts 			= db_prefix("accounts");
$module_userprefs 	= db_prefix("module_userprefs");
$sql 				= "SELECT $accounts.name AS name,$accounts.acctid AS acctid, $module_userprefs.userid FROM $module_userprefs 
	INNER JOIN $accounts ON $accounts.acctid = $module_userprefs.userid WHERE $module_userprefs.setting = 'wantedlevel' 
	AND $module_userprefs.value > 0 ORDER BY $module_userprefs.value DESC";
$result 			= db_query($sql) or die(db_error(LINK));
$playern			= translate_inline("Player Name");
$wantedl			= translate_inline("Wanted Level");
output("<center><table><tr><td>$playern</td><td align=right>$wantedl</td></tr>", true);
for($i = 1 ; $i < 11 ; $i++)
{
	$row = db_fetch_assoc($result);
	$wantedlevel =get_module_pref('wantedlevel', $row['acctid']);
	if ($wantedlevel > 0) 	$status = translate_inline("`@Purse Snatcher");
	if ($wantedlevel >2) 	$status = translate_inline("`#Bank Robber");
	if ($wantedlevel > 5) 	$status = translate_inline("`QCon Artist");
	if ($wantedlevel > 10) 	$status = translate_inline("`*Serial Killer");
	if ($wantedlevel > 20) 	$status = translate_inline("`&Politician");
	if ($wantedlevel > 35) 	$status = translate_inline("`%Terrorist");
	if ($wantedlevel > 0) 	output("<tr><td>" . $row['name'] . " </td><td align=right>$status</td></tr>", true);
}
output("</center></table>", true);
addnav("Back to HoF", "hof.php");
villagenav();
?>