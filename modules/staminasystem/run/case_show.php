<?php

popup_header("Your Stamina statistics");

$stamina = get_module_pref("stamina");
$daystamina = get_module_pref("daystamina");
$redpoint = get_module_pref("red");
$amberpoint = get_module_pref("amber");
$redpct = get_stamina(0);
$amberpct = get_stamina(1);
$greenpct = get_stamina(2);

$pctoftotal = round($stamina / $daystamina * 100, 2);

$greentotal = round((($daystamina - $redpoint - $amberpoint)/$daystamina)*100);
$ambertotal = round(((($daystamina - $redpoint)/$daystamina)*100) - $greentotal);
$redtotal = (100 - $greentotal) - $ambertotal;

$greenwidth = (($greentotal / 100) * $greenpct);
$amberwidth = (($ambertotal / 100) * $amberpct);
$redwidth = (($redtotal / 100) * $redpct);

$greendarkwidth = $greentotal - $greenwidth;
$amberdarkwidth = $ambertotal - $amberwidth;
$redwdarkidth = $redtotal - $redwidth;

$totalwidth = $greenwidth + $amberwidth + $redwidth;

if ($totalwidth > 100) $greenwidth -= ($totalwidth -100);

rawoutput("<div class='staminabar show'>
			<div class='progress-staminabar progress-staminabar-red' style='width: $redwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-red' style='width: $redwdarkidth%;'></div>
			<div class='progress-staminabar progress-staminabar-amber' style='width: $amberwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-amber' style='width: $amberdarkwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-green' style='width: $greenwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-green' style='width: $greendarkwidth%;'></div>
		</div>");

output_notl("`n");
output("Total Stamina: %s / %s | Amber point: %s | Red point: %s",number_format($stamina), number_format($daystamina), number_format($amberpoint), number_format($redpoint));

output("`n`nHere is the nitty-gritty of your Stamina statistics.  The most important value is the total cost, over there on the right.  If there's anything in the Buff column, something's temporarily affecting the cost of performing that action (negative numbers are good!).  More details follow after the stats.`n`n");

$action = translate_inline("Action");
$experience = translate_inline("Experience");
$cost = translate_inline("Natural Cost");
$buff = translate_inline("Buff");
$total = translate_inline("Total");
rawoutput("<table width=100%><tr><td><b>$action</b></td><td><b>$experience</b></td><td><b>$cost</b></td><td><b>$buff</b></td><td><b>$total</b></td></tr>");

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
	output("`^$key`0 Lv %s", $level);
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