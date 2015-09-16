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
		"version"=>"2009-03-29",
		"author"=>"Dan Hall",
		"category"=>"Stamina",
		"download"=>"",
	);
	return $info;
}
function staminacorecombat_install(){
	module_addhook_priority("forest",0);
	module_addhook('everyhit-loggedin');
	module_addhook("startofround-prebuffs");
	module_addhook("endofround");
	module_addhook("fightnav");
	module_addhook("fightnav-graveyard");
	install_action("Hunting - Normal",array(
		"maxcost"=>25000,
		"mincost"=>10000,
		"firstlvlexp"=>1000,
		"expincrement"=>1.08,
		"costreduction"=>150,
		"class"=>"Hunting"
	));
	install_action("Hunting - Big Trouble",array(
		"maxcost"=>30000,
		"mincost"=>10000,
		"firstlvlexp"=>1000,
		"expincrement"=>1.08,
		"costreduction"=>200,
		"class"=>"Hunting"
	));
	install_action("Hunting - Easy Fights",array(
		"maxcost"=>20000,
		"mincost"=>10000,
		"firstlvlexp"=>1000,
		"expincrement"=>1.08,
		"costreduction"=>100,
		"class"=>"Hunting"
	));
	install_action("Hunting - Suicidal",array(
		"maxcost"=>35000,
		"mincost"=>10000,
		"firstlvlexp"=>1000,
		"expincrement"=>1.08,
		"costreduction"=>250,
		"class"=>"Hunting"
	));
	install_action("Fighting - Standard",array(
		"maxcost"=>2000,
		"mincost"=>500,
		"firstlvlexp"=>2000,
		"expincrement"=>1.1,
		"costreduction"=>15,
		"class"=>"Combat"
	));
	install_action("Running Away",array(
		"maxcost"=>1000,
		"mincost"=>200,
		"firstlvlexp"=>500,
		"expincrement"=>1.05,
		"costreduction"=>8,
		"class"=>"Combat"
	));
	//triggers when a player loses more than 10% of his total hitpoints in a single round
	install_action("Taking It on the Chin",array(
		"maxcost"=>2000,
		"mincost"=>200,
		"firstlvlexp"=>5000,
		"expincrement"=>1.1,
		"costreduction"=>15,
		"class"=>"Combat"
	));
	return true;
}
function staminacorecombat_uninstall(){
	uninstall_action("Hunting - Normal");
	uninstall_action("Hunting - Big Trouble");
	uninstall_action("Hunting - Easy Fights");
	uninstall_action("Hunting - Suicidal");
	uninstall_action("Fighting - Standard");
	uninstall_action("Running Away");
	uninstall_action("Taking It on the Chin");
	return true;
}
function staminacorecombat_dohook($hookname,$args){
	global $session;
	static $damagestart = 0;
	switch($hookname){
	case "everyhit-loggedin":
		if ($session['user']['alive']==1){
			staminacorecombat_applystaminabuff();
		}
	break;
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
		addnav(array("T?Look for Trouble (`Q%s%%`0)", $normalcost),"forest.php?op=search&stam=search");
		if ($session['user']['level']>1){
			addnav(array("E?Look for an Easy Fight (`Q%s%%`0)", $slumcost),"forest.php?op=search&type=slum&stam=slum");
		}
		addnav(array("B?Look for Big Trouble (`Q%s%%`0)", $thrillcost),"forest.php?op=search&type=thrill&stam=thrill");
		if (getsetting("suicide", 0)) {
			if (getsetting("suicidedk", 10) <= $session['user']['dragonkills']) {
				addnav(array("*?Search `\$Suicidally`0 (`Q%s%%`0)",$suicidecost), "forest.php?op=search&type=suicide&stam=suicide");
			}
		}
		break;
	case "fightnav-graveyard":
	case "fightnav":
		$script = $args['script'];
		$fightcost = stamina_getdisplaycost("Fighting - Standard");
		$runcost = stamina_getdisplaycost("Running Away");

		blocknav($script."op=fight");
		blocknav($script."op=run");
		blocknav($script."op=fight&auto=five");
		blocknav($script."op=fight&auto=ten");
		addnav("Standard Fighting");
		addnav(array("F?Fight (`Q%s%%`0)", $fightcost),$script."op=fight&stam=fight");
		addnav(array("R?Run (`Q%s%%`0)", $runcost),$script."op=run&stam=run");
		addnav("Automatic Fighting");
		addnav(array("5?For 5 Rounds (`Q%s%%`0)", ($fightcost*5)),$script."op=fight&auto=five&stam=fight");
		addnav(array("1?For 10 Rounds (`Q%s%%`0)", ($fightcost*10)),$script."op=fight&auto=ten&stam=fight");
		break;
	case "startofround-prebuffs":
		$process = httpget("stam");
		
		switch($process){
			case "search":
				$return = process_action("Hunting - Normal");
				if ($return['lvlinfo']['levelledup']==true){
					output("`c`b`0You gained a level in Looking for Trouble!  This action costs fewer Stamina points now, so you can find more beasties to aggress!`b`c`n`n");
				}
			break;
			case "slum":
				$return = process_action("Hunting - Easy Fights");
				if ($return['lvlinfo']['levelledup']==true){
					output("`c`b`0You gained a level in Looking for Easy Fights!  This action costs fewer Stamina points now, so you can pick on more small creatures!`b`c`n`n");
				}
			break;
			case "thrill":
				$return = process_action("Hunting - Big Trouble");
				if ($return['lvlinfo']['levelledup']==true){
					output("`c`b`0You gained a level in Looking for Big Trouble!  This action costs fewer Stamina points now, so you can throw yourself on the mercy of large creatures more often!`b`c`n`n");
				}
			break;
			case "suicide":
				$return = process_action("Hunting - Suicidal");
				if ($return['lvlinfo']['levelledup']==true){
					output("`c`b`0You gained a level in Looking for Really Big Trouble!  This action costs fewer Stamina points now, so you can put yourself in mortal danger more often!`b`c`n`n");
				}
			break;
		}
		if ($session['user']['alive']==1){
			staminacorecombat_applystaminabuff();
		}
		$damagestart = $session['user']['hitpoints'];
		break;
	case "endofround":
		$damagetaken = $damagestart - $session['user']['hitpoints'];
		if (httpget("stam")=="fight" || httpget("op")=="fight"){
			$return = process_action("Fighting - Standard");
			if ($return['lvlinfo']['levelledup']==true){
				output("`n`c`b`0You gained a level in Standard Fighting!  You are now level %s!  This action will cost fewer Stamina points now.`b`c`n",$return['lvlinfo']['newlvl']);
			}
		} else if ((httpget("op")=="fight" && httpget("auto")) || (httpget("op")=="fight" && httpget("skill"))){
			$return = process_action("Fighting - Standard");
			if ($return['lvlinfo']['levelledup']==true){
				output("`n`c`b`0You gained a level in Standard Fighting!  You are now level %s!  This action will cost fewer Stamina points now.`b`c`n",$return['lvlinfo']['newlvl']);
			}
		}
		if (httpget("stam")=="run" || httpget("op")=="run"){
			$return = process_action("Running Away");
			if ($return['lvlinfo']['levelledup']==true){
				output("`n`c`b`0You gained a level in Running Away!  You are now level %s!  This action will cost fewer Stamina points now, so you can run away like a cowardly dog more often!`b`c`n",$return['lvlinfo']['newlvl']);
			}
		}
		$reps = ($damagetaken / $session['user']['maxhitpoints']) * 9;
		if ($reps >= 1){
			$staminalost = 0;
			for ($i=0; $i<floor($reps); $i++){
				$return = process_action("Taking It on the Chin");
				$staminalost += $return['points_used'];
				if ($return['lvlinfo']['levelledup']==true){
					output("`n`c`b`0You gained a level in Taking It On The Chin!  You are now level %s!  This action will cost fewer Stamina points now, so getting beaten up will tire you out a little less.  Good thing, really!`b`c`n",$return['lvlinfo']['newlvl']);
				}
			}
			output("The force of the blow sends you reeling, and knocks %s Stamina points out of you!`n",$staminalost);
		}
		break;
	}
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
	} else {
		strip_buff('stamina-corecombat-exhaustion');
	}
	
	$red = get_stamina(0);
	if ($red < 100){
		$death = e_rand(0,100);
		if ($death > $red){
			output("`\$Vision blurring, you succumb to the effects of exhaustion.  You take a step forward to strike your enemy, but instead trip over your own feet.`nAs the carpet of leaves and twigs drifts lazily up to meet your face, you close your eyes and halfheartedly reach out your hands to cushion the blow - but they sail through the ground as if it were made out of clouds.`nYou fall.`nUnconsciousness.  How you'd missed it.`0");
			$session['user']['hitpoints']=0;
			
			redirect("shades.php");
		}
	}
	return true;
}
?>