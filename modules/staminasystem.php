<?php

require_once "modules/staminasystem/lib/lib.php";

function staminasystem_getmoduleinfo(){
	$info=array(
		"name"=>"Expanded Stamina System - Core",
		"version"=>"20090329",
		"author"=>"Dan Hall, aka Caveman Joe, improbableisland.com",
		"override_forced_nav"=>true,
		"category"=>"Stamina",
		"download"=>"",
		"settings"=>array(
			"actionsarray"=>"Array of actions in the game that use Stamina,viewonly",
			"turns_emulation_base"=>"Use an approximation of turns for events that are not hooked in yet - and if so then a Turn is worth at least this much Stamina (set to zero to disable),int|20000",
			"turns_emulation_ceiling"=>"One turn is worth at most this amount,int|30000",
		),
		"prefs"=>array(
			"stamina"=>"Player's current Stamina,int|0",
			//"daystamina"=>"Player's New Day Stamina,int|2000000",
			"red"=>"Amount of the bar taken up in Red Stamina levels,int|300000",
			"amber"=>"Amount of the bar taken up in Amber Stamina levels,int|500000",
			"actions"=>"Player's Actions array,textarea|array()",
			"buffs"=>"Player's Buffs array,textarea|array()",
			"user_minihof"=>"Show me the mini-HOF for Stamina-related actions,bool|true",
		)
	);
	return $info;
}

function staminasystem_install(){
	include("staminasystem/installer.php");
	return true;
}

function staminasystem_uninstall(){
	return true;
}

function staminasystem_dohook($hookname,$args){
	global $stamina,$session;
	include("staminasystem/dohook/switch_$hookname.php");
	return $args;
}

function staminasystem_run(){
	global $session;
	$op = httpget('op');
	include("staminasystem/run/case_$op.php");
}
?>
