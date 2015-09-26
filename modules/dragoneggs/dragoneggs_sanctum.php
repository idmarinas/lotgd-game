<?php
function dragoneggs_sanctum(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Order of the Inner Sanctum");
	output("`n`c`b`&Order of the Inner Sanctum`b`c`7");
	//If the player isn't in the right city, move them to it.
	if ($session['user']['location']!= get_module_setting("sanctumloc","sanctum")){
		$session['user']['location'] = get_module_setting("sanctumloc","sanctum");
	}
	if (get_module_pref("member","sanctum")==0){
		output("You start snooping around the building but a finely dressed man comes out of the front door and notices you.");
		output("`n`n`&'Hey!'`7 he yells at you. `&'This is a private building.  Members only.'`7");
		output("`n`n`7Realizing you're not going to figure much out here, you decide to leave.");
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`7You're out of research turns for today.");
	}else{
		output("`7You decide to look for Dragon Eggs at the Inner Sanctum.`n`n");
		increment_module_pref("researches",1);
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		debuglog("used a research turn in the Inner Sanctum.");
		switch($case){
		//switch(23){
			case 1: case 2:
				output("One of the Sanctum brothers talks with you briefly about your plight. He decides to help you and casts a spell.`n`n");
				if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
					output("You are healed!");
					$session['user']['hitpoints']=$session['user']['maxhitpoints'];
					debuglog("was healed to full by researching at the Order of the Inner Sanctum.");
				}else{
					output("You are `iBlessed`i!");
					if ($session['bufflist']['blesscurse']['atkmod']==1.2) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their blessing by 5 rounds by researching at the Order of the Inner Sanctum.");
					}else{
						apply_buff('blesscurse',
							array("name"=>translate_inline("Blessed"),
								"survivenewday"=>1,
								"rounds"=>15,
								"wearoff"=>translate_inline("The burst of energy passes."),
								"atkmod"=>1.2,
								"defmod"=>1.1,
								"roundmsg"=>translate_inline("Energy flows through you!"),
							)
						);
						debuglog("received a blessing by researching at the Order of the Inner Sanctum.");
					}
				}
			break;
			case 3: case 4:
				output("You participate in the Order's monthly ritual.");
				output("`n`nYou see strange, disturbing things. You `\$lose all your hitpoints except one`7 but you `%gain a gem`7.");
				$session['user']['hitpoints']=1;
				$session['user']['gems']++;
				debuglog("received a gem and lost all hitpoints except 1 by researching at the Order of the Inner Sanctum.");
				if ($session['user']['turns']>=2){
					output("If you would like to `@spend two turns`7 longer, you can gain another `%gem`7.  Are you interested?");
					addnav("Spend 2 Turns","runmodule.php?module=dragoneggs&op=sanctum3");
				}
			break;
			case 5: case 6:
				output("You get into an argument with one of the Order members about the power of the `@Green Dragons`7 and think nothing of it.");
				output("`n`nHowever, during the monthly ceremony, you feel the evil eye of the member gazing upon you!");
				output("`n`nYou've been `iCursed`i!");
				if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
					$session['bufflist']['blesscurse']['rounds'] += 5;
					debuglog("increased their curse by 5 rounds by researching at the Order of the Inner Sanctum.");
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
					debuglog("was cursed by researching at the Order of the Inner Sanctum.");
				}
			break;
			case 7: case 8:
				output("You find favor with the finely dressed man.  He nods approvingly at your actions and blesses you.`n`n");
				output("You are `iBlessed`i!");
				if ($session['bufflist']['blesscurse']['atkmod']==1.2) {
					$session['bufflist']['blesscurse']['rounds'] += 5;
					debuglog("increased their blessing by 5 rounds by researching at the Order of the Inner Sanctum.");
				}else{
					apply_buff('blesscurse',
						array("name"=>translate_inline("Blessed"),
							"survivenewday"=>1,
							"rounds"=>15,
							"wearoff"=>translate_inline("The burst of energy passes."),
							"atkmod"=>1.2,
							"defmod"=>1.1,
							"roundmsg"=>translate_inline("Energy flows through you!"),
						)
					);
					debuglog("received a blessing by researching at the Order of the Inner Sanctum.");
				}
			break;
			case 9: case 10:
				output("You are tapped on the shoulder and one of the Order members asks you to step aside to talk to her.");
				output("`n`nYou go into one of the side rooms and talk for a short while.");
				output("`n`n`&'I had a dream last night. You were in it,'`7 she says. `&'I need to give you this or else horrible things will happen.'");
				output("`n`n`7She grabs your hand and forces an envelope into it. You try to say something but she disappears before you can utter a word.");
				output("`n`nYou open the envelope to find `^350 gold`7!");
				$session['user']['gold']+=350;
				debuglog("received 350 gold by researching at the Order of the Inner Sanctum.");
			break;
			case 11: case 12:
				output("You participate in the Order's monthly ritual.`n`n");
				output("You are blasted back by a powerful spell and hit your head.  You're barely conscious and you `\$lose 1/2 your hitpoints`7.`n`n");
				$session['user']['hitpoints']*=.5;
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if ($session['user']['turns']>0 && (($level>8 && $chance<=3) || ($level<=8 && $chance<=2))){
					$expgain =$session['user']['level']*15+($session['user']['dragonkills']*10);
					$session['user']['experience']+=$expgain;
					output("You feel a terrible tentacle grab at you and you stab it.");
					output("`n`nSucces! You gain `#%s experience`7 from the encounter.",$expgain);
					debuglog("lost 1/2 hitpoints but gained $expgain experience by researching at the Order of the Inner Sanctum.");
				}else debuglog("lost 1/2 hitpoints by researching at the Order of the Inner Sanctum.");
			break;
			case 13: case 14:
				output("You participate in the Order's monthly ritual.`n`n");
				output("An egg is presented and they cast a spell to try to destroy it and all the members of the Order cheer!`n`n");
				output("However, the incantation goes wrong and instead summons a Wraith and everyone boos!`n`n");
				output("Then the Wraith attacks you and everyone runs away!");
				set_module_pref("monster",2);
				addnav("Fight the Wraith","runmodule.php?module=dragoneggs&op=attack");
				blocknav("runmodule.php?module=sanctum");
				blocknav("village.php");
			break;
			case 15: case 16:
				output("The finely dressed man approaches you requesting that you pay the monthly dues.");
				output("`n`n`#'I thought there were no dues!'`7 you protest.`n`n");
				output("`&'This month there are,'`7 comes the reply.`n`n");
				output("He asks for `^500 gold `7 or `%2 gems`7.");
				if ($session['user']['gold']+$session['user']['goldinbank']>=500) addnav("Pay 500 gold","runmodule.php?module=dragoneggs&op=sanctum15&op2=1");
				if ($session['user']['gems']>=2) addnav("Pay 2 gems","runmodule.php?module=dragoneggs&op=sanctum15&op2=2");
				addnav("Quit the Order","runmodule.php?module=dragoneggs&op=sanctum15&op2=3");
				blocknav("runmodule.php?module=sanctum");
				blocknav("village.php");
			break;
			case 17: case 18:
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				output("You wander around the halls and then duck into a closed room.");
				output("`n`nAfter searching around for a little while, you find something of interest...`n`n");
				if ((($level>10 && $chance<=3) || ($level<=10 && $chance<=2)) && $session['user']['weapon']!="`&Holy Sword`0"){
					output("It's a `&Holy Sword`7; much nicer than your `^%s`7.  Excellent!",$session['user']['weapon']);
					$session['user']['weapon']="`&Holy Sword`0";
					$session['user']['weapondmg']+=2;
					$session['user']['attack']+=2;
					debuglog("received a Holy Sword (2 higher weapon than their previous) by researching at the Inner Sanctum.");
				}else{
					output("It's a parchment.  You find a `%gem`7 in it.");
					$session['user']['gems']++;
					debuglog("gained a gem by researching at the Order of the Inner Sanctum.");
				}
			break;
			case 19: case 20:
				output("You participate in the Order's monthly ritual.`n`n");
				output("You begin chanting and realize that this is a dragon egg destroying ritual.`n`n");
				if ($session['user']['gems']>=5){
					output("You use `%5 gems`7 to cast a spell to destroy the egg successfully. You gain a `&dragon egg point`7!");
					$session['user']['gems']-=5;
					increment_module_pref("dragoneggs",1,"dragoneggpoints");
					increment_module_pref("dragoneggshof",1,"dragoneggpoints");
					addnews("`7Thanks to the work of %s`7, a dragon egg was destroyed, thereby saving many lives!",$session['user']['name']);
					debuglog("gained a dragoneggpoint by spending 5 gems to cast a spell to destroy the egg while researching dragon eggs at the Order of the Inner Sanctum.");
				}else{
					output("Unfortunately, the key to the spell is lost.  If only you had `%5 gems`7 you could have cast a spell to destroy the egg.");
				}
			break;
			case 21: case 22:
				output("You participate in the Order's monthly ritual.`n`n");
				output("You begin chanting and realize that this is an egg-hatching ritual.`n`n");
				if ($session['user']['gems']>=2){
					output("You may be able to destroy the egg... it will be difficult but if you use `%2 gems`7 you may actually have a chance to cast a spell to destroy it!");
					addnav("Destroy the Dragon Egg","runmodule.php?module=dragoneggs&op=sanctum21&op2=0");
				}else{
					output("You don't have enough gems... if only you had 2 gems there'd be a chance you could cast a spell to destroy the egg.");
				}
			break;
			case 23: case 24:
				output("You decide to do some reading in the `&Order`7's library. One book in particular catches your attention.");
				output("You read it carefully and begin to realize that by reading it you can banish a monster!");
				output("`n`nIn particular, you'll be able to target one of these:");
				output("`n`qWerebeast:  This creature causes a painful death to warriors.  They victims come back 3 days later to fight as minions.");
				output("`n`5Tsoitthian: Perhaps it arose from the swamps.  Perhaps it appeared through more devious means.  It seems like a cross between a lizard and a dreadfish.");
				output("`n`)Crysthose: Silicone based life; unlikely from our world.  Resistant to many forms of magic but particularly vulnerable to projectile weapons.");
				output("`n`1Mytrico: Strange creatures born of the fog. Perhaps the most diffiult creatures to defeat in the dark of night.");
				output("`n`QYthilian: Insect-like creatures that live off the blood of warriors that have been placed in a poisonous coma.");
				output("`n`#Pricole: Thought to initially be harmless cat-like creatures... until one killed 3 guard dogs without receiving a scratch.");
				output("`n`%Aatrithic: One has never been seen in the light, so it is difficult to report on what exactly one looks like.");
				output("`n`@Flaayer: By consuming the memories of its victims, the Flaayer is only growing stronger every day.");
				output("`n`n`7Which monster will you banish?");
				addnav("Banish the `qWerebeast","runmodule.php?module=dragoneggs&op=sanctum23&op2=1");
				addnav("Banish the `5Tsoitthian","runmodule.php?module=dragoneggs&op=sanctum23&op2=2");
				addnav("Banish the `)Crysthose","runmodule.php?module=dragoneggs&op=sanctum23&op2=3");
				addnav("Banish the `1Mytrico","runmodule.php?module=dragoneggs&op=sanctum23&op2=4");
				addnav("Banish the `QYthilian","runmodule.php?module=dragoneggs&op=sanctum23&op2=5");
				addnav("Banish the `#Pricole","runmodule.php?module=dragoneggs&op=sanctum23&op2=6");
				addnav("Banish the `%Aatrithic","runmodule.php?module=dragoneggs&op=sanctum23&op2=7");
				addnav("Banish the `@Flaayer","runmodule.php?module=dragoneggs&op=sanctum23&op2=8");
				blocknav("runmodule.php?module=sanctum");
				blocknav("village.php");
			break;
			case 25: case 26:
				if (get_module_pref("quest1")==0 & (is_module_active("djail")||is_module_active("jail"))){
					output("The finely dressed man gives you a list of locations to visit in order.");
					output("`n`n`&'Go to the following locations before the end of the day and when you return here I will give you a gem,'`7 he tells you.");
					output("`n`n`cThe Jail`nThe MightyE's Weapons`nMerick's Stables`nHealer's Hut`c`n");
					output("`&'I hope you have this because there are no reminders and no notice that you're on the right track until you come back here,'`7 he explains.");
					set_module_pref("quest1",1);
				}else{
					output("You search through the library and find something sparkling.  You `%gain a gem`7!");
					$session['user']['gems']++;
					debuglog("gained a gem by researching at the Order of the Inner Sanctum.");
				}
			break;
			case 27: case 28:
				output("The finely dressed man approaches you.`n`n");
				output("`&'I will give you a gem if you can prove you have intuitive power.'`7 He picks up a dotted cube and drops it out of your eyesight.`n`n");
				output("`&'What number is it?'");
				addnav("1","runmodule.php?module=dragoneggs&op=sanctum27&op2=1");
				addnav("2","runmodule.php?module=dragoneggs&op=sanctum27&op2=2");
				addnav("3","runmodule.php?module=dragoneggs&op=sanctum27&op2=3");
				addnav("4","runmodule.php?module=dragoneggs&op=sanctum27&op2=4");
				addnav("5","runmodule.php?module=dragoneggs&op=sanctum27&op2=5");
				addnav("6","runmodule.php?module=dragoneggs&op=sanctum27&op2=6");
				blocknav("runmodule.php?module=sanctum");
				blocknav("village.php");
			break;
			case 29: case 30: case 31: case 32: case 33: case 34:
				output("You don't find anything of value.");
			break;
			case 35: case 36:
				dragoneggs_case36();
			break;
		}
	}
	if (get_module_pref("member","sanctum")<>0) addnav("Return to the Order","runmodule.php?module=sanctum");
	villagenav();
}
?>