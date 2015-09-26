<?php
function dragoneggs_animal(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Merick's Stables");
	output("`c`b`^Merick's Stables`b`c`7`n");
	//This will fix their current location just in case they are being transported to the capital city
	if (is_module_active("cities") && $session['user']['location']!= getsetting("villagename", LOCATION_FIELDS)){
		$session['user']['location'] = getsetting("villagename", LOCATION_FIELDS);
	}
	$open=get_module_setting("animalopen");	
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("animalmin") && get_module_setting("animallodge")>0 && get_module_pref("animalaccess")==0){
		output("You don't have enough `@Green Dragon Kills`7 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("animalmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("animallodge")>0 && get_module_pref("animalaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`7You're out of research turns for today.");
	}else{
		output("`7You decide to look for Dragon Eggs at some caves in the back of Merick's Stables.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		//switch($case){
		switch(25){
			case 1: case 2: 
				output("You find the remains of a recently-deceased spelunker.`n`n");
				$chance=0;
				if (e_rand(1,3)==1) $chance++;
				if (($session['user']['level']>10 && e_rand(1,3)==1) || ($session['user']['level']<11 && e_rand(1,4)==1)) $chance++;
				if ($chance==2){
					if ($session['user']['weapon']!="Heavy Pick Axe" && e_rand(1,3)==1){
						output("You find a very heavy Pick Axe; which looks to be even more powerful than your %s`7.",$session['user']['weapon']);
						$session['user']['weapon']="Heavy Pick Axe";
						$session['user']['weapondmg']+=2;
						$session['user']['attack']+=2;
						debuglog("received a Heavy Pick Axe (2 higher weapon than their previous) by researching at Merick's Stables.");
					}else{
						output("You find a nice piece of colored cloth that you add to your armor.  It makes you look more charming and you `&Gain 1 Charm`7.");
						$session['user']['charm']++;
						debuglog("gained a charm while researching dragon eggs at Merick's Stables.");
					}
				}elseif ($chance==1){
					output("You look closely and it appears as if some creature has eaten the climber's brain. You turn and throw up with your stomach churning.");
					if (isset($session['bufflist']['weakstomach'])) {
						$session['bufflist']['weakstomach']['rounds'] += 5;
						debuglog("increased the weakstomach buff by 5 rounds while researching dragon eggs at Merick's Stables.");
					}else{
						apply_buff('weakstomach',
							array("name"=>translate_inline("Weak Stomach"),
								"rounds"=>5,
								"wearoff"=>translate_inline("Your stomach starts to feel better."),
								"atkmod"=>0.9,
								"roundmsg"=>translate_inline("Your stomach still feels queezy."),
							)
						);
						debuglog("received the weakstomach buff while researching dragon eggs at Merick's Stables.");
					}
				}else{
					output("You look closely and you hear some strange sounds from the stomach.  Suddenly, the abdomen splits open and a creature attacks you!");
					blocknav("stables.php");
					blocknav("village.php");
					addnav("Fight the `\$Gastropian","runmodule.php?module=dragoneggs&op=attack");
					set_module_pref("monster",10);
				}
			break;
			case 3: case 4:
				output("You find a strange book.  Will you read it?");
				addnav("Read the Book","runmodule.php?module=dragoneggs&op=animal3");
			break;
			case 5: case 6:
				output("You step into the caves and hear a bone break under your feet.  You look more closely and realize it was a shaman's stick.  This is a very bad omen.  You've been `iCursed`i!");
				if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
					$session['bufflist']['blesscurse']['rounds'] += 5;
					debuglog("increased curse buff by 5 turns while researching dragon eggs at Merick's Stables.");
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
					debuglog("was cursed while researching dragon eggs at Merick's Stables.");
				}
			break;
			case 7: case 8:
				output("BATS!!!`n`n");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
					output("You dodge the bats and find yourself standing outside of Merick's Stables.");
					blocknav("stables.php");
				}else{
					output("They're in your hair! EWWW!!!!");
					if (isset($session['bufflist']['hairbat'])) {
						$session['bufflist']['hairbat']['rounds'] += 10;
					}else{
						apply_buff('hairbat',
							array("name"=>translate_inline("Hair Bats"),
								"rounds"=>10,
								"wearoff"=>translate_inline("The Bats fly off."),
								"atkmod"=>0.9,
								"roundmsg"=>translate_inline("The bats distract you."),
							)
						);
					}
					debuglog("got bats in their hair while researching dragon eggs at Merick's Stables.");
				}
			break;
			case 9: case 10:
				if ($session['user']['turns']>0){
					output("You find yourself deep into the cave when you hear some rocks falling.");
					output("`n`nYour exit is blocked! You need to spend a turn looking for the exit.");
					$session['user']['turns']--;
					debuglog("lost a turn while researching dragon eggs at Merick's Stables.");
				}else{
					output("You don't find anything of value.");
				}
			break;
			case 11: case 12:
				output("You find yourself falling into a small cavern!");
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if (($level>5 && $chance<=3) || ($level<=5 && $chance<=2)){
					output("Luckily, you land safely and climb out without getting hurt.");
				}else{
					output("You inhale a lung-full of a poisonous gas.");
					if ($session['user']['maxhitpoints']>$session['user']['level']*11+4){
						$session['user']['maxhitpoints']--;
						output("You feel your hitpoints fall... You've `\$lost a permanent hitpoint`7!!!");
						debuglog("lost a permanent hitpoint choking in a small cavern researching dragon eggs at Merick's Stables.");
					}else{
						$session['user']['hitpoints']=1;
						if ($session['user']['gems']==0){
							output("You lose all `\$hitpoints except 1`7.");
							debuglog("lost all hitpoints except 1 researching dragon eggs at Merick's Stables.");
						}else{
							output("You lose all `\$hitpoints except 1`7 and `%lose 1 gem`7.");
							$session['user']['gems']--;
							debuglog("lost all hitpoints except 1 and 1 gem researching dragon eggs at Merick's Stables.");
						}
					}
				}
			break;
			case 13: case 14:
				if (get_module_pref("lantern")==0){
					output("You find an old antique lantern. It doesn't work, but if you can find somewhere to sell it, it might be worth a lot of money!");
					set_module_pref("lantern",1);
					debuglog("found a lantern while researching dragon eggs at Merick's Stables.");
				}else{
					output("You don't find anything of value.");
				}
			break;
			case 15: case 16:
				$light=0;
				output("You suddenly find yourself in a very dark part of the cave.");
				if (is_module_active("ruinworld2")){
					$allprefs=unserialize(get_module_pref('allprefs','ruinworld2'));
					if ($allprefs['haslight']==1) $light=1;
				}
				if (get_module_pref("lantern")>0) $light=2;
				if ($light>0){
					if ($light==1) output("You pull out your torch from the Dinosaur World and try to light it.");
					else output("You pull out your lantern that you found earlier and try to light it.");
					output("After a little trying, you get it lit.");
					$gems=e_rand(1,2);
					if ($gems==2 && e_rand(1,5)!=1) $gems=1;
					output("You see a something shiny on the ground. You `%gain %s %s`7!!",$gems,translate_inline($gems>1?"gems":"gem"));
					$session['user']['gems']+=$gems;
					debuglog("found $gems gems while researching dragon eggs at Merick's Stables.");
				}else{
					output("Not having a light source, you stumble!");
					$chance=e_rand(1,9);
					$level=$session['user']['level'];
					if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
						output("Luck is with you though... you catch yourself before you suffer any serious injuries.");
					}else{
						if ($session['bufflist']['fracture']['rounds']>50) {
							output("Oh no! You've broken BOTH of your wrists.  Oh, this is bad.  They're not going to heal very quickly, either.");
							apply_buff('fracture',array(
								"name"=>translate_inline("2 Broken Wrists"),
								"rounds"=>75,
								"wearoff"=>translate_inline("Your fractures finally heal."),
								"atkmod"=>0.91,
								"survivenewday"=>1,
							));
							debuglog("received the '2 broken wrists' buff while researching dragon eggs at Merick's Stables.");
						}else{
							output("Oh no! You break your wrist.  Oh, this is bad.  It's not going to heal very quickly, either.");
							apply_buff('fracture',array(
								"name"=>translate_inline("Broken Wrist"),
								"rounds"=>100,
								"wearoff"=>translate_inline("Your fracture finally heals."),
								"atkmod"=>0.95,
								"survivenewday"=>1,
							));
							debuglog("received the 'broke their wrist' buff while researching dragon eggs at Merick's Stables.");
						}
					}
				}
			break;
			case 17: case 18: case 19: case 20:
				output("You take a break and lean on one of the stalagmites.  However, you feel the ground move.  Earthquake??");
				output("No! It's something else... a creature from the dark and it starts to attack you.");
				blocknav("stables.php");
				blocknav("village.php");
				addnav("Fight the `\$Stalagaryth","runmodule.php?module=dragoneggs&op=attack");
				set_module_pref("monster",11);
			break;
			case 21: case 22: case 23: case 24:
				output("You find yourself in a twisting maze deep in the caves.  This isn't going to be easy to get out of!`n`n");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>7 && $chance<=4) || ($level<=7 && $chance<=3)){
					output("After stumbling around for a while, you find yourself at a strange pool near an exit that leads to the village.  Will you drink from the pool or take the exit?");
					addnav("Drink from the Pool","runmodule.php?module=dragoneggs&op=animal21");
				}else{
					if ($session['user']['turns']>0){
						output("You spend a turn getting out of the caves.");
						$session['user']['turns']--;
						debuglog("lost a turn while lost in the deep caves while researching dragon eggs at Merick's Stables.");
					}else{
						output("You `\$lose all hitpoints except 1`7 after stumbling and falling.");
						$session['user']['hitpoints']=1;
						debuglog("lost all hitpoints except 1 while researching dragon eggs at Merick's Stables.");
					}
				}
				blocknav("stables.php");
			break;
			case 25: case 26: case 27: case 28:
				$chance=e_rand(1,11);
				$level=$session['user']['level'];
				$drinks=0;
				if (is_module_active("drinks")) if(get_module_pref("drunkeness","drinks")<=0) $drinks=1;
				if (($level>5 && $chance<=3) || ($level<=5 && $chance<=2)){
					output("You find yourself face to face with a towering stranger.  He looks down at you, blocking your way.");
					output("You realize either you're going to have to talk with him or turn around and leave.");
					addnav("Chat with the Stranger","runmodule.php?module=dragoneggs&op=animal25");
				}else{
					if ($session['user']['gold']>=500 && $drinks==1){
						blocknav("stables.php");
						blocknav("village.php");
						addnav("Buy a Round","runmodule.php?module=dragoneggs&op=animal25&op2=500");
						addnav("Leave","inn.php");
						if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
							$innname=getsetting("innname", LOCATION_INN);
							$barkeep = getsetting('barkeep','`tCedrik');
							
						}else{
							$innname=translate_inline("The Boar's Head Inn");
							$barkeep =translate_inline("`%Cedrik");
						}
						output("Not finding much of interest, you decide to go to the %s`7 for a drink. The bar is full of happy people. %s`7 strides up to you and asks if you'd like to buy the bar a round of drinks for `^500 gold`7.",$innname,$barkeep);
					}else{
						output("You see a strange shape pass you buy in the caves.  When you turn to see what it was, there's nothing there.");
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
	addnav("Return to Merick's Stables","stables.php");
	villagenav();
}
?>