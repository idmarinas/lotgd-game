<?php
function dragoneggs_police(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header(array("The Jail of %s", $session['user']['location']));
	output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
	$open=get_module_setting("policeopen");
	if (is_module_active("jail")) $jail="jail";
	else $jail="djail";
	//Move them to the right city if the jail is only located in one city
	if (get_module_setting("oneloc",$jail)==1){
		$session['user']['location']=get_module_setting("jailloc",$jail);
	}
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("policemin") && get_module_setting("policelodge")>0 && get_module_pref("policeaccess")==0){
		output("You don't have enough `@Green Dragon Kills`2 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("policemin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`2 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("policelodge")>0 && get_module_pref("policeaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("You're out of research turns for today.");
	}else{
		output("You decide to look for Dragon Eggs at the Jail.`n`n");
		increment_module_pref("researches",1);
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		debuglog("used a research turn in the Jail.");
		switch($case){
		//switch(25){
			case 1: case 2:
				output("You wander by the cells and notice a strange old man sitting in the back.  He starts to whisper to you.");
				output("`n`n`#'I know what is haunting your nights.  Remember those lambs?  Remember how they used to cry out in the night? What will it take to silence them?!?!?'`2`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
					output("You tell him that the lambs have never stopped crying and he makes a strange happy sound. He tells you a trick to killing a `@Green Dragon`2 and tosses you a gem.");
					output("`n`nYou `%gain a gem`2.");
					debuglog("gained a gem by researching the Jail.");
					$session['user']['gems']++;
				}else{
					output("You start crying uncontrollably.`n`nHow embarrassing! You `&lose 2 charm`2.");
					$session['user']['charm']-=2;
					debuglog("lose 2 charm by researching the Jail.");
				}
			break;
			case 3: case 4:
				output("One of the deputies walks by and a case file falls off the stack that he's carrying.  Being the curious type, you decide to take a look.`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>4 && $chance<=4) || ($level<=4 && $chance<=2)){
					output("`iReport: Methods to help defeat the `@Green Dragons`2 and how to exploit their vulnerabilities...`i");
					output("`n`nYou read the report details. Excellent! In addition, you find `%a gem`2 in the case files. You `%gain a gem`2.");
					$session['user']['gems']++;
					debuglog("gained 1 gem by researching the Jail.");
				}else{
					switch(e_rand(1,5)){
						case 1:
							output("`iReport: Lost hamster.  Needs further research at the Old House...`i");
						break;
						case 2:
							output("`iReport: Lost `^1 gold`2 at the Healer's Hut.  We'll work on that one later.`i");
						break;
						case 3:
							output("`iReport: Blood supply is low at the Healer's Hut.  Consider donating if able.`i");
						break;
						case 4:
							output("`iReport: Lost wallet reported in %s Square`2.  Keep on the lookout for wallet thief.`i",getsetting("villagename", LOCATION_FIELDS));
						break;
						case 5:
							output("`iReport: Stray dog seen walking into the Hall of Fame.  Report to Merick's Stables if seen again.`i");
						break;
					}
					output("`n`nOh well, nothing of use.");
				}
			break;
			case 5: case 6:
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if ((($level>10 && $chance>2) || ($level<=10 && $chance>3)) && $session['user']['weapon']!="`#Deputy's Sword`0" && e_rand(1,2)==1){
					output("You find yourself searching under one of the desks.  You notice that a deputy has misplaced his weapon. Would you like to take it?");
					addnav("Take the Deputy's Sword","runmodule.php?module=dragoneggs&op=police5");
				}else{
					output("After spending 20 minutes looking under one of the deputy's desk you find a stale donut.  How useless.");
				}
			break;
			case 7: case 8:
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				output("You sit down with one of the deputies and try to act all friendly-like.");
				output("`n`n`#'Let me see that file,'`2 you say.`n`n`@");
				if (($level>7 && $chance>4) || ($level<=7 && $chance>3)){
					output("'Okay!'`2 says the deputy and he hands it over.");
					output("`n`nYou read through it briefly.  You recognize the assailant and give the deputy the information.  He gives you `%a gem`2!");
					$session['user']['gems']++;
					debuglog("gained a gem by researching the Jail.");
				}else{
					output("'No.  Go away'`2 he says.  You decide to go away.");
				}
			break;
			case 9: case 10:
				if ($session['user']['turns']>0){
					output("`@'Come check out the jail cells. They're state of the art,'`2 mentions the deputy.");
					output("Thinking this would be a good opportunity, you step in and notice some strange writing on the wall.");
					output("You call the deputy over to take a look and he comes over by you.  You hear a <click> sound and realize he just locked you in the cell!");
					output("`n`nYou `@spend a turn`2 waiting for the sheriff to come and let you two out.");
					$session['user']['turns']--;
					debuglog("lost a turn by researching the Jail.");
				}elseif ($session['user']['gems']>0){
					output("`@'Come check out the jail cells. They're state of the art,'`2 mentions the deputy.");
					output("Thinking this would be a good opportunity, you step in and notice some strange writing on the wall.");
					output("`n`n`c`\$`iThere is no hope`i`2`c");
					output("`nOh no! There's no hope! You lose a `%gem`2 scratching out the grim message.");
					$session['user']['gems']--;
					debuglog("lost a gem by researching the Jail.");
				}elseif (get_module_pref("researches")>0){
					output("Thinking there's a gem hidden in one of the cell toilets, you spend an extra research trying to fish it out but you fail.");
					increment_module_pref("researches",-1);
					debuglog("spent and extra research researching the Jail.");
				}else{
					output("You find yourself staring forelornly at an empty cell.  Nothing happens.");
				}
			break;
			case 11: case 12:
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				output("You wander around the jail looking for something helpful.  One of the deputies cleans his crossbow while you search.`n`nSuddenly, you hear a loud <thwick>!`n`n");
				if (($level>7 && $chance>3) || ($level<=7 && $chance>2)){
					output("You've been shot! Your life hangs in balance... you're down to `\$1 hitpoint`2!");
					$session['user']['hitpoints']=1;
					debuglog("lost all hitpoints except one by researching the Jail.");
				}else{
					output("The bolt whizzes by your ear.  You're lucky to escape without injury!!");
				}
			break;
			case 13: case 14:
				if ($session['user']['weapondmg']>10){
					output("The sheriff looks at your %s`2 and asks to see your permit for carrying that thing.",$session['user']['weapon']);
					output("Not having a permit, he tells you you'll need to pay for one. `@'It only costs `^500 gold`@, otherwise I'll have to take your weapon.'");
					if ($session['user']['gold']+$session['user']['goldinbank']>=500) addnav("Give the Sheriff `^500 Gold","runmodule.php?module=dragoneggs&op=police13&op2=pay");
					addnav("Try to Talk Your Way Out of It","runmodule.php?module=dragoneggs&op=police13&op2=talk");
					blocknav("village.php");
					blocknav("runmodule.php?module=$jail");
				}else{
					output("The sheriff asks to see your weapon.  You hand over your %s`2 for him to look at.",$session['user']['weapon']);
					output("`n`nAfter a couple of minutes, he tells you that everything looks in order and he walks away.");
				}
			break;
			case 15: case 16:
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				output("The sheriff thinks you're doing good work and invites you into his office.`n`n");
				if ((($level>10 && $chance<=3) || ($level<=10 && $chance<=2)) && $session['user']['weapon']!="`@Holy Mace`0"){
					output("He hands you a `@Holy Mace`2; an upgrade from your `^%s`2.",$session['user']['weapon']);
					$session['user']['weapon']="`@Holy Mace`0";
					$session['user']['weapondmg']++;
					$session['user']['attack']++;
					debuglog("received a Holy Mace (1 higher weapon than their previous) by researching at the Jail.");
				}else{
					output("He shakes your hand and thanks you. You feel a little cheated; you wanted something more!");
					output("`n`n`@'Fine.  Here's a gem.'`2  He hands you a `%gem`2.");
					$session['user']['gems']++;
					debuglog("gained a gem by researching at the Jail");
				}
			break;
			case 17: case 18:
				if ($session['user']['turns']>0){
					output("You are touring one of the cells with one of the Deputies when he decides to show you how hard it is to open the cells.");
					output("You find yourself trapped in the cell as he fumbles with the keys trying to open the door.  He apologizes and gives you a gem if you don't tell anyone.");
					output("`n`nYou `@spend a turn`2 but `%gain a gem`2.");
					$session['user']['gems']++;
					$session['user']['turns']--;
					debuglog("gained a gem and lost a turn by researching at the Jail");
				}else{
					output("You try to talk to the deputy about how to save the kingdom.  He invites you to sit at his desk and he can give you `%a gem`2.");
					output("`n`nUnfortunately, you just don't have the time to do it.");
				}
			break;
			case 19: case 20:
				output("You walk by one of the cells and feel a hand grip your wrist.  Attempting to wrench yourself free, the prisoner pleads with you.");
				output("`4'PLEASE! Something's coming and it will kill us all! Help me get out of here!'`2 he says. You pull your wrist away and try to dismiss the palpable fear.");
				output("`n`nYou stumble and barely catch yourself.  You're physically drained.  You `\$lose all your hitpoints except one`2.");
				$session['user']['hitpoints']=1;
				debuglog("lost all hitpoints except one by researching at the Jail");
			break;
			case 21: case 22:
				output("`@'Okay everyone, no more tours. I have to close things up for a little while,'`2 reports the sheriff.");
				if (get_module_pref("researches")<get_module_setting("research")){
					increment_module_pref("researches",1);
					output("`n`nYou spend another research turn leaving the Jail.");
					debuglog("lost another research turn by researching at the Jail");
				}
				blocknav("runmodule.php?module=$jail");
			break;
			case 23: case 24:
				output("The sheriff asks to see your weapon so you hand it over to him. `@'Yes, this is a nice %s`@.'`2`n`n",$session['user']['weapon']);
				if ($session['user']['gems']>=3 && $session['user']['weapon']!="`@Lawful Sword`0" && e_rand(1,3)==1){
					output("`@'I can offer you an ever nicer weapon here,'`2 he says as he pulls out a `@Lawful Sword`2 and shows it to you.");
					output("`@'It's yours for `%3 gems`@. Are you interested?'");
					addnav("Purchase the Lawful Sword","runmodule.php?module=dragoneggs&op=police23");
				}else{
					output("Worried that he is going to try to 'tax' your weapon, you take it back and end this research.");
				}
			break;
			case 25: case 26:
				output("Suddenly, an inmate escapes and grabs your neck!");
				set_module_pref("monster",17);
				addnav("Fight the Crazed Inmate","runmodule.php?module=dragoneggs&op=attack");
				blocknav("village.php");
				blocknav("runmodule.php?module=$jail");
			break;
			case 27: case 28:
				if (get_module_pref("dragoneggs","dragoneggpoints")>0){
					output("The Sheriff informs you of the `^Dragon Egg Reward Program`2. `@'We have a new program that we're instituting on a case-by-case basis. Since you have evidence of destorying a dragon egg, we're willing to offer you `^500 gold`@ for your `&Dragon Egg Point`@. Are you interested?'");
					addnav("Sell Dragon Egg Point","runmodule.php?module=dragoneggs&op=police25");
				}else{
					output("The Sheriff explains that there's a `^Dragon Egg Reward Program`2 and that you're more than welcome to discuss the program when you have a `&Dragon Egg Point`2 for exchange.");
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
	addnav("Return to the Jail","runmodule.php?module=$jail");
	villagenav();
}
?>