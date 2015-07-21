<?php

function keepgold_getmoduleinfo(){
	$info = array(
		"name"=>"Keep Money",
		"version"=>"1.0",
		"author"=>"Christian Rutsch",
		"category"=>"Dragonkill",
		"settings"=>array(
			"howmuch"=>"How much percent of gold should be kept after dk?,int|50",
			"gold"=>"Keep gold on hand?,bool|0",
			"bank"=>"Keep gold in bank?,bool|1",
		),
		"prefs"=>array(
			"gold"=>"Saved gold,hidden|0",
			"bank"=>"Saved goldinbank,hidden|0",
			"You will not see any values here because they are wiped instantly after the dragonkill,note",
		),
	);
	return $info;
}

function keepgold_install(){
	module_addhook("dk-preserve");
	module_addhook("dragonkill");
	return true;
}

function keepgold_uninstall(){
	return true;
}

function keepgold_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
		case "dk-preserve":
			$modifier = get_module_setting("howmuch") /100;
			$modifier = e_rand($modifier / 2 , $modifier);
			if (get_module_setting("gold")) set_module_pref("gold", round( $session['user']['gold'] * $modifier, 0));
			if (get_module_setting("bank")) set_module_pref("bank",  round( $session['user']['goldinbank'] * $modifier, 0));
			break;
		case "dragonkill":
			$session['user']['gold'] = get_module_pref("gold");
			$session['user']['goldinbank'] = get_module_pref("bank");
			set_module_pref("gold", 0);
			set_module_pref("bank", 0);
			break;
	}
	return $args;
}
?>