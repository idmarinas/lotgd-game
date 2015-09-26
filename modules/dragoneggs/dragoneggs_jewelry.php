<?php
function dragoneggs_jewelry(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Oliver, the Jeweler");
	output("`c`b`&Oliver's Jewelry`b`c`7`n");
	//If the player isn't in the right city, move them to it.
	if ($session['user']['location']!= get_module_setting("jewelerloc","jeweler")){
		$session['user']['location'] = get_module_setting("jewelerloc","jeweler");
	}
	$open=get_module_setting("jewelryopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("jewelrymin") && get_module_setting("jewelrylodge")>0 && get_module_pref("jewelryaccess")==0){
		output("You don't have enough `@Green Dragon Kills`7 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("jewelrymin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("jewelrylodge")>0 && get_module_pref("jewelryaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("You're out of research turns for today.");
	}else{
		output("You decide to look for Dragon Eggs at Oliver's Jewelry.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(3){
			case 1: case 2: 
				output("You explain to `3Oliver`7 that you'd like to look in the basement and he agrees to let you.");
				output("`n`nAs you approach the basement door, the lights suddenly blink in and out. You stumble and tumble down the stairs!`n`n");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $chance<=7) || ($level<=10 && $chance<=6)){
					output("Luckily, you're quick enough to catch the railing before hitting the ground.  No harm done!");
				}else{
					output("You `\$lose all hitpoints except one`7.");
					if ($session['user']['level']>6 && $session['user']['maxhitpoints']>$session['user']['level']*12&&e_rand(1,3)==1){
						output("You also `\$lose a permanent hitpoint`7.");
						$session['user']['maxhitpoints']--;
						debuglog("lost all hitpoints except one and one permanent hitpoint while researching dragon eggs at Oliver's Jewelry.");
					}else debuglog("lost all hitpoints except one while researching dragon eggs at Oliver's Jewelry.");
					$session['user']['hitpoints']=1;
				}
			break;
			case 3: case 4:
				output("In one of the display cases, you ask to see a beautiful crystal.");
				output("`n`n`&'Ah, that's a rare one.  You have good taste.  I can't sell that one to you for money though.  It costs `%gems`&,' `7explains `3Oliver`7.");
				if ($session['user']['gems']>=1){
					addnav("Offer 1 gem","runmodule.php?module=dragoneggs&op=jewelry3&op2=1");
					if ($session['user']['gems']>=3){
						addnav("Offer 3 gems","runmodule.php?module=dragoneggs&op=jewelry3&op2=3");
						if ($session['user']['gems']>=6) addnav("Offer 6 gems","runmodule.php?module=dragoneggs&op=jewelry3&op2=6");
					}
				}else output("`n`n`7Not having any gems to offer, you sadly decline to purchase it.");
			break;
			case 5: case 6:
				output("`3Oliver`7`7 asks if you can help change an oil lamp.  You agree, climb on the ladder, and start telling him an oil lamp joke.");
				output("`n`n`#'Hey `3Oliver`#,'`7 you say, `#'How many Vampires does it take to change an oil lamp?'");
				output("`n`n`3Oliver`7 rolls his eyes and tells you that he doesn't know.");
				output("`n`n`#'One.  He just... AARRRRGGHHHHHH!!!!'");
				output("`n`n`3Oliver `7 doesn't quite get the joke until he realizes you've fallen off the ladder and seriously injured yourself.");
				output("`n`nYou `\$lose all hitpoints except 1`7.");
				$session['user']['hitpoints']=1;
				debuglog("lost all hitpoints except one while researching dragon eggs at Oliver's Jewelry.");
			break;
			//
			case 7: case 8:
				output("You go down to the basement and start looking around.  You see something shiny in a crevice in the wall.  Would you like to stick your hand into the crevice to grab whatever's in there?");
				addnav("Grab in the Crevice","runmodule.php?module=dragoneggs&op=jewelry7&op2=7");
			break;
			case 9: case 10: 
				output("What's the best place to research? Why, it's the bathroom, of course.");
				output("`n`nYou check the facilities and let the water run for a little while. Everything looks in order and you're about to turn the faucet off when the water turns `\$BLOOD RED`7!!!`n`n");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
					output("Luckily, you retain your sensibilities and turn the faucet off.");
				}else{
					if (isset($session['bufflist']['visions'])) {
						output("Ooooh! Pretty colors! Since you're already seeing Disturbing Visions, this really doesn't have much of an effect on you.");
					}else{
						output("The madness overwhelms you! For the rest of your day you're going to have flashes of this disturbing vision.");
						apply_buff('visions',array(
							"name"=>translate_inline("Disturbing Visions"),
							"rounds"=>500,
							"wearoff"=>translate_inline("`4The terrible vision fades."),
							"roundmsg"=>translate_inline("`b`\$THE BLOOD! OH THE BLOOD!!!`b"),
							"atkmod"=>.96,
						));
						debuglog("received a Disturbing Visions buff while researching dragon eggs at Oliver's Jewelry.");
					}
				}
			break;
			case 11: case 12:
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				$gold=e_rand(25,45);
				if (($level>7 && $chance<=4) || ($level<=7 && $chance<=2)) $gold*=e_rand(5,9);
				output("You find yourself in a back room looking for anything useful.  You notice a very old desk with a thick layer of dust on it.  When you open the drawer, you find `^%s gold`7.  Clearly nobody needs it as much as you do!",$gold);
				$session['user']['gold']+=$gold;
				debuglog("gained $gold gold while researching dragon eggs at Oliver's Jewelry.");
			break;
			case 13: case 14:
				$level=$session['user']['level'];
				$chance=e_rand(1,7);
				output("You sit down to read an old manuscript that you find in the basement.`n`n");
				if (($level>7 && $chance<=4) || ($level<=7 && $chance<=2)){
					output("You get to a paragraph of strange writing and read the text to yourself. A mystical power sweeps across you.");
					output("`n`n`QYou Advance in your Specialty`&.");
					require_once("lib/increment_specialty.php");
					increment_specialty("`Q");
					debuglog("increment specialty by researching at Oliver's Jewelry.");
				}else{
					if ($session['user']['turns']>0 && e_rand(1,3)==1){
						output("You find yourself lost in time as you read it... new information fills you with great plans and ideas.");
						output("`n`nYou `@lose a turn`7 reading the book but when you close it you notice there is a gem encrusted on the back cover! You `%gain a gem`7!");
						$session['user']['turns']--;
						$session['user']['gems']++;
						debuglog("lost a turn but gained a gem by researching at Oliver's Jewelry.");
					}else{
						output("It turns out to be a useless novel; not worthy of your time. You leave out of boredom.");
					}
				}
			break;
			case 15: case 16:
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				output("You hear a creaking sound in the ceiling... Oh no! It's collapsing!!`n`n");
				if (($level>7 && $chance<=4) || ($level<=7 && $chance<=2)){
					output("You narrowly avoid getting crushed... but you decide you want to get out of the Jeweler's to catch a breath of fresh air.");
					blocknav("runmodule.php?module=jeweler");
				}else{
					output("It hits you on the head and knocks you unconscious.`n`nYou `\$lose all your hitpoints`7 except one.");
					$session['user']['hitpoints']=1;
					if ($session['user']['turns']>0){
						output("You `@lose a turn`7 recovering from the clunk on your noggin.");
						$session['user']['turn']--;
						debuglog("lost all hitpoints except 1 and a turn by researching at Oliver's Jewelry.");
					}else debuglog("lost all hitpoints except 1 by researching at Oliver's Jewelry.");
				}
			break;
			case 17: case 18:
				output("Figuring there MUST be something useful in the basement, you go to check it out.");
				output("`n`nYou see a shimmering light ahead of you.");
				output("Will you enter the light?");
				addnav("Yes","runmodule.php?module=dragoneggs&op=jewelry17&op2=1");
				addnav("No","runmodule.php?module=dragoneggs&op=jewelry17&op2=2");
				blocknav("runmodule.php?module=jeweler");
				blocknav("village.php");
			break;
			case 19: case 20:
				output("You enter a back room and start looking around.  You see a very ornate checkers set... and the pieces start moving on their own!!");
				output("`n`nOkay, that's just stupid... I mean, a haunted chess set would be cool... but checkers? Honestly, who still plays checkers?");
				output("`n`nYou head back to town.");
				blocknav("runmodule.php?module=jeweler");
			break;
			case 21: case 22: case 23: case 24:
				output("While you're looking around at the Jewelry cases, you see a strange man.  He introduces himself as `%Andy Arrow`7.`n`n");
				output("He starts to tell you about how he fought a `@Green Dragon`7 and is about to describe the encounter in detail.");
				output("`n`nWill you listen?");
				addnav("Yes","runmodule.php?module=dragoneggs&op=jewelry21&op2=1");
				addnav("No","runmodule.php?module=dragoneggs&op=jewelry21&op2=2");
				blocknav("runmodule.php?module=jeweler");
				blocknav("village.php");
			break;
			case 25: case 26: case 27: case 28:
				output("You see some strange green slime in the corner of the basement and go to look closer.  Something's moving inside it!");
				output("`n`n`@`bGreen Slime`b`7!!!");
				addnav("Fight the `@Green Slime","runmodule.php?module=dragoneggs&op=attack");
				set_module_pref("monster",7);
				blocknav("runmodule.php?module=jeweler");
				blocknav("village.php");
			break;
			case 29: case 30: case 31: case 32: case 33: case 34: case 35:
				output("You don't find anything of value.");
			break;
			case 36:
				dragoneggs_case36();
			break;
		}
	}
	addnav("Return to Oliver's Jewelry","runmodule.php?module=jeweler");
	villagenav();
}
?>