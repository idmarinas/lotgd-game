<?php

global $charstat_info, $badguy, $actions_used;

//Look at the number of Turns we're missing.  Default is ten, and we'll add or remove some Stamina depending, as long as we're not in a fight.
if (get_module_setting("turns_emulation_base")!=0 ){
	if (!isset($badguy)){
		$stamina = e_rand(get_module_setting("turns_emulation_base"),get_module_setting("turns_emulation_ceiling"));
		while ($session['user']['turns'] < 10){
			$session['user']['turns']++;
			debug("Turns Removed");
			removestamina($stamina);
		}
		while ($session['user']['turns'] > 10){
			$session['user']['turns']--;
			debug("Turns Added");
			addstamina($stamina);
		}
	}
}

//add recent actions to the _top_ of the charstat column
if (!isset($charstat_info['Recent Actions'])){
	//Put yer thing down, flip it an' reverse it
	$yarr = array_reverse($charstat_info);
	$yarr['Action Rankings'] = array();
	$yarr['Recent Actions']=array();
	$charstat_info = array_reverse($yarr);
}

if (isset($actions_used)){
	foreach($actions_used AS $action=>$vals){
		if (!$actions_used[$action]['lvlinfo']['currentlvlexp']){
			$actions_used[$action]['lvlinfo']['currentlvlexp']=1;
		}
		$pct = (($actions_used[$action]['lvlinfo']['exp']-$actions_used[$action]['lvlinfo']['currentlvlexp']) / ($actions_used[$action]['lvlinfo']['nextlvlexp']-$actions_used[$action]['lvlinfo']['currentlvlexp'])) * 100;
		$disp = "<div class='progressbar'>
			<div class='progress-progressbar progress-progressbar-progress' style='width: $pct%;'></div>
			<div class='progress-text'>".translate_inline("Lv").$actions_used[$action]['lvlinfo']['lvl']." (+`8".$actions_used[$action]['exp_earned']."`0 xp)</div>
		</div>";
		setcharstat("Recent Actions",$action,$disp);
		
		if (get_module_pref("user_minihof")){
			$st = microtime(true);
			stamina_minihof($action);
			$en = microtime(true);
			$to = $en - $st;
			debug("Minihof: ".$to);
		}
	}
}

//debug($actions_used);


//Then, since Turns are pretty well baked into core and we don't want to be playing around with adding turns just as they're needed for core to operate, we'll just add ten turns here and forget all about it...
$session['user']['turns'] = 10;

//Display the actual Stamina bar

$stamina = get_module_pref("stamina");
$daystamina = 2000000;
$redpoint = get_module_pref("red");
$amberpoint = get_module_pref("amber");
$redpct = get_stamina(0);
$amberpct = get_stamina(1);
$greenpct = get_stamina(2);

$pctoftotal = round($stamina / $daystamina * 100, 5);

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

$alert = "";
if (!$session['user']['dragonkills'] && $session['user']['age'] <= 1 && $greenpct <= 1){
	$alert = "- " . translate_inline("When you run low on Stamina, you become weaker in combat.  Recover Stamina by eating, drinking or using a New Day.");
}

$new = "<a href='runmodule.php?module=staminasystem&op=show' target='_blank' onclick=\"".popup("runmodule.php?module=staminasystem&op=show").";return false;\">
		<div data-uk-tooltip='{pos:'left'}' title='$pctoftotal% $alert' class='staminabar'>
			<div class='progress-staminabar progress-staminabar-red' style='width: $redwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-red' style='width: $redwdarkidth%;'></div>
			<div class='progress-staminabar progress-staminabar-amber' style='width: $amberwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-amber' style='width: $amberdarkwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-green' style='width: $greenwidth%;'></div>
			<div class='progress-staminabar progress-staminabar-dark-green' style='width: $greendarkwidth%;'></div>
		</div></a>";

setcharstat("Character Info", "Stamina", $new);

addnav("","runmodule.php?module=staminasystem&op=show");

?>