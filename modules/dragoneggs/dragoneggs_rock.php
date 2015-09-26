<?php
function dragoneggs_rock(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("The Veteran's Club");
	output("`c`b`2The Veteran's Club`b`c`4`n`n");
	$open=get_module_setting("rockopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("rockmin") && get_module_setting("rocklodge")>0 && get_module_pref("rockaccess")==0){
		output("You don't have enough `@Green Dragon Kills`4 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("rockmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`4 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("rocklodge")>0 && get_module_pref("rockaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`4You're out of research turns for today.");
	}else{
		output("`4You decide to look for Dragon Eggs at the Curious Looking Rock...");
		if (is_module_active("sheldon")) output("and notice the words `QThe Sheldon Gang`4 spray-painted on one side of the rock.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(5){
			case 1: case 2: case 3: case 4:
				output("You come across a stray dog.");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>12 && $chance<=3) || ($level<=12 && $chance<=2)){
					if ($session['bufflist']['ally']['type']=="spotstray"){
						output("Your ally `5Spot the Stray`4 starts barking uncontrollably and the dog runs away.");
					}else{
						output("Luckily, you find a piece of food from an earlier meal in your pocket and offer it to the dog. He comes over and eagerly accepts it.");
						output("`n`nIt looks like you've made a new friend! His name is `5Spot the Stray`4.");
						apply_buff('ally',array(
							"name"=>translate_inline("`5Spot the Stray"),
							"rounds"=>-1,
							"wearoff"=>translate_inline("`5Spot's stomach grumbles and he runs off looking for food."),
							"atkmod"=>1.1,
							"type"=>"spotstray",
						));
						if (is_module_active("dlibrary")){
							if (get_module_setting("ally11","dlibrary")==0){
								set_module_setting("ally11",1,"dlibrary");
								addnews("%s`^ was the first person to meet `5Spot the Stray`^ at the Curious Looking Rock.",$session['user']['name']);
							}
						}
						debuglog("gained the help of ally Spot the Stray by researching at the Curious Looking Rock.");
					}
				}else{
					output("He doesn't seem to trust you and runs away.");
				}
			break;
			case 5: case 6:
				output("You see a rusty lockbox.  Will you take a chance and open it?");
				addnav("Open the Box","runmodule.php?module=dragoneggs&op=rock5");
			break;
			case 7: case 8:
				output("You see a strange man standing by the rock.  He beckons you over.`n`n");
				output("`^'I can teach you... teach you many many things.");
				if ($session['user']['turns']>2){
					output("Will you spend `@3 turns`^ with me so I can try to teach you?'");
					addnav("Learn from the Stranger","runmodule.php?module=dragoneggs&op=rock7");
				}else{
					output("However, you don't have the time to work with me.  That's unfortunate.'");
					output("`4He leaves without another word.");
				}
			break;
			case 9: case 10:
				output("You find yourself standing next to this strange rock and really want to lift it for some reason.");
				if ($session['user']['turns']>0){
					output("You `@spend 1 turn`4 trying to lift the rock.");
					$session['user']['turns']--;
					debuglog("lost 1 turn while researching dragon eggs at the Curious Looking Rock.");
				}else{
					if (isset($session['bufflist']['backpain'])) {
						$session['bufflist']['backpain']['rounds'] += 5;
						output("Oh No!! You've made your back even worse for another `^5 rounds`4!");
					}else{
						output("You strain your back... and it's not going to heal up by tomorrow.");
						apply_buff('backpain',array(
							"name"=>translate_inline("`4Back Pain"),
							"rounds"=>10,
							"wearoff"=>translate_inline("`4Your back heals."),
							"atkmod"=>.96,
							"survivenewday"=>1,
						));
					}
					debuglog("received a back pain buff while researching dragon eggs at the Curious Looking Rock.");
				}
			break;
			case 11: case 12:
				$member=0;
				if (is_module_active("sheldon")) if (get_module_pref("member","sheldon")>0) $member=1;
				if ($member==0){
					output("You are bushwacked by the Sheldon Gang!!`n`n");
					$level=$session['user']['level'];
					$chance=e_rand(1,5);
					if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
						output("Luckily, you fight them off and escape.");
					}else{
						if ($session['user']['gems']>=10 && e_rand(1,3)==1){
							$gems=e_rand(1,round($session['user']['gems']/10));
							if ($gems>4)$gems=e_rand(2,4);
							output("They steal some of your gems and run off greedily... `%%s %s`4 to be precise!",$gems,translate_inline($gems>1?"gems":"gem"));
							$session['user']['gems']-=$gems;
							debuglog("lost $gems gems to the Sheldon Gang while researching dragon eggs at the Curious Looking Rock.");
						}elseif ($session['user']['maxhitpoints']>$session['user']['level']*12 && e_rand(1,3)==1){
							output("It's a nasty beating and you `\$lose all your hitpoints except one`4 but even worse you `\$lose a permanent hitpoint`4.");
							$session['user']['maxhitpoints']--;
							$session['user']['hitpoints']=1;
							debuglog("lost all hitpoints except one and a permanent hitpoint to the Sheldon Gang while researching dragon eggs at the Curious Looking Rock.");
						}elseif ($session['user']['turns']>=2){
							$session['user']['turns']-=2;
							output("It's a bad day for you, isn't it? You `@lose two turns`4 running away.");
							debuglog("lost 2 turns to the Sheldon Gang while researching dragon eggs at the Curious Looking Rock.");
						}else{
							if (isset($session['bufflist']['beating'])) {
								$session['bufflist']['beating']['rounds'] += 12;
							}else{
								apply_buff('beating',array(
									"name"=>translate_inline("`4Beat Up"),
									"rounds"=>25,
									"wearoff"=>translate_inline("`4Your wounds heal."),
									"atkmod"=>.96,
									"survivenewday"=>1,
								));
							}
							output("Oh, the beating they give you! that's going to take a while to heal. It's going to hurt tomorrow, that's for sure.");
							debuglog("received a beating buff while researching dragon eggs at the Curious Looking Rock.");
						}
					}
				}else{
					output("The Sheldon Gang is about to beat you to a pulp when you show them the scar on your arm.");
					output("`n`n`#'%s one of us, boys... leave %s alone,'`4 says the leader.  You wipe your brow at how lucky you are to have avoided that beating.",translate_inline($session['user']['sex']?"She's":"He's"),translate_inline($session['user']['sex']?"her":"him"));
					debuglog("avoided getting a beaten by the Sheldon Gang while researching dragon eggs at the Curious Looking Rock.");
				}
			break;
			case 13: case 14:
				output("You see a Sheldon Gang member sleeping soundly under a tree.  In his arms he holds a very high quality weapon.");
				output("`n`nPerhaps you can... 'borrow' it?  Would you like to try?");
				addnav("'Borrow' the Weapon","runmodule.php?module=dragoneggs&op=rock13");
			break;
			case 15: case 16:
				output("You see one of the Sheldon Gang stumbling towards you carrying a bottle of ale.");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
					output("He shares his ale with you!");
					if (is_module_active("drinks")) $drunk = round(get_module_pref('drunkeness','drinks')/10,0);
					else $drunk=e_rand(7,9);
					if ($drunk>8 && is_module_active("drinks")==0){
						debuglog("received an ale from a Sheldon Gang member but dropped it while researching by the Curious Looking Rock.");
						output("You spill the drink. That's alcohol abuse!");
						apply_buff('buzz',array(
							"name"=>translate_inline("Alcohol Abuse"),
							"rounds"=>2,
							"atkmod"=>.99,
							"roundmsg"=>translate_inline("You regret abusing that alcohol."),
						));
					}elseif ($drunk > 8){
						output("You've had too much already though, and you decline.");
					}else{
						output("`1You chug down the ale!`n");
						switch(e_rand(1,3)){
							case 1: case 2:
								output("`1You feel healthy!");
								$session['user']['hitpoints']+=round($session['user']['maxhitpoints']*.1,0);
								debuglog("received an ale from a Sheldon Gang member and gained 10% hitpoints and Ale Buff while researching by the Curious Looking Rock.");
							break;
							case 3:
								output("`&You feel vigorous!");
								$session['user']['turns']++;
								debuglog("received an ale from a Sheldon Gang and gained a turn and Ale Buff it while researching by the Curious Looking Rock.");
							break;
						}
						apply_buff('buzz',array(
							"name"=>translate_inline("`#Buzz"),
							"rounds"=>10,
							"wearoff"=>translate_inline("Your buzz fades."),
							"atkmod"=>1.25,
							"roundmsg"=>translate_inline("You've got a nice buzz going."),
							"activate"=>"offense"
						));
						if (is_module_active("drinks")){
							increment_module_pref('drunkeness',33,'drinks');
							$drunk = round(get_module_pref('drunkeness','drinks')/10,0);
							$drunkenness = array(
								-1=>translate_inline("stone cold sober"),
								0=>translate_inline("quite sober"),
								1=>translate_inline("barely buzzed"),
								2=>translate_inline("pleasantly buzzed"),
								3=>translate_inline("almost drunk"),
								4=>translate_inline("barely drunk"),
								5=>translate_inline("solidly drunk"),
								6=>translate_inline("sloshed"),
								7=>translate_inline("hammered"),
								8=>translate_inline("really hammered"),
								9=>translate_inline("almost unconscious")
							);
							output("`n`n`1You now feel %s.",$drunkenness[$drunk]);
						}
					}
				}else{
					output("He stumbles and poors the ale all over you and you growl at him.  He moves on and you suddenly realize he picked your pocked!");
					if ($session['user']['gold']>=350){
						output("He stole `^350 gold`4 from you!");
						$session['user']['gold']-=350;
						debuglog("lost 350 gold to a pickpocket while researching dragon eggs at the Curious Looking Rock.");
					}else{
						output("He stole all your money!");
						$session['user']['gold']=0;
						debuglog("lost all his money to a pickpocket while researching dragon eggs at the Curious Looking Rock.");
					}
				}
			break;
			case 17: case 18:
				output("You hear some voices and hide behind the Curious Looking Rock.  It's a couple of gang members from the Sheldon Gang.`n`n");
				$chance=e_rand(1,7);
				if (($session['user']['level']>=7 && $chance<4) || ($session['user']['level']<7 && $chance<3)){
					output("They never notice you and you hear them talking about stolen loot.  You see them drop some gems and you wait until they leave.");
					output("`n`nYou `%gain a gem`4.");
					$session['user']['gems']++;
					debuglog("gained a gem while researching dragon eggs at the Curious Looking Rock.");
				}else{
					output("They walk around the Curious Looking Rock and see you squatting down trying to eavesdrop on them!");
					output("`n`nThey beat you up and leave you for dead.");
					$session['user']['hitpoints']=1;
					debuglog("lost all hitpoints except one while researching dragon eggs at the Curious Looking Rock.");
				}
			break;
			case 19: case 20:
				output("You find a still brewing up some high-potency ale.");
				output("After looking around for a while, you realize it's the Sheldon Gang's home brew!");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
					output("`n`nTime to leave... you sneak away quietly.");
				}else{
					output("Uh oh... the owner's didn't leave!");
					output("`n`nBefore you have a chance to react, you're surrounded and receiving the beating of your life.");
					output("`n`nThey steal all your money and leave you with just `\$1 hitpoint`4.");
					$session['user']['hitpoints']=1;
					$session['user']['gold']=0;
					debuglog("lost all gold and all hitpoints except one while researching dragon eggs at the Curious Looking Rock.");
				}
			break;
			case 21: case 22: case 23: case 24: 
				output("You see a dragon's egg and get ready to destroy it.  However, you don't notice a guardian of the egg...");
				output("A really horrid floating bluish creature called a Blupe attacks you!");
				addnav("`0Attack the `\$Blupe`0!","runmodule.php?module=dragoneggs&op=attack");
				set_module_pref("monster",14);
				blocknav("rock.php");
				blocknav("village.php");
			break;
			case 25: case 26: case 27: case 28:
				if (is_module_active("sheldon")){
					if (get_module_pref("member","sheldon")>0) $member=1;
					elseif (get_module_pref("member","sheldon")<0) $member=0;
					else $member=2;
				}else $member=0;
				if ($member==2){
					output("You see a strange man standing by the Curious Looking Rock.  He doesn't look trustworthy at all.");
					addnav("Speak with Him","runmodule.php?module=dragoneggs&op=rock25");
				}elseif ($member==1){
					output("One of the members of the Sheldon Gang recognizes you and explains that they had a successful raid on a very nice mansion in the village.");
					output("He hands you an envelope stuffed with bills and says `Q'Here you go, this is your cut.'`4");
					$gold=e_rand(150,250);
					output("`n`nYou count out `^%s gold`4 and smile at your good fortune.",$gold);
					$session['user']['gold']+=$gold;
					debuglog("received $gold gold for being a Sheldon Gang member while researching dragon eggs at the Curious Looking Rock.");
				}else{
					output("You notice something shiny on the ground.");
					if (e_rand(1,4)==1){
						output("Oh man! It's just a `^gold piece`4.");
						$session['user']['gold']++;
						debuglog("found a gold piece while researching dragon eggs at the Curious Looking Rock.");
					}else{
						output("You find a `%gem`4!");
						$session['user']['gems']++;
						debuglog("found a gem while researching dragon eggs at the Curious Looking Rock.");
					}
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
	addnav("Return to the Curious Looking Rock","rock.php");
	villagenav();
}
?>