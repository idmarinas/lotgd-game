<?php
function dragoneggs_inn(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
		$innname=getsetting("innname", LOCATION_INN);
		$barkeep = getsetting('barkeep','`tCedrik');
	}else{
		$innname=translate_inline("The Boar's Head Inn");
		$barkeep =translate_inline("`%Cedrik");
	}
	page_header("%s",$innname);
	rawoutput("<span style='color: #9900FF'>");
	output("`c`b`0%s`b`c`0",$innname);
	$open=get_module_setting("innopen");
	//This will fix their current location just in case they are being transported to the capital city
	if (is_module_active("cities") && $session['user']['location']!= getsetting("villagename", LOCATION_FIELDS)){
		$session['user']['location'] = getsetting("villagename", LOCATION_FIELDS);
	}
	if (get_module_pref("member","sanctum")<>0){
		output("`0The bartender notices that you're trying to research at the bar.");
		output("`%'Now, you know that it's not worth your time researching here.  You need to go to the Inner Sanctum since you're a member there!'");
		output("`n`n`0You act as if you were just testing him as you re-orient yourself.");
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("innmin") && get_module_setting("innlodge")>0 && get_module_pref("innaccess")==0){
		output("You don't have enough `@Green Dragon Kills`0 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("innmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`0 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("innlodge")>0 && get_module_pref("innaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`0You're out of research turns for today.");
	}else{
		output("`0You decide to look for Dragon Eggs at the Inn.`n`n");
		increment_module_pref("researches",1);
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		debuglog("used a research turn in the Boar's Head Inn.");
		switch($case){
		//switch(15){
			case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8:
				if (is_module_active("sanctum")){
					if ($session['user']['dragonkills']<get_module_setting("mindk","sanctum")){
						output("You start chatting with a finely dressed man in the Inn.  He likes you, but says you're too inexperienced to join.");
						output("You ask him what he means, and he shrugs and says he looks forward to seeing you when you've defeated a couple more `@Green Dragons`0.");
					}else{
						if ($session['user']['dragonkills']<2) $cost=2;
						elseif ($session['user']['dragonkills']<10) $cost=4;
						else $cost=5;
						output("You start chatting with a finely dressed man in the Inn. He takes a liking to you and invites you to join the order.");
						output("`&'Unfortunately, membership isn't free.  It will cost `%%s gems`& to get in,'`0 he explains. `&'Are you interested?'",$cost);
						blocknav("inn.php");
						blocknav("village.php");
						if ($session['user']['gems']>=$cost) addnav("Yes, I would like to Join.","runmodule.php?module=dragoneggs&op=inn1&op2=$cost");
						addnav("No Thank You.","runmodule.php?module=dragoneggs&op=inn1&op2=1");
					}
				}else{
					$erand=e_rand(1,5);
					if ($erand==1){
						output("You order a drink but the bartender never notices you.");
					}elseif ($erand<4){
						output("You find a bag and open it.  It has `^250 gold`0!!");
						$session['user']['gold']+=250;
						debuglog("found 250 gold while searching for dragon eggs at the Inn.");
					}else{
						output("You search under the stool and notice something shiny.  It's a `%gem`0!");
						$session['user']['gems']++;
						debuglog("found a gem while searching for dragon eggs at the Inn.");
					}
				}
			break;
			case 9: case 10:
				output("You find yourself under a key location under a window around the Inn and you listen intently to the patrons.`n`n");
				$chance=e_rand(1,7);
				$level=$session['user']['level'];
				if (($level>8 && $chance<=2) || ($level<=8 && $chance<=1)){
					output("You overhear a very intense conversation!");
					output("`n`n`^'We are very close to hatching a Dragon Egg over in Bluspring's Warrior Training,'`0 says a gruff male voice.");
					output("`n`n`0'That may be the case, but how are we going to stop some of those pesky dragon slayers from catching us?'`0 asks a woman, her voice trembling slightly.");
					output("`n`n`^'Nobody knows about it.  Trust me, it can't fail! I've been bribing guards all over with these gems,' `0he replies.");
					output("`n`nShe opens a bag full of gems and several fall out, rolling to your feet.  You `%gain 2 gems`0.");
					$session['user']['gems']+=2;
					debuglog("received 2 gems by researching at the Boar's Head Inn.");
				}else{
					output("`^'I have to tell you a secret,'`0 states an older gentleman.  You creep closer to hear more.");
					output("`^'The bartender gave me an extra gold back when I got my change!'");
					output("`n`n`0You roll your eyes.  You're not going to learn anything new here.");
				}
			break;
			case 11: case 12:
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				output("You approach %s`0 and are about to ask a question when he hands you a package.",$barkeep);
				output("`n`n`%'I've been waiting for you.  Here's the package.  You know what to do with it'`0 he says.");
				if ((($level>10 && $chance<=3) || ($level<=10 && $chance<=2)) && $session['user']['armor']!="`5Rustproof Armor`0"){
					output("You leave with the package and discreetly open it.  Ahhh! A new jacket, nicer than your %s`0.  Excellent! It's `5Rustproof Armor`0!",$session['user']['armor']);
					$session['user']['armor']="`5Rustproof Armor`0";
					$session['user']['armordef']++;
					$session['user']['defense']++;
					debuglog("received a Rustproof Armor (1 higher armor than their previous) by researching at the Boar's Head Inn.");
				}else{
					output("Knowing you're not going to get anything for it, you still take the package and deliver it to Pegasus at the Armor Store.");
					blocknav("inn.php");
					blocknav("village.php");
					addnav("Continue","armor.php");
				}
			break;
			case 13: case 14:
				output("You look around, and when nobody is looking you start knocking on random planks on the wall.`n`n");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $chance<=3) || ($level<=10 && $chance<=2)){
					output("One of them is hollow! You push through and find a secret temple.  It looks like there's some nice items here.`n`n");
					$chance2=e_rand(1,3);
					$chance3=e_rand(1,3);
					if ($chance2==3 && $session['user']['weapon']!="`7Antique Katana`0"){
						output("The first item is a new weapon.  It's an `7Antique Katana`0.  Well, it's no better than your current weapon but it's something new.`n`n");
						$session['user']['weapon']="`7Antique Katana`0";
						debuglog("received an Antique Katana (no change from previous) by researching at the Boar's Head Inn.");
					}else{
						output("The first item is a new weapon.  It's a `&Glowing Katana`0! Excellent!`n`n");
						$session['user']['weapondmg']++;
						$session['user']['attack']++;
						$session['user']['weapon']="`&Glowing Katana`0";
						debuglog("received a Glowing Katana (1 higher weapon than their previous) by researching at the Boar's Head Inn.");
					}
					if ($chance3==1 && $session['user']['armor']!="`0Well-Worn Armor`0"){
						output("The second item is new armor.  It's `7Well-Worn Armor`0.  Although it's no better than your current armor, it looks much cooler and you gain a charm point by wearing it.`n`n");
						$session['user']['armor']="`0Well-Worn Armor`0";
						$session['user']['charm']++;
						debuglog("received a Well-Worn Armor (no change from previous) and a charm point by researching at the Boar's Head Inn.");
					}else{
						output("The second item is new armor.  It's `&Reinforced Armor`0 that will do a great job at protecting you.  You happily change out the armor.");
						$session['user']['armor']="`&Reinforced Armor`0";
						$session['user']['armordef']++;
						$session['user']['defense']++;
						debuglog("received a Reinforced Armor (1 higher armor than their previous) by researching at the Boar's Head Inn.");
					}
					output("You leave your old weapon and armor behind and quickly leave the temple.");
				}else{
					output("All the planks are real and nothing results from your research.");
				}
			break;
			case 15: case 16: case 17: case 18:
				output("You are walking through the halls of the lodge when you hear a commotion.  You open a door to one of the rooms and see someone breaking in through the window!");
				addnav("Attack the Intruder","runmodule.php?module=dragoneggs&op=inn15");
				blocknav("inn.php");
				blocknav("village.php");
			break;
			case 19: case 20:
				output("You find yourself in the cellars when a commotion gets your attention.");
				output("`n`nYou find yourself face to face with a `4Fire Hound`0!");
				set_module_pref("monster",1);
				addnav("Fight the `4Fire Hound","runmodule.php?module=dragoneggs&op=attack");
				blocknav("inn.php");
				blocknav("village.php");
			break;
			case 21: case 22:
				output("%s`0 pulls you into his office and sits across from you.  He tells you the story of the first `&Green Dragon`0 and the likelihood of death to all the residents of the Kingdom.",$barkeep);
				output("You feel a cold chill spread across you.`n`n");
				$chance=e_rand(1,15);
				$level=$session['user']['level'];
				if (($level>7 && $chance<=4) || ($level<=7 && $chance<=3)){
					output("The initial wave of fear passes from you and the information starts to settle in. He gives you `%3 gems`0 and tells you to go destroy those dragon eggs!");
					$session['user']['gems']+=3;
					debuglog("received 3 gems by researching at the Boar's Head Inn.");
				}else{
					if ($session['user']['gems']>5) $gems=5;
					else $gems=$session['user']['gems'];
					$session['user']['gems']-=$gems;
					output("You feel your grip on reality failing. You lose track for reality and pass out.  You wake later to find you've lost `%%s gems`0.",$gems);
					if (get_module_pref("researches")<get_module_setting("research")){
						increment_module_pref("researches",1);
						debuglog("lost $gems gems and an extra research turn by researching in the Boar's Head Inn.");
						output("You also lose a research turn.");
					}else debuglog("lost $gems gems by researching in the Boar's Head Inn.");
				}
			break;
			case 23: case 24:
				output("You see a room slightly ajar and take a peak inside.  You overhear someone and peak even farther.");
				output("`n`nA witch looks up at you and casts a spell.  You're `iCursed`i!!");
				if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
					$session['bufflist']['blesscurse']['rounds'] += 5;
					debuglog("increased their curse by 5 rounds researching in the Boar's Head Inn.");
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
					debuglog("was cursed by researching in the Boar's Head Inn.");
				}
			break;
			case 25: case 26:
				output("You decide to research a little around the back of the Inn.  As you're walking, you feel your foot sinking into the ground.");
				output("Your foot becomes stuck in the mud.");
				if ($session['user']['turns']>0){
					$session['user']['turns']--;
					debuglog("lost a turn researching in the Boar's Head Inn.");
					output("You finally wrench your foot out, but it takes `@a turn`0.");
				}else{
					$session['user']['charm']--;
					debuglog("lost a charm by researching in the Boar's Head Inn.");
					output("You wrench your foot out but it definitely doesn't look nice.  You `&lose a charm`0.");
				}
			break;
			case 27: case 28:
				output("You find yourself in %s's`0 office... it's making you nervous.`n`n",$barkeep);
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if ($session['user']['turns']>0 && (($level>8 && $chance<=3) || ($level<=8 && $chance<=2))){
					$session['user']['turns']--;
					output("You quickly read some papers and realize it's a parchment with ancient text on it.  You spend a turn deciphering it.");
					require_once("lib/increment_specialty.php");
					increment_specialty("`0");
					output("`nYou advance in your specialty!");
					debuglog("spent a turn to increment specialty by researching at the Boar's Head Inn.");
				}else{
					if ($session['user']['turns']>0){
						$session['user']['turns']--;
						output("You find some papers and read through several of them.  It's just receipts and paperwork from the bar. You waste a turn without gaining anything useful.");
						debuglog("spent a turn researching dragon eggs at the Boar's Head Inn.");
					}else{
						output("You don't find anything useful and you hurry out of the office");
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
	addnav(array("Return to %s",$innname),"inn.php");
	villagenav();
}
?>