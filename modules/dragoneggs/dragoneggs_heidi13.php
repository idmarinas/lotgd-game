<?php
function dragoneggs_heidi13(){
	global $session;
	page_header("Heidi's Place");
	output("`c`b`&Heidi, the Well-wisher`b`c`7`n");
	$chance=e_rand(1,9);
	if ((is_module_active("cities") && $session['user']['location']!= getsetting("villagename", LOCATION_FIELDS))||is_module_active("cities")==0) $openh=1;
	else $openh=0;
	if (($session['user']['level']>10 && $chance<4) || ($session['user']['level']<11 && $chance<3)){
		output("You decide to work with the student for a little while and settle in to work on the dimensional transmorgifier.`n`n");
		if ($chance==1 && $session['user']['turns']>0){
			output("Things are going really well and you decide to spend a turn tweaking things to really get them going well.");
			output("`n`nTogether, you triumphantly hit the 'ON' button and wait...");
			output("`n`nThere's a strange buzzing sound... then a whirrling... then a clunking...");
			output("`n`nBeams shoot out from the machine and you hear a vaccuum-like sucking sound.  The beams target 2 hidden dragon eggs!");
			output("`n`nYou've `&Destroyed 2 Dragon Eggs`7!!!");
			$session['user']['turn']--;
			increment_module_pref("dragoneggs",2,"dragoneggpoints");
			increment_module_pref("dragoneggshof",2,"dragoneggpoints");
			addnews("%s `7destroyed `&2 Dragon Eggs`7 simultaneously.  How amazing is that???",$session['user']['name']);
			debuglog("gained 2 dragon egg points and spent a turn helping a student at Heidi's Place.");
		}else{
			output("Things are going well and you turn it on...");
			output("`n`nBeams shoot out from the machine and you hear the sound of an egg cracking.");
			output("`n`nYou've `&Destroyed a Dragon Egg`7!!!");
			increment_module_pref("dragoneggs",1,"dragoneggpoints");
			increment_module_pref("dragoneggshof",1,"dragoneggpoints");
			addnews("%s `7closed a `&Dragon Egg`7 with some experimental technology.",$session['user']['name']);
			debuglog("destroyed a dragon egg by helping a student at Heidi's Place.");
		}
		addnav("Return to Heidi's Place","runmodule.php?module=heidi");
		villagenav();
	}elseif ($openh==1){
		output("You work and struggle to get the machine together but it just isn't meant to be. In a futile attempt, you turn it on prematurely and a piece of metal hits you in the arm!`n`n");
		if ($session['user']['hitpoints']>10){
			output("You lose `\$10 hitpoints`7 and decide you better go to the healer to get checked out.");
			$session['user']['hitpoints']-=10;
			debuglog("lost 10 hitpoints by researching at Heidi's Place.");
		}else{
			if (isset($session['bufflist']['injury'])) {
				output("Unfortunately, you hit the EXACT same spot on your arm.  Although it didn't do anything permanent but its going to hurt for another 5 rounds.");
				$session['bufflist']['injury']['rounds'] += 5;
				debuglog("extended the injury buff for 5 rounds while researching dragon eggs at Heidi's Place.");
			}else{
				apply_buff('injury',array(
					"name"=>translate_inline("Injury"),
					"rounds"=>10,
					"wearoff"=>translate_inline("`4Your arm heals."),
					"atkmod"=>.95,
				));
				debuglog("received the injury buff while researching dragon eggs at Heidi's Place.");
				output("Luckily, it seems like it didn't do anything permanent but it certainly hurts.");
			}
		}
		addnav("Continue","healer.php");
	}else{
		output("You work and struggle to get the machine together but it just isn't meant to be. In a futile attempt, you turn it on prematurely and a piece of metal hits you in the arm!`n`n");
		if (isset($session['bufflist']['injury'])) {
			output("Unfortunately, you hit the EXACT same spot on your arm.  Although it didn't do anything permanent but its going to hurt for another 5 rounds.");
			$session['bufflist']['injury']['rounds'] += 5;
			debuglog("extended the injury buff for 5 rounds while researching dragon eggs at Heidi's Place.");
		}else{
			apply_buff('injury',array(
				"name"=>translate_inline("Injury"),
				"rounds"=>10,
				"wearoff"=>translate_inline("`4Your arm heals."),
				"atkmod"=>.95,
			));
			debuglog("received the injury buff while researching dragon eggs at Heidi's Place.");
			output("Luckily, it seems like it didn't do anything permanent but it certainly hurts.");
		}
		addnav("Return to Heidi's Place","runmodule.php?module=heidi");
		villagenav();
	}
}
?>