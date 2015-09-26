<?php
function dragoneggs_train(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Bluspring's Warrior Training");
	output("`c`b`7Bluspring's Warrior Training`b`c");
	//This will try to fix their current location if they are being transported here from a city other than their home
	if (is_module_active("cities") && $op3=="nav" && $session['user']['location'] !=get_module_pref("homecity","cities")) $session['user']['location']=get_module_pref("homecity","cities");
	$open=get_module_setting("uniopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("unimin") && get_module_setting("unilodge")>0 && get_module_pref("uniaccess")==0){
		output("You don't have enough `@Green Dragon Kills`7 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("unimin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("unilodge")>0 && get_module_pref("uniaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("You're out of research turns for today.");
	}else{
		output("You decide to research at the Bluspring's Warrior Training.`n`n");
		increment_module_pref("researches",1);
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		debuglog("used a research turn in Bluspring's Warrior Training.");
		switch($case){
			case 1: case 2:
				output("You meet with one of the masters.  He produces a dragon skull with ancient engravings on it.");
				output("You recognize some of the runes and explain them.  He thanks you by giving you a `%gem`7.");
				$session['user']['gems']++;
				debuglog("gained a gem by researching at Bluspring's Warrior Training.");
			break;
			case 3: case 4:
				output("You arrive at the master's office.");
				output("`n`n`#'I have your IOU here,'`7 says the master, `#'It's for `^550 gold`#.  All you have to do is sign here and I'll pay you back.'`n`n");
				output("`7Realizing there's been a mistake, you have to make a decision.  Will you take the money or explain the error?");
				addnav("Take the Money","runmodule.php?module=dragoneggs&op=train3&op2=1");
				addnav("Explain the Error","runmodule.php?module=dragoneggs&op=train3&op2=2");
				blocknav("train.php");
				blocknav("village.php");
			break;
			case 5: case 6:
				output("You hear the students in the training camp whispering of impending doom.  Soon enough, the camp is being overrun with fear.`n`n");
				output("Quickly, you try to subdue the students and convince them that everything is under control.");
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if (($level>5 && $chance<=3) || ($level<=5 && $chance<=2)){
					output("`n`nLuckily, you're able to convince everyone to relax.  The High Master thanks you and gives you a `^300 gold`7 reward.");
					$session['user']['gold']+=300;
					debuglog("gained 300 gold by researching at Bluspring's Warrior Training.");
				}else{
					output("`n`nUnfortunately, you fail.  Oh well!");
				}
			break;
			case 7: case 8:
				output("You enter a training room and notice several students and a professor gathered around an ancient stone tablet.");
				output("`n`n`#'Would you like to help?'`7 asks the professor.");
				addnav("Yes, I'll help","runmodule.php?module=dragoneggs&op=train7&op2=1");
				addnav("No Thank you","runmodule.php?module=dragoneggs&op=train7&op2=2");
				blocknav("train.php");
				blocknav("village.php");
			break;
			case 9: case 10:
				output("You meet with the physical fitness trainer and he takes a liking to you. `#'I can train you to make you stronger or faster if you'd like.  It will just cost you `^500 gold`#.  You interested?'`7 he asks.");
				if ($session['user']['gold']>=500){
					addnav("Increase Attack for `^500 gold","runmodule.php?module=dragoneggs&op=train9&op2=1");
					addnav("Increase Defense for `^500 gold","runmodule.php?module=dragoneggs&op=train9&op2=2");
				}else output("`n`nNot having the money, you decline.");
			break;
			case 11: case 12: case 13: case 14: case 15: case 16:
				output("You find yourself talking with the High Master of Bluspring's Warrior Training. `#'Have you ever written a treatise on defense?'`7 he asks.`n`n");
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if (($level>5 && $chance<=3) || ($level<=5 && $chance<=2)){
					output("`3'Yes I have,'`7 you reply.  You pull it out and show it to him.  He smiles and buys it from you for `^275 gold`7.");
					$session['user']['gold']+=275;
					debuglog("gained 275 gold by researching at the Bluspring's Warrior Training.");
				}else{
					output("`3'No, I haven't,'`7 you admit. The High Master dismisses you and gets back to his work."); 
				}
			break;
			case 17: case 18:
				output("You find yourself in one of the training rooms and see a table full of unusual papers.");
				output("Next to one of the papers you find a silver key.");
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if (($level>5 && $chance<=3) || ($level<=5 && $chance<=2)){
					if (e_rand(1,4)==1){
						output("`n`nAs soon as you touch it, a mystical power sweeps across you.");
						output("`n`n`QYou Advance in your Specialty`&.");
						require_once("lib/increment_specialty.php");
						increment_specialty("`Q");
						debuglog("increment specialty by researching at the Bluspring's Warrior Training.");
					}else{
						output("`n`nYou pick up the key and realize it is actually a gem.");
						$session['user']['gems']++;
						debuglog("gained a gem by researching at the Bluspring's Warrior Training.");
					}
				}else{
					output("`n`nNot realizing the value of the key, you disregard it.  Perhaps next time you'll pay closer attention to it.");
				}
			break;
			case 19: case 20:
				output("You meet up with the janitor.");
				output("As you walk with him, he mentions that you shouldn't look in the room 'coming up on the right' because he has to 'finish cleaning the mess'.");
				output("`n`n`#'Perhaps you'd like to tell me a little more,'`7 you say. He tries to dissuade you by mentioning that the sheriff are aware and on the case.");
				if ($session['user']['gems']>0){
					output("`n`nRealizing that you should be the judge, you look into the room and you feel a shock go through your system.");
					output("Such carnage... such destruction!  You `%drop a gem`7 and `&lose a charm`7 from the shock.");
					$session['user']['gems']--;
					$session['user']['charm']--;
					debuglog("lost a gem and a charm by researching at the Bluspring's Warrior Training.");
				}else output("`n`nYou decide you don't want to see and take his advice to heart and keep going.");
			break;
			case 21: case 22:
				if (is_module_active("jail") || is_module_active("djail")){
					output("You ask around about the latest activities of the dragon worshippers.  However, you persistence gets the attention of the sheriff.");
					output("`n`n`@'You're going to have to come down to the jail and answer some questions of your own,'`7 he says.");
					output("`n`nYou accompany the sheriff to the jail and find yourself researching events there.");
					blocknav("train.php");
					blocknav("village.php");
					increment_module_pref("researches",-1);
					addnav("Dragon Egg Research at the Jail","runmodule.php?module=dragoneggs&op=police&op3=nav");
				}else{
					output("You hear the students in the training camp whispering of impending doom.  Soon enough, the camp is being overrun with fear.`n`n");
					output("Quickly, you try to subdue the students and convince them that everything is under control.");
					$chance=e_rand(1,5);
					$level=$session['user']['level'];
					if (($level>5 && $chance<=3) || ($level<=5 && $chance<=2)){
						output("`n`nLuckily, you're able to convince everyone to relax.  The High Master thanks you and gives you a `^300 gold`7 reward.");
						$session['user']['gold']+=300;
						debuglog("gained 300 gold by researching at Bluspring's Warrior Training.");
					}else{
						output("`n`nUnfortunately, you fail.  Terror spreads through the school.");
						output("Things can't get any worse and you crumple in horror!!");
					}
				}
			break;
			case 23: case 24: case 25: case 26:
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				output("The High Master approaches you about writing a training manual for Bluspring's Warrior Training.  You contemplate whether you really want to spend the time doing this or not.");
				if ((($level>8 && $chance<=3) || ($level<=8 && $chance<=2)) && get_module_pref("retainer")==0){
					output("`n`nAfter a short deliberation, you realize it may not take much time after all.");
					output("`n`nYou gain a retainer!");
					set_module_pref("retainer",2);
					rawoutput("<small><small>");
					output("`c`^Notes on Retainers`c");
					output("Retainers are a nice cushion for those lucky enough to obtain one.`n`n");
					output("If you get one, then once a system day you may receive a small stipend. If you have a lucky day, it will be more than the standard amount. On an unlucky day, you won't get anything.  If it's a REALLY bad day, you'll lose the retainer.");
					rawoutput("<big><big>");
					debuglog("gained a retainer by researching at the Bluspring's Warrior Training.");
				}else{
					output("`n`nYou decide that it's not worth it and turn down the offer.");
				}
			break;
			case 27: case 28:
				if ((is_module_active("cities") && $session['user']['location']!= getsetting("villagename", LOCATION_FIELDS))||is_module_active("cities")==0) $openh=1;
				else $openh=0;
				output("Your research gets a little strange when you start describing a 'walking squid that kills with mental powers.'");
				blocknav("train.php");
				blocknav("village.php");
				increment_module_pref("researches",-1);
				if ($openh==1){
					output("The sheriff is called and brings you to the `4Healer's Hut`7 for some 'Mental Relaxation'.  You take the opportunity to research there.");
					addnav("Dragon Egg Research at the Healer's Hut","runmodule.php?module=dragoneggs&op=hospital&op3=nav");
				}else{
					output("The sheriff is called and brings you to `@%s Square`7 for some 'Mental Relaxation'.  You take the opportunity to research there.",getsetting("villagename", LOCATION_FIELDS));
					addnav(array("Search at %s Square",getsetting("villagename", LOCATION_FIELDS)),"runmodule.php?module=dragoneggs&op=town&op3=nav");
				}
			break;
			case 29: case 30: case 31: case 32: case 33: case 34: case 35:
				output("You don't find anything of value.");
			break;
			case 36:
				dragoneggs_case36();
			break;
		}
	}
	addnav("Return to Bluspring's Warrior Training","train.php");
	villagenav();
}
?>