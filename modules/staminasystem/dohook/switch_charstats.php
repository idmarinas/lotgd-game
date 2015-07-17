<?php

global $charstat_info, $badguy;
//First we remove the Turns stat...
if (isset($charstat_info['Personal Info']) && isset($charstat_info['Personal Info']['Turns'])){
	unset($charstat_info['Personal Info']['Turns']);
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

$new = "<a class='progress-staminabar-icon-info' title='$pctoftotal%' href='runmodule.php?module=staminasystem&op=show' target='_blank' onclick=\"".popup("runmodule.php?module=staminasystem&op=show").";return false;\"><i class='fa fa-info-circle fa-fw'></i></a>
		<div class='staminabar $animated'>
			<div class='progress-staminabar progress-staminabar-red' style='width: $redwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-red' style='width: $redwdarkidth%;'></div>
			<div class='progress-staminabar progress-staminabar-amber' style='width: $amberwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-amber' style='width: $amberdarkwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-green' style='width: $greenwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-green' style='width: $greendarkwidth%;'></div>
		</div>";

setcharstat("Character Info", "Stamina", $new);



//Add the "Show Actions" bit
 //addcharstat("Character Info");
 //addcharstat("Stamina Details", "<a href='runmodule.php?module=staminasystem&op=show' target='_blank' onclick=\"".popup("runmodule.php?module=staminasystem&op=show").";return false;\">".translate_inline("Show")."</a>");
addnav("","runmodule.php?module=staminasystem&op=show");

?>