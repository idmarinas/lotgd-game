<?php

/*

Expanded Stamina System by Dan Hall, AKA Caveman Joe.
With thanks to Nicolas Harter, for showing exactly how to lay this out, via his marvellous Abilities system.  To be honest, the Abilities system was such a well-presented and clean and tidy toolbox that I pretty well stole a lot of it.

*/

require_once "modules/staminasystem/lib/lib.php";

function staminatest_getmoduleinfo(){
	$info=array(
		"name"=>"Stamina Test Module",
		"version"=>"2008-12-29",
		"author"=>"Dan Hall, based on Abilities system by Nicolas Harter",
		"override_forced_nav"=>true,
		"category"=>"Stamina",
		"download"=>"",
	);
	return $info;
}

function staminatest_install(){
	module_addhook("superuser");
	return true;
}

function staminatest_uninstall(){
	return true;
}

function staminatest_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "superuser":
			addnav("Stamina Testing","runmodule.php?module=staminatest&op=start");
			break;
	}
	return $args;
}

function staminatest_run(){
	global $session;
	page_header("Stamina Testing");
	switch (httpget("op")){
		case "start":
			output("Testing Testing!");
			break;
		case "add":
			output("Attempting to install an action called Sexins, with these parameters:`n`nStarting and Maximum costs: 500`nMinimum cost: 200`nReps for a Reduction: 20`nReduction: 10`n");
			install_action("Sexins",array(
				"maxcost"=>25000,
				"mincost"=>10000,
				"expperrep"=>100,
				"expforlvl"=>1000,
				"costreduction"=>10,
				"dkpct"=>2.5
			));
			break;
		case "process":
			output("Processing the Sexins action");
			process_action("Sexins");
			break;
		case "newday":
			output("Processing a New Day");
			stamina_process_newday();
			break;
		case "buff":
			output("Applying a stamina buff to Sexins for the current user, for 20 rounds, reducing action cost to half.");
			apply_stamina_buff('ultra-sexy-buff-for-sexins', array(
				"name"=>"Ultra Sexy Buff for Sexins",
				"action"=>"Sexins",
				"costmod"=>0.5,
				"expmod"=>0.8,
				"rounds"=>5,
				"roundmsg"=>"Round Message!",
				"wearoffmsg"=>"Wearoff Message!",
			));
			output("Also applying a Stamina Class buff to all Hunting actions, reducing their cost to half for twenty rounds.");
			apply_stamina_buff('huntclasstest', array(
				"name"=>"Hunting Class test buff",
				"class"=>"Hunting",
				"costmod"=>0.5,
				"expmod"=>0.8,
				"rounds"=>20,
				"roundmsg"=>"Round Message!",
				"wearoffmsg"=>"Wearoff Message!",
			));
			break;
		case "get":
			$thingtodebug = get_player_action("Sexins");
			debug ($thingtodebug);
			break;
		case "uninstall":
			output("Uninstalling the Sexins action, deleting all actions entries and associated buffs");
			uninstall_action("Sexins");
			break;
		case "dragonkill":
			output("Processing a Dragon Kill");
			stamina_process_dragonkill();
			break;
		case "calcbuffs":
			output("Calculating Buffs");
			stamina_calculate_buffed_cost("Sexins");
			break;
		case "calcexp":
			output("Calculating Buffed EXP");
			stamina_calculate_buffed_exp("Sexins");
			break;
	}
	addnav("Install an action called Sexins","runmodule.php?module=staminatest&op=add");
	addnav("Uninstall","runmodule.php?module=staminatest&op=uninstall");
	addnav("Process the Sexins Action for the current user","runmodule.php?module=staminatest&op=process");
	addnav("Process newday","runmodule.php?module=staminatest&op=newday");
	addnav("Add a buff","runmodule.php?module=staminatest&op=buff");
	addnav("Get Stamina","runmodule.php?module=staminatest&op=get");
	addnav("Process a Dragon Kill","runmodule.php?module=staminatest&op=dragonkill");
	addnav("Calculate Buffed Cost","runmodule.php?module=staminatest&op=calcbuffs");
	addnav("Calculate Buffed exp","runmodule.php?module=staminatest&op=calcexp");
	addnav("Back to the Grotto","superuser.php");
	page_footer();
	return true;
}
?>
