<?php

function implantsleepsuppression_getmoduleinfo(){
	$info = array(
		"name" => "Implant - Sleep Suppression System",
		"author" => "Dan Hall, based on generic speciality files by Eric Stevens et al",
		"version" => "2009-07-05",
		"download" => "",
		"category" => "Implants",
	);
	return $info;
}

function implantsleepsuppression_install(){
	module_addhook("choose-specialty");
	module_addhook("set-specialty");
	module_addhook("specialtynames");
	module_addhook("specialtymodules");
	module_addhook("specialtycolor");
	module_addhook("stamina-newday");
	return true;
}

function implantsleepsuppression_uninstall(){
	// Reset the specialty of anyone who had this specialty so they get to
	// rechoose at new day
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='SS'";
	db_query($sql);
	return true;
}

function implantsleepsuppression_dohook($hookname,$args){
	global $session,$resline;
	
	$spec = "SS";
	$name = "Sleep Suppression System";
	$ccode = "`2";

	switch ($hookname) {
	case "choose-specialty":
		if ($session['user']['specialty'] == "" || $session['user']['specialty'] == '0') {
			if ($session['user']['dragonkills'] >= 1){
				addnav("Sleep Suppression System","newday.php?setspecialty=SS$resline");
				output("`5\"This is a `2Sleep Suppression System`5 implant.  This one doesn't have any interactive effects, like other Implants that may be designed for combat or travel or what-have-you - it simply suppresses the release of certain chemicals that cause tiredness.  In a nutshell, you'll start to feel tired much later in the day than you would normally - you'll crash a lot faster, but you'll get more time being fully awake.  It's a tradeoff, really.\"`n`n");
			}
		}
		break;
	case "set-specialty":
		if($session['user']['specialty'] == "SS") {
			page_header("Sleep Suppression System");
			output("`2Inside your head is an Implant designed to keep you alert for longer.`n`nThe point at which you start eating into Amber stamina has been reduced.");
		}
		break;
	case "specialtycolor":
		$args[$spec] = $ccode;
		break;
	case "specialtynames":
		$args[$spec] = translate_inline($name);
		break;
	case "specialtymodules":
		$args[$spec] = "implantsleepsuppression";
		break;
	case "stamina-newday":
		if($session['user']['specialty'] == "SS") {
			increment_module_pref("amber",-100000,"staminasystem");
		}
		break;
	}
	return $args;
}

function implantsleepsuppression_run(){
}
?>
