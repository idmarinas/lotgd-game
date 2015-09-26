<?php
function dragoneggs_magic(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$storename=get_module_setting('gsowner','pqgiftshop');
	$header = color_sanitize($storename);
	page_header("%s's Ol' Gifte Shoppe",$header);
	output("`c`b`&Ye Ol' Gifte Shoppe`b`c`7`n`n");
	//If the player isn't in the right city, move them to it.
	if ($session['user']['location']!= get_module_setting("gsloc","pqgiftshop")){
		$session['user']['location'] = get_module_setting("gsloc","pqgiftshop");
	}
	$open=get_module_setting("magicopen");
	$gift=get_module_setting('gsowner','pqgiftshop')."'s Gift Shop";
	if (get_module_pref("quest2")==4){
		output("Having completed `&%s Quest`7 you appear before %s`7 with proof that you've finished the quest.",$gift,$storename);
		output("`n`n%s`7 looks at the paperwork happily and gives you your reward.",$storename);
		addnav("Continue","runmodule.php?module=dragoneggs&op=magic15&op2=4");
		blocknav("village.php");
		blocknav("runmodule.php?module=pqgiftshop");
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("magicmin") && get_module_setting("magiclodge")>0 && get_module_pref("magicaccess")==0){
		output("You don't have enough `@Green Dragon Kills`7 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("magicmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("magiclodge")>0 && get_module_pref("magicaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("You're out of research turns for today.");
	}else{
		output("You decide to look for Dragon Eggs at %s.`n`n",$gift);
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(23){
			case 1: case 2:
				if ($session['user']['hitpoints']>1){
					$gain=e_rand(2,5);
					output("You pick up a glass ball and peer into it...");
					output("`n`nYou feel a shock go through your fingers as you sense the future crushing down upon you.");
					output("`n`nYou `@gain %s turns`7 but `\$lose all hitpoints except 1`7.",$gain);
					$session['user']['turns']+=$gain;
					$session['user']['hitpoints']=1;
					debuglog("lost all hitpoints except 1 and gained $gain turns while researching dragon eggs at Ye Ol' Gifte Shoppe.");
				}elseif ($session['user']['gold']>=1000){
					$lose=e_rand(100,500);
					output("%s`7 offers you some `^magic sand`7 that will give you unlimited power.  All you have to do is pay `^%s gold`7 for it.",$storename,$lose);
					output("`n`nYou don't even hesitate!  You pay the `^%s gold`7.",$lose);
					output("`n`n%s`7 gives you a bag of `^magic sand`7 but despite everything you try to do it turns out to just be `^sand`7.",$storename);
					$session['user']['gold']-=$lose;
					debuglog("lost $lose gold while researching dragon eggs at Ye Ol' Gifte Shoppe.");
				}else{
					output("You feel a little tired from being in the store.  You decide to step out for a little while.");
					blocknav("runmodule.php?module=pqgiftshop");
				}
			break;
			case 3: case 4:
				output("You see a strange locked box and enquire about the price.");
				output("`n`n%s`7 doesn't even look over and says `&'Yeah, that's `^500 gold`&.'`n`n`7",$storename);
				if ($session['user']['gold']>=500){
					addnav("Buy the Box","runmodule.php?module=dragoneggs&op=magic3");
					output("Are you interested?");
				}else{
					output("Not having the money, you decline.");
					debuglog("couldn't afford a box while researching dragon eggs at Ye Ol' Gifte Shoppe.");
				}
			break;
			case 5: case 6:
				output("%s`7 looks at you and starts to panic.  `&'You're the one! I know your face. If you don't leave now, they'll kill everyone!'`7",$storename);
				output("`n`nA bit unsure what she's talking about, you quickly leave the store and hit your shoulder on the door frame on the way out.");
				output("`n`nYou feel unsettled after the encounter.");
				blocknav("runmodule.php?module=pqgiftshop");
				if (isset($session['bufflist']['unsettling'])) {
					$session['bufflist']['unsettling']['rounds'] += 5;
				}else{
					apply_buff('unsettling',array(
						"name"=>translate_inline("Unsettling Feeling"),
						"rounds"=>10,
						"wearoff"=>translate_inline("`&The unsettling feeling fades."),
						"atkmod"=>.98,
						"defmod"=>1.02,
					));
				}
				debuglog("gained an unsettling buff while researching dragon eggs at Ye Ol' Gifte Shoppe.");
			break;
			case 7: case 8:
				$chance=e_rand(1,5);
				//next line from smith.php
				$previous= strpos($session['user']['armor'],"Glowing ")!==false ? 1 : 0;
				if ((($session['user']['level']>8 && $chance<4) || ($session['user']['level']<=8 && $chance<3)) && $previous==0){
					output("You find a very nice pendant and realize it's a protective pendant.  However, %s`7 seems to have overlooked its value.",$storename);
					output("It could probably raise the defense of your %s`7 by 2 points if you buy it.`n`n",$session['user']['armor']);
					$offer=$session['user']['armordef']*50;
					if ($session['user']['gold']<$offer){
						output("You look at the price and see that it's listed at `^%s gold`7.  If only you had enough money you could have gotten an easy armor upgrade!",$offer);
						debuglog("missed a chance to get a cheap upgrade to armor while researching dragon eggs at Ye Ol' Gifte Shoppe.");
					}else{
						output("Knowing that the value of the pendant is several times higher than your proposed offer, you tell %s`7 that you'll pay `^%s gold`7 for this little pendant and your offer is accepted.",$storename,$offer);
						addnav("Pay for Pendant","runmodule.php?module=dragoneggs&op=magic7&op2=$offer");
					}
				}else{
					output("You see a bunch of cheap trinkets.  `#'There's no magic here,'`7 you complain.  %s`7 doesn't even look up.",$storename);
				}
			break;
			case 9: case 10:
				output("`#'What's this?`7' you ask as you pick up a mummified head.");
				output("`n`n`&'DON'T TOUCH THAT!!!'`7 yells %s`7; a little too late.",$storename);
				output("`n`nThe eyes on the mummified head open and the mouth opens.  It bites you!");
				output("`n`nYou are `iCursed`i.");
				if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
					$session['bufflist']['blesscurse']['rounds'] += 5;
					debuglog("increased their curse by 5 rounds by researching at Ye Ol' Gifte Shoppe.");
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
					debuglog("received a curse by researching at Ye Ol' Gifte Shoppe.");
				}
			break;
			case 11: case 12:
				output("You find a book and decide to page through it.  It seems like a book on etiquette.");
				output("`n`nAfter a couple of minutes of perusing the book, you find a rule you'd never heard before; one that you've been violating for a LONG time:`n`n");
				switch(e_rand(1,5)){
					case 1:
						output("`i`cChew With Your Mouth Closed`c`i`n");
					break;
					case 2:
						output("`i`cDo Not Pass Gas at the Dinner Table`c`i`n");
					break;
					case 3:
						output("`i`cDo NOT pick your nose in public and then fling the boogers at your friend and laugh, saying 'You just got Booggered!!'`c`i`n");
					break;
					case 4:
						output("`i`cIf a friend has a huge scab on their forehead, do NOT try to pick it.`c`i`n");
					break;
					case 5:
						output("`i`cGetting a dangerous and transmissible illness and then travelling all around the world is NOT appropriate.`c`i`n");
					break;
				}
				output("You `&Gain 1 Charm`7 by following the new rule.");
				$session['user']['charm']++;
				debuglog("gained 1 charm by researching at Ye Ol' Gifte Shoppe.");
			break;
			case 13: case 14:
				output("`&'Let me tell you something about the `@Green Dragon`&,'`7 says %s.",$storename);
				output("`n`nYou listen intently and nod your head.  She's impressed with your attentiveness and gives you a`% gem`7.");
				$session['user']['gems']++;
				debuglog("gained a gem by researching at Ye Ol' Gifte Shoppe.");
			break;
			case 15: case 16:
				if (get_module_pref("quest2")==0){
					output("`&'Pay attention,'`7 says %s`7, `&'And you will be greatly rewarded. I will not repeat the instructions.'",$storename);
					output("`n`n`7You perk up to the offer. %s`7 continues, `&'Visit the following places in order and give a gem at each one.  Come back to me before the end of the day and I will reward you greatly.'",$storename);
					output("`n`n`7%s`7 lists the locations.",$storename);
					output("`&'Give a `%gem`& to the `\$Healer's Hut`&, then `^Ye Olde Bank`&, and finally to the `!Daily News`&. Complete this before the end of the day and come back to the research area for a reward.'");
					output("`n`n`7It sounds like an easy enough mission!");
					set_module_pref("quest2",1);
				}elseif (get_module_pref("quest2")==5){
					output("You don't find anything of value.");
				}elseif(get_module_pref("quest2")>0){
					output("`&'I told you to go finish my quest. What are you doing here?'`7 asks %s`7.",$storename);
				}
			break;
			case 17: case 18:
				if (get_module_pref("exchange")==0){
					output("`&'I am trying to create a new spell,'`7 explains %s`7, `&'But I need `bDragon Egg Points`b to complete it.'`n`n",$storename);
					if (get_module_pref("dragoneggs","dragoneggpoints")>0){
						output("`&'I can cast a spell that will give you a `\$Permanent hitpoint`& for each of your `bDragon Egg Points`b you give me, up to 3.'");
						addnav("Exchange");
						addnav("1 Dragon Egg Point","runmodule.php?module=dragoneggs&op=magic17&op2=1");
						if (get_module_pref("dragoneggs","dragoneggpoints")>1) addnav("2 Dragon Egg Points","runmodule.php?module=dragoneggs&op=magic17&op2=2");
						if (get_module_pref("dragoneggs","dragoneggpoints")>2) addnav("3 Dragon Egg Points","runmodule.php?module=dragoneggs&op=magic17&op2=3");
						addnav("Leave");
					}else{
						output("You don't have any `&Dragon Egg Points`7 so there's nothing more you can offer.");
					}
				}else{
					output("You inquire about the Spell that %s`7 was creating and ask her if she would like some more `&Dragon Egg Points`7.`n`n",$storename);
					output("`&'I have finished my new spell by obtaining enough `bDragon Egg Points`b.  I may attempt a new spell; but probably not for a while.  If you inspire me by killing a `@Green Dragon`&, I may make you another offer,'`7 explains %s`7.",$storename);
				}
			break;
			case 19: case 20:
				output("`&'I need some gems,'`7 explains %s`7, `&'If you can give me some, I can cast a spell that gives you `@extra turns`&.'",$storename);
				if ($session['user']['gems']>0){
					addnav("Exchange");
					addnav("1 Gem for 1 Turn","runmodule.php?module=dragoneggs&op=magic19&op2=1");
					if ($session['user']['gems']>1) addnav("2 Gems for 3 Turns","runmodule.php?module=dragoneggs&op=magic19&op2=2");
					if ($session['user']['gems']>2) addnav("3 Gems for 5 Turns","runmodule.php?module=dragoneggs&op=magic19&op2=3");
					addnav("Leave");
				}else{
					output("Not having any `%gems`7 to share, you shrug and continue your work.");
					debuglog("didn't find anything due to lack of gems while researching dragon eggs at Ye Ol' Gifte Shoppe.");
				}
			break;
			case 21: case 22:
				output("`&'I'm sorry,'`7 says %s`7, `&'but I need to clean a little here.  You'll have to leave the shop.'",$storename);
				output("`n`n`7You leave, but luckily it doesn't cost you a research turn.");
				increment_module_pref("researches",-1);
				blocknav("runmodule.php?module=pqgiftshop");
			break;
			case 23: case 24: 
				output("You're admiring a taxidermy `qbear`7 when %s`7 looks in horror.",$storename);
				output("`&'That's not a stuffed `qbear`7... that's real!!!'");
				if (is_module_active("lumberyard") || is_module_active("quarry") || is_module_active("metalmine") || is_module_active("oceanquest")) output("`n`nOh no! Not that `qBear`7 AGAIN!!!");
				set_module_pref("monster",19);
				addnav("Attack the `qBear","runmodule.php?module=dragoneggs&op=attack");
				blocknav("runmodule.php?module=pqgiftshop");
				blocknav("village.php");
			break;
			case 25:
				if ($session['user']['gems']>1){
					output("You hit your head on a hanging lamp and you feel your brain groan in protest; two gems falling out of your pouch.  You `%lose 2 gems`7.");
					$session['user']['gems']-=2;
					debuglog("lost 2 gems while researching dragon eggs at Ye Ol' Gifte Shoppe.");
				}elseif ($session['user']['gold']>=1000){
					output("You bump a crystal ball and it crashes to the floor and shatters.  You try to push the shattered remains under the carpet by %s`7 notices.",$storename);
					output("`n`n`&'That will be `^1000 gold`&,' `7she says.");
					output("`n`nYou hand over the `^1000 gold`7 and try to be more careful in the future.");
					debuglog("lost 1000 gold by researching at Ye Ol' Gifte Shoppe.");
				}else{
					$session['user']['gold']++;
					output("You find a gold piece on the floor.");
					debuglog("found a gold piece while researching dragon eggs at Ye Ol' Gifte Shoppe.");
				}
			break;
			case 26: case 27: case 28:
				output("`&'Although a litle expensive, I think I have something you might be interested in,'`7 says %s`7.",$storename);
				output("`&'If you have the money, I can improve your specialty for only `^1000 gold`&. Are you interested?'");
				if ($session['user']['gold']>=1000){
					addnav("Increase Specialty","runmodule.php?module=dragoneggs&op=magic26");
				}else{
					output("`7Not having the money, you shrug and keep looking for something useful.");
					debuglog("Couldn't afford to improve their specialty while researching dragon eggs at Ye Ol' Gifte Shoppe.");
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
	addnav(array("Return to %s's Gift Shop",get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=pqgiftshop");
	villagenav();
}
?>