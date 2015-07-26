<?php

function staminamounts_getmoduleinfo(){
	$info = array(
		"name"=>"Stamina System - Mounts",
		"version"=>"2009-01-12",
		"author"=>"Dan Hall",
		"category"=>"Stamina",
		"download"=>"",
		"prefs-mounts"=>array(
			"Buff 1,title",
			"buff1name"=>"Buff Name,text|",
			"buff1action"=>"Action to Buff,text|",
			"buff1class"=>"Class to Buff,text|",
			"buff1costmod"=>"Costmod,float|",
			"buff1expmod"=>"Expmod,float|",
			"buff1rounds"=>"Rounds,int|",
			"buff1roundmsg"=>"Round message,text|",
			"buff1wearoffmsg"=>"Wearoff message,text|",
			"Buff 2,title",
			"buff2name"=>"Buff Name,text|",
			"buff2action"=>"Action to Buff,text|",
			"buff2class"=>"Class to Buff,text|",
			"buff2costmod"=>"Costmod,float|",
			"buff2expmod"=>"Expmod,float|",
			"buff2rounds"=>"Rounds,int|",
			"buff2roundmsg"=>"Round message,text|",
			"buff2wearoffmsg"=>"Wearoff message,text|",
			"Buff 3,title",
			"buff3name"=>"Buff Name,text|",
			"buff3action"=>"Action to Buff,text|",
			"buff3class"=>"Class to Buff,text|",
			"buff3costmod"=>"Costmod,float|",
			"buff3expmod"=>"Expmod,float|",
			"buff3rounds"=>"Rounds,int|",
			"buff3roundmsg"=>"Round message,text|",
			"buff3wearoffmsg"=>"Wearoff message,text|",
			"Buff 4,title",
			"buff4name"=>"Buff Name,text|",
			"buff4action"=>"Action to Buff,text|",
			"buff4class"=>"Class to Buff,text|",
			"buff4costmod"=>"Costmod,float|",
			"buff4expmod"=>"Expmod,float|",
			"buff4rounds"=>"Rounds,int|",
			"buff4roundmsg"=>"Round message,text|",
			"buff4wearoffmsg"=>"Wearoff message,text|",
			"Buff 5,title",
			"buff5name"=>"Buff Name,text|",
			"buff5action"=>"Action to Buff,text|",
			"buff5class"=>"Class to Buff,text|",
			"buff5costmod"=>"Costmod,float|",
			"buff5expmod"=>"Expmod,float|",
			"buff5rounds"=>"Rounds,int|",
			"buff5roundmsg"=>"Round message,text|",
			"buff5wearoffmsg"=>"Wearoff message,text|",
			"Buff 6,title",
			"buff6name"=>"Buff Name,text|",
			"buff6action"=>"Action to Buff,text|",
			"buff6class"=>"Class to Buff,text|",
			"buff6costmod"=>"Costmod,float|",
			"buff6expmod"=>"Expmod,float|",
			"buff6rounds"=>"Rounds,int|",
			"buff6roundmsg"=>"Round message,text|",
			"buff6wearoffmsg"=>"Wearoff message,text|",
			"Buff 7,title",
			"buff7name"=>"Buff Name,text|",
			"buff7action"=>"Action to Buff,text|",
			"buff7class"=>"Class to Buff,text|",
			"buff7costmod"=>"Costmod,float|",
			"buff7expmod"=>"Expmod,float|",
			"buff7rounds"=>"Rounds,int|",
			"buff7roundmsg"=>"Round message,text|",
			"buff7wearoffmsg"=>"Wearoff message,text|",
			"Buff 8,title",
			"buff8name"=>"Buff Name,text|",
			"buff8action"=>"Action to Buff,text|",
			"buff8class"=>"Class to Buff,text|",
			"buff8costmod"=>"Costmod,float|",
			"buff8expmod"=>"Expmod,float|",
			"buff8rounds"=>"Rounds,int|",
			"buff8roundmsg"=>"Round message,text|",
			"buff8wearoffmsg"=>"Wearoff message,text|",
			"Buff 9,title",
			"buff9name"=>"Buff Name,text|",
			"buff9action"=>"Action to Buff,text|",
			"buff9class"=>"Class to Buff,text|",
			"buff9costmod"=>"Costmod,float|",
			"buff9expmod"=>"Expmod,float|",
			"buff9rounds"=>"Rounds,int|",
			"buff9roundmsg"=>"Round message,text|",
			"buff9wearoffmsg"=>"Wearoff message,text|",
			"Buff 10,title",
			"buff10name"=>"Buff Name,text|",
			"buff10action"=>"Action to Buff,text|",
			"buff10class"=>"Class to Buff,text|",
			"buff10costmod"=>"Costmod,float|",
			"buff10expmod"=>"Expmod,float|",
			"buff10rounds"=>"Rounds,int|",
			"buff10roundmsg"=>"Round message,text|",
			"buff10wearoffmsg"=>"Wearoff message,text|",
		)
	);
	return $info;
}
function staminamounts_install(){
	module_addhook("stamina-newday");
	module_addhook("boughtmount");
	module_addhook("soldmount");
	return true;
}
function staminamounts_uninstall(){
	return true;
}
function staminamounts_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "boughtmount":
	case "soldmount":
	case "stamina-newday":
		require_once "modules/staminasystem/lib/lib.php";
		$currentmount = $session['user']['hashorse'];
		for ($i=1;$i<=10;$i++){
			if (get_module_objpref("mounts",$currentmount,"buff".$i."name","staminamounts") || get_module_objpref("mounts",$currentmount,"buff".$i."costmod","staminamounts") || get_module_objpref("mounts",$currentmount,"buff".$i."expmod","staminamounts")){
				apply_stamina_buff("mountbuff".$i, array(
					"name"=>get_module_objpref("mounts",$currentmount,"buff".$i."name","staminamounts"),
					"action"=>get_module_objpref("mounts",$currentmount,"buff".$i."action","staminamounts"),
					"class"=>get_module_objpref("mounts",$currentmount,"buff".$i."class","staminamounts"),
					"costmod"=>get_module_objpref("mounts",$currentmount,"buff".$i."costmod","staminamounts"),
					"expmod"=>get_module_objpref("mounts",$currentmount,"buff".$i."expmod","staminamounts"),
					"rounds"=>get_module_objpref("mounts",$currentmount,"buff".$i."rounds","staminamounts"),
					"roundmsg"=>get_module_objpref("mounts",$currentmount,"buff".$i."roundmsg","staminamounts"),
					"wearoffmsg"=>get_module_objpref("mounts",$currentmount,"buff".$i."wearoffmsg","staminamounts"),
				));
			}
		}
		break;
	}
	return $args;
}
?>