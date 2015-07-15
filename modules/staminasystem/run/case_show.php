<?php

popup_header("Your Stamina statistics");

$stamina = get_module_pref("stamina");
$daystamina = get_module_pref("daystamina");
$redpoint = get_module_pref("red");
$amberpoint = get_module_pref("amber");
$redpct = get_stamina(0);
$amberpct = get_stamina(1);
$greenpct = get_stamina(2);

$greentotal = round((($daystamina - $redpoint - $amberpoint)/$daystamina)*100);
$ambertotal = round(((($daystamina - $redpoint)/$daystamina)*100) - $greentotal);
$redtotal = (100 - $greentotal) - $ambertotal;

$greenwidth = (($greentotal / 100) * $greenpct);
$amberwidth = (($ambertotal / 100) * $amberpct);
$redwidth = (($redtotal / 100) * $redpct);

$colorgreen = "#00FF00";
$coloramber = "#FFA200";
$colorred = "#FF0000";
$colordarkgreen = "#003300";
$colordarkamber = "#2F1E00";
$colordarkred = "#330000";
$colorbackground = $colordarkgreen;

if ($greenpct == 0){
	$colorgreen = $colordarkamber;
	$colorbackground = $colordarkamber;
}
if ($amberpct == 0){
	$colorgreen = $colordarkred;
	$coloramber = $colordarkred;
	$colorbackground = $colordarkred;
}

$pctgrey = (((100 - $greenwidth)-$amberwidth)-$redwidth);

rawoutput("<table style='border: solid 1px #000000' bgcolor='$colorbackground' cellpadding='0' cellspacing='0' width='100%' height='20'><tr><td width='$redwidth%' bgcolor='$colorred'></td><td width='$amberwidth%' bgcolor='$coloramber'></td><td width='$greenwidth%' bgcolor='$colorgreen'></td><td width='$pctgrey%'></td></tr></table>");

output("Total Stamina: %s / %s | Amber point: %s | Red point: %s",number_format($stamina), number_format($daystamina), number_format($amberpoint), number_format($redpoint));

output("`n`nHere is the nitty-gritty of your Stamina statistics.  The most important value is the total cost, over there on the right.  If there's anything in the Buff column, something's temporarily affecting the cost of performing that action (negative numbers are good!).  More details follow after the stats.`n`n");

rawoutput("<table width=100%><tr><td><b>Action</b></td><td><b>Experience</b></td><td><b>Natural Cost</b></td><td><b>Buff</b></td><td><b>Total</b></td></tr>");

$act = get_player_action_list();

foreach($act AS $key => $values){
	$cost = $values['naturalcost'];
	$level = $values['level'];
	$exp = $values['exp'];
	$mincost = $values['mincost'];
	$costwithbuff = stamina_calculate_buffed_cost($key);
	$modifier = $costwithbuff - $cost;
	$bonus = "None";
	if ($modifier < 0) {
		$bonus = "`@".number_format($modifier)."`0";
	} elseif ($modifier > 0) {
		$bonus = "`\$".number_format($modifier)."`0";
	};
	rawoutput("<tr><td>");
	output("`^$key`0 Lv $level");
	rawoutput("</td><td>");
	$exp = number_format($exp);
	output_notl("$exp");
	rawoutput("</td><td>");
	$cost = number_format($cost);
	output_notl("$cost");
	rawoutput("</td><td>");
	output_notl("$bonus");
	rawoutput("</td><td>");
	$costwithbuff = number_format($costwithbuff);
	output_notl("`Q`b$costwithbuff`b`0");
	rawoutput("</td></tr>");
}
rawoutput("</table>");

output("`n`nRemember, using the Stamina system is easy - just keep in mind that the more you do something, the better you get at it.  So if you do a lot of the things you enjoy doing the most, the game will let you do more of those things each day.  All of the statistics you see above can help you fine-tune your character, but honestly, 99%% of the time you needn't worry about the statistics and mechanics - they're only there for when you're curious!`n`nAll Bonuses and Penalties are cleared at the start of each game day.`n`n");

popup_footer();

?>