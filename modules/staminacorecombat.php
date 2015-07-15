<?php

require_once "modules/staminasystem/lib/lib.php";

/*STAMINA ACTIONS USED

Hunting - Normal
Used when hunting for a normal-level creature to kill.

Hunting - Big Trouble
Used when thrillseeking.

Hunting - Easy Fights
Used when slumming.

Hunting - Suicidal
Used when searching suicidally.

*/

function staminacorecombat_getmoduleinfo(){
	$info = array(
		"name"=>"Stamina System - Core Combat",
		"version"=>"0.1 2009-01-06",
		"author"=>"Dan Hall",
		"category"=>"Stamina",
		"download"=>"",
	);
	return $info;
}
function staminacorecombat_install(){
	module_addhook_priority("forest",0);
	module_addhook("startofround");
	module_addhook("endofround");
	install_action("Hunting - Normal",array(
		"maxcost"=>25000,
		"mincost"=>10000,
		"expperrep"=>100,
		"expforlvl"=>1000,
		"costreduction"=>10,
		"dkpct"=>2.5
	));
	install_action("Hunting - Big Trouble",array(
		"maxcost"=>30000,
		"mincost"=>10000,
		"expperrep"=>100,
		"expforlvl"=>1000,
		"costreduction"=>10,
		"dkpct"=>2.5
	));
	install_action("Hunting - Easy Fights",array(
		"maxcost"=>20000,
		"mincost"=>10000,
		"expperrep"=>100,
		"expforlvl"=>1000,
		"costreduction"=>10,
		"dkpct"=>2.5
	));
	install_action("Hunting - Suicidal",array(
		"maxcost"=>35000,
		"mincost"=>10000,
		"expperrep"=>100,
		"expforlvl"=>1000,
		"costreduction"=>10,
		"dkpct"=>2.5
	));
	install_action("Fighting - Standard",array(
		"maxcost"=>2000,
		"mincost"=>500,
		"expperrep"=>100,
		"expforlvl"=>100000,
		"costreduction"=>10,
		"dkpct"=>2.5
	));
	install_action("Running like a Coward",array(
		"maxcost"=>1000,
		"mincost"=>500,
		"expperrep"=>100,
		"expforlvl"=>500,
		"costreduction"=>10,
		"dkpct"=>2.5
	));
	install_action("Taking Damage",array(
		"maxcost"=>600,
		"mincost"=>100,
		"expperrep"=>100,
		"expforlvl"=>1000,
		"costreduction"=>5,
		"dkpct"=>2.5
	));
	return true;
}
function staminacorecombat_uninstall(){
	uninstall_action("Hunting - Normal");
	uninstall_action("Hunting - Big Trouble");
	uninstall_action("Hunting - Easy Fights");
	uninstall_action("Hunting - Suicidal");
	uninstall_action("Fighting - Standard");
	uninstall_action("Running like a Coward");
	uninstall_action("Taking Damage");
	return true;
}
function staminacorecombat_dohook($hookname,$args){
	global $session;
	static $damagestart = 0;
	switch($hookname){
	case "forest":
		blocknav("forest.php?op=search");
		blocknav("forest.php?op=search&type=slum");
		blocknav("forest.php?op=search&type=thrill");
		blocknav("forest.php?op=search&type=suicide");
		addnav("Fight");
		$normalcost = stamina_getdisplaycost("Hunting - Normal");
		$slumcost = stamina_getdisplaycost("Hunting - Easy Fights");
		$thrillcost = stamina_getdisplaycost("Hunting - Big Trouble");
		$suicidecost = stamina_getdisplaycost("Hunting - Suicidal");
		addnav(array("T?Look for Trouble (`Q%s%%`0)", $normalcost),"runmodule.php?module=staminacorecombat&op=search");
		addnav(array("E?Look for an Easy Fight (`Q%s%%`0)", $slumcost),"runmodule.php?module=staminacorecombat&op=slum");
		addnav(array("B?Look for Big Trouble (`Q%s%%`0)", $thrillcost),"runmodule.php?module=staminacorecombat&op=thrill");
		if (getsetting("suicide", 0)) {
			if (getsetting("suicidedk", 10) <= $session['user']['dragonkills']) {
				addnav(array("*?Search `\$Suicidally`0 (`Q%s%%`0)",$suicidecost), "runmodule.php?module=staminacorecombat&op=suicide");
			}
		}
		break;
	case "startofround":
		if ($session['user']['alive']==1){
			staminacorecombat_applystaminabuff();
		}
		$damagestart = $session['user']['hitpoints'];
		debug($damagestart);
		break;
	case "endofround":
		debug($damagestart);
		$damagetaken = $damagestart - $session['user']['hitpoints'];
		debug($damagetaken);
		if (httpget("op")=="fight"){
			process_action("Fighting - Standard");
		}
		if (httpget("op")=="run"){
			process_action("Running like a Coward");
		}
		if ($damagetaken > 0){
			for ($i=0; $i<$damagetaken; $i++){
				process_action("Taking Damage");
			}
		}
		break;
	}
	return $args;
}
function staminacorecombat_run(){
	global $session;
	$op = httpget('op');
	if ($op=="search"){
		process_action("Hunting - Normal");
		redirect("forest.php?op=search");
	}
	if ($op=="slum"){
		process_action("Hunting - Easy Fights");
		redirect("forest.php?op=search&type=slum");
	}
	if ($op=="thrill"){
		process_action("Hunting - Big Trouble");
		redirect("forest.php?op=search&type=thrill");
	}
	if ($op=="suicide"){
		process_action("Hunting - Suicidal");
		redirect("forest.php?op=search&type=suicide");
	}
	page_footer();
	return $args;
}

function staminacorecombat_applystaminabuff(){
	//increments and applies the Exhaustion Penalty
	global $session;
	
	$amber = get_stamina();
	if ($amber < 100){
		//Gives a proportionate debuff from 1 to 0.2, at 2 decimal places each time
		$buffvalue=round(((($amber/100)*80)+20)/100,2);
		if ($buffvalue < 1){
			$buffmsg = "`0You're getting tired...";
		}
		if ($buffvalue < 0.8){
			$buffmsg = "`4You're getting `ivery`i tired...`0";
		}
		if ($buffvalue < 0.6){
			$buffmsg = "`\$You're getting `bexhausted!`b`0";
		}
		if ($buffvalue < 0.3){
			$buffmsg = "`\$You're getting `bdangerously exhausted!`b`0";
		}
		apply_buff('stamina-corecombat-exhaustion', array(
			"name"=>"Exhaustion",
			"atkmod"=>$buffvalue,
			"defmod"=>$buffvalue,
			"rounds"=>-1,
			"roundmsg"=>$buffmsg,
			"schema"=>"module-staminacorecombat"
		));
	}
	
	$red = get_stamina(0);
	if ($red < 100){
		$death = e_rand(0,100);
		if ($death > $red){
			output("`\$Vision blurring, you succumb to the effects of exhaustion.  You take a step forward to strike your enemy, but instead trip over your own feet.`nAs the carpet of leaves and twigs drifts lazily up to meet your face, you close your eyes and halfheartedly reach out your hands to cushion the blow - but they sail through the ground as if it were made out of clouds.`nYou fall.`nUnconsciousness.  How you'd missed it.`0");
			$session['user']['hitpoints']=0;
		}
	}
	return true;
}
?>