<?php

global $charstat_info, $badguy;
//First we remove the Turns stat...
if (isset($charstat_info['Vital Info']) && isset($charstat_info['Vital Info']['Turns'])){
	unset($charstat_info['Vital Info']['Turns']);
}

modulehook("stamina-emulation");

//Look at the number of Turns we're missing.  Default is ten, and we'll add or remove some Stamina depending, as long as we're not in a fight.
if (get_module_setting("turns_emulation_base")!=0 ){
	if (!isset($badguy)){
		$stamina = e_rand(get_module_setting("turns_emulation_base"),get_module_setting("turns_emulation_ceiling"));
		while ($session['user']['turns'] < 10){
			$session['user']['turns']++;
			removestamina($stamina);
		}
		while ($session['user']['turns'] > 10){
			$session['user']['turns']--;
			addstamina($stamina);
		}
	}
}

//Then, since Turns are pretty well baked into core and we don't want to be playing around with adding turns just as they're needed for core to operate, we'll just add ten turns here and forget all about it...
$session['user']['turns'] = 10;



//Display the actual Stamina bar

$stamina = get_module_pref("stamina");
$daystamina = get_module_pref("daystamina");
$redpoint = get_module_pref("red");
$amberpoint = get_module_pref("amber");
$redpct = get_stamina(0);
$amberpct = get_stamina(1);
$greenpct = get_stamina(2);

$stat = "Stamina";

$pctoftotal = round($stamina / $daystamina * 100, 2);

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

$new = "";
$new .= "<font color=$colorbackground>$pctoftotal%</font><br><table style='border: solid 1px #000000' bgcolor='$colorbackground' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$redwidth%' bgcolor='$colorred'></td><td width='$amberwidth%' bgcolor='$coloramber'></td><td width='$greenwidth%' bgcolor='$colorgreen'></td><td width='$pctgrey%'></td></tr></table><a href='runmodule.php?module=staminasystem&op=show' target='_blank' onclick=\"".popup("runmodule.php?module=staminasystem&op=show").";return false;\">".translate_inline("Show Details")."</a>";

// $new .= "<table style='border: solid 1px #000000' bgcolor='#777777' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pctoftotal%' bgcolor='$color'></td><td width='$pctused%'></td></tr></table>";
setcharstat("Vital Info", $stat, $new);



//Add the "Show Actions" bit
// addcharstat("Vital Info");
// addcharstat("Stamina Details", "<a href='runmodule.php?module=staminasystem&op=show' target='_blank' onclick=\"".popup("runmodule.php?module=staminasystem&op=show").";return false;\">".translate_inline("Show")."</a>");
addnav("","runmodule.php?module=staminasystem&op=show");

?>