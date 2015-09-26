<?php
function dragoneggs_gypsy(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Gypsy Seer's Graveyard");
	output("`c`b`3Gypsy Seer's Graveyard`b`c`5`n");
	$open=get_module_setting("gypsyopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("gypsymin") && get_module_setting("gypsylodge")>0 && get_module_pref("gypsyaccess")==0){
		output("You don't have enough `@Green Dragon Kills`5 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("gypsymin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`5 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("gypsylodge")>0 && get_module_pref("gypsyaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`5You're out of research turns for today.");
	}else{
		output("`5You decide to look for Dragon Eggs at the Gypsy Seer's Tent.  You step into the back yard and find a graveyard!`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(25){
			case 1: case 2: 
				$chance=e_rand(1,11);
				$level=$session['user']['level'];
				output("Finding something very peculiar about a tombstone, you decide to dig around the base of it.");
				if (($level>10 && $chance<=3) || ($level<=10 && $chance<=2)){
					$dks=$session['user']['dragonkills'];
					increment_module_pref("researches",-1);
					output("Soon enough, you're finding salt and dust.  This was the tombstone of a dragon sympathizer! You're getting closer to an answer!");
					output("`n`nYou `%find a gem`5 and feel invigorated to research another location.");
					$session['user']['gems']++;
					debuglog("found a gem and a free research while researching dragon eggs at the Gypsy Seer's Tent.");
					addnav("Research");
					dragoneggs_navs();
					blocknav("village.php");
					blocknav("gypsy.php");
				}else{
					output("`n`nThere's nothing of value here.  It's a bit disappointing, but you hope to find something at one of the other tombstones.");
				}
			break;
			case 3: case 4:
				output("When you enter a crypt, you notice the name `@Waterhouse`5 above the eves.  When you see some artwork on the walls you feel inspired.");
				output("`n`nYou `@gain 3 turns`5.");
				$session['user']['turns']+=3;
				debuglog("gained 3 turns while researching dragon eggs at the Gypsy Seer's Tent.");
			break;
			case 5: case 6:
				$chance=e_rand(1,7);
				output("You walk past a grave and feel a shiver into your spine.  Was that your name on the tombstone?`n`n");
				if (($session['user']['level']>7 && $chance<4) || ($session['user']['level']<=7 && $chance<5)){
					output("You look more closely and realize that it's just someone with a similar name.");
				}else{
					output("You stoop down and see your name on the tombstone.  It's you!");
					output("`n`n`c`)`b%s`b`nRest in Peace`c`5`n",$session['user']['name']);
					output("You stumble back in horror and hit your head against another tombstone and pass out.");
					output("`n`nYou `\$lose all hitpoints except one`5.");
					$rand=e_rand(1,3);
					if (($session['user']['level']>4 && $session['user']['maxhitpoints']>$session['user']['level']*11&&$rand==1)||($session['user']['turns']>0)){
						$max=0;
						$turn=0;
						if ($session['user']['level']>4 && $session['user']['maxhitpoints']>$session['user']['level']*11 && $rand==1){
							output("You `\$lose a permanent hitpoint`5.");
							$session['user']['maxhitpoints']--;
							$max=1;
						}
						if ($session['user']['turns']>0){
							output("You `@lose a turn`5.");
							$session['user']['turns']--;
							$turn=1;
						}
						if ($max==1 && $turn==1) debuglog ("lost 1 permanent hitpoint, 1 turn, and all hitpoints except 1 while researching in the Gypsy Seer's Tent.");
						elseif ($turn==1) debuglog ("lost  1 turn and all hitpoints except 1 while researching in the Gypsy Seer's Tent.");
						else debuglog ("lost 1 permanent hitpoint and all hitpoints except 1 while researching in the Gypsy Seer's Tent.");
					}else debuglog ("lost all hitpoints except 1 while researching in the Gypsy Seer's Tent.");
					$session['user']['hitpoints']=1;
				}
			break;
			case 7: case 8:
				if ($session['user']['turns']>0){
					output("You look around in a small mausoleum and notice something strange in the corner. As you go to examine it, you hear the door to the mausoleum slam shut!");
					output("`n`nYou're trapped! You spend a turn scratching at the door desperately hoping someone will let you out.");
					$session['user']['turns']--;
					debuglog("lost a turn while researching dragon eggs at the Gypsy Seer's Tent.");
				}else{
					output("You look into a mausoleum and notice the door hinge is a little rusty. You look more closely and a rat jumps out of an alcove and scratches your face.");
					output("`n`nYou `&lose two charm`5 from the scratch.");
					$session['user']['charm']-=2;
					debuglog("lost 2 charm while researching dragon eggs at the Gypsy Seer's Tent.");
				}
			break;
			case 9: case 10:
				output("When you hear a growl behind you, a bell tolls in the distance.  Is the graveyard alive??");
				output("You look for something to reassure you that you're safe.  On a tombstone, you see a crucifix.  You grab for it...`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>4 && $chance<=4) || ($level<=4 && $chance<=2)){
					output("You feel much better now that you have this amazing `^gold`5 cross.  You eventually sell it for `^250 gold`5 which makes you feel even safer.");
					$session['user']['gold']+=250;
					debuglog("gained 250 gold while researching dragon eggs at the Gypsy Seer's Tent.");
				}else{
					output("Stealing from the dead?? A `icurse`i upon you!");
					if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their curse by 5 rounds by researching at the Gypsy Seer's Tent.");
					}else{
						apply_buff('blesscurse',
							array("name"=>translate_inline("Cursed"),
								"rounds"=>15,
								"survivenewday"=>1,
								"wearoff"=>translate_inline("The burst of energy passes."),
								"atkmod"=>0.8,
								"defmod"=>0.9,
								"roundmsg"=>translate_inline("Dark Energy flows through you!"),
							)
						);
						debuglog("received a curse by researching at the Gypsy Seer's Tent.");
					}
				}
			break;
			case 11: case 12: case 13: case 14:
				output("You find a grave that's freshly covered.  You go over and see that it's some strange monster!");
				output("`n`nYou think you see an arm twitch so you run over and beat it with a stick.  Excellent work!");
				$mult=e_rand(5,10);
				$expgain =$session['user']['level']*$mult+($session['user']['dragonkills']*3);
				$session['user']['experience']+=$expgain;
				output("`n`nYou `#gain %s experience`5!",$expgain);
				debuglog("gained $expgain experience by beating an almost-dead corpse in the Gypsy Seer's Tent.");
			break;
			case 15: case 16:
				output("You come upon a dark figure kneeling before a gravestone.  Trying not to disturb him, you try to walk by.");
				output("`n`nIt's hard not to glance at him though... when you realize it's a `\$Vampire`5 feasting on someone recently deceased!");
				output("`n`nAppalled, you attack!");
				addnav("Fight `\$Vampire","runmodule.php?module=dragoneggs&op=gypsy15");
				blocknav("gypsy.php");
				blocknav("village.php");
			break;
			case 17: case 18: case 19: case 20:
				output("You notice something strange stirring in the ground.  You research a little closer and realize it's... it's...");
				output("`n`n`\$A ZOMBIE!!!");
				addnav("Fight the `\$Zombie","runmodule.php?module=dragoneggs&op=attack");
				set_module_pref("monster",6);
				blocknav("gypsy.php");
				blocknav("village.php");
			break;
			case 21: case 22:
				output("One of the groundskeepers sees you and starts talking about how there was a gruesome incident in the graveyard the other night with a corpse that was dismembered by some dog that had gotten into the grave and was chewing on the arm...");
				output("`n`nAfter awhile, you block out what he's saying to you.`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>4 && $chance<=4) || ($level<=4 && $chance<=2) && $session['user']['turns']>0){
					$session['user']['turns']--;
					$session['user']['gems']++;
					output("Eventually, you start looking down at the ground and see something shiny.  You find a `%gem`5 but spend a `@turn`5.");
					debuglog("gained a gem and lost a turn while researching in the Gypsy Seer's Tent.");
				}else{
					output("You decide to run to the Inn to escape.");
					blocknav("gypsy.php");
					blocknav("village.php");
					$session['user']['location'] = getsetting("villagename", LOCATION_FIELDS);
					if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
						$innname=getsetting("innname", LOCATION_INN);
					}else{
						$innname=translate_inline("The Boar's Head Inn");
					}
					addnav(array("Return to %s",$innname),"inn.php");
				}
			break;
			case 23: case 24:
				output("The groundskeeper starts to tell you about a new cult that's been gaining followers.");
				output("You learn that they are going to start recruiting in the Forest soon. You explain that you can stop them, but you need more gems.  He hands you one.");
				$session['user']['gems']++;
				output("`n`nYou `%gain a a gem`5.");
				debuglog("gained a gem while researching in the Gypsy Seer's Tent.");
			break;
			case 25: case 26: case 27: case 28:
				output("You see a man shoveling with all his might and he introduces himself as `)Graveyard Greg`5.");
				output("`n`nYou explain your worries about that horrible `@Green Dragon`5.`n`n");
				$exp=e_rand(35,60)*$session['user']['level'];
				if ($session['user']['experience']>=$exp){
					output("`#'Tell you what, you tell me about these monsters and I'll fight by your side for a spell,'`) Greg`5 explains.");
					output("`n`nYou'll need to give him `#%s experience`5 and then he'll fight by your side.",$exp);
					addnav("Teach Greg","runmodule.php?module=dragoneggs&op=gypsy25&op2=$exp");
				}else{
					output("He reassures you that he'll be able to keep up with digging graves as long as people keep dieing.");
					output("Why don't you find that very comforting?");
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
	addnav("Return to the Gypsy Seer's Tent","gypsy.php");
	villagenav();
}
?>