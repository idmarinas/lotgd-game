<?php
function dragoneggs_weapons(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("MightyE's Weapons");
	output("`c`b`&MightyE's Weapons`b`c`7");
	$open=get_module_setting("weaponsopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("weaponsmin") && get_module_setting("weaponslodge")>0 && get_module_pref("weaponsaccess")==0){
		output("You don't have enough `@Green Dragon Kills`7 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("weaponsmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("weaponslodge")>0 && get_module_pref("weaponsaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("You're out of research turns for today.");
	}else{
		output("You decide to look for Dragon Eggs at MightyE's Weapons.`n`n");
		increment_module_pref("researches",1);
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		debuglog("used a research turn in the MightyE's Weapons.");
		switch($case){
		//switch(25){
			case 1: case 2:
				output("You look around the store and notice a finely fashioned letter opener.");
				output("`#'Ah, yes, that's one of the last ones I have like that.  Not much use, but very pretty,'`7 explains `!MightyE`7. `#'If you're interested, it'll only cost you `^250 gold`#.'");
				if ($session['user']['gold']>=250) addnav("Purchase Letter Opener","runmodule.php?module=dragoneggs&op=weapons1");
				else output("`n`n`7Not having `^250 gold`7, you decide to pass on the offer.");
			break;
			//
			case 3: case 4:
				if ($session['user']['turns']>1){
					output("`#'Listen, I have to step out for a couple of minutes,'`7 says `!MightyE`7, `#'If you don't mind watching the store, I can give you `^350 gold`#. It will only take a turn.'");
					output("`n`n`7Are you interested in making an easy `^350 gold`7?");
					addnav("Watch the Store","runmodule.php?module=dragoneggs&op=weapons3");
				}else{
					output("`#'I'm sorry, but I have to close up,'`7 explains `!MightyE`7, `#'Please exit the store.'");
					output("`n`nNot having any recourse, you leave.");
					blocknav("weapons.php");
				}
			break;
			case 5: case 6:
				output("Feeling that sometimes you can learn more just by listening, you settle back and try to soak in information.");
				output("`n`nYou don't find out anything new about dragon eggs. However, you feel refreshed.  Your `\$hitpoints`7 are restored to full and you find a `%gem`7!");
				$session['user']['gems']++;
				$session['user']['hitpoints']=$session['user']['maxhitpoints'];
				debuglog("gained a gem and restored hitpoints to full by researching at the MightyE's Weapons.");
			break;
			case 7: case 8:
				output("You see a very old man enter and pick up one of the daggers.  He talks with `!MightyE`7 about it and pays with some strange `^gold coins`7.  You ask `!MightyE`7 if you can take a look at them.");
				output("`n`nYou deduce that these aren't from around here! You notice him drop something shiny and you go to pick it up!`n`nYou `%gain 1 gem`7.");
				$session['user']['gems']++;
				debuglog("gained a gem by researching at the MightyE's Weapons.");
			break;
			case 9: case 10:
				output("You see a group of men enter into the store and smell the strange odor of fish coming from them.  You look closer and think you see... is that... Gills???");
				output("`n`nYou stumble back and hit your head on a shelf.  You `\$lose all hitpoints except 1`7.");
				$session['user']['hitpoints']=1;
				debuglog("lost all hitpoints except 1 by researching at the MightyE's Weapons.");
			break;
			case 11: case 12:
				output("You look under a rug, thinking there's something of interest there.");
				output("`n`nThere isn't.");
			break;
			case 13: case 14:
				$gold=$session['user']['gold']+$session['user']['goldinbank'];
				$value=round($session['user']['weaponvalue']*.75);
				$total=$value+$gold;
				output("You strike up a conversation with `!MightyE`7 about what the best weapon is to use against the `@Green Dragon`7.");
				output("`n`n`#'I have a really powerful one here... something better than anything you can get from normal means,'`7 he says.");
				output("`n`nHe pulls out an `#Ice Sword`7 and shows it to you.`n`n`#");
				if ($total>=10000 && $session['user']['weapondmg']<17 && $session['user']['weapon']!="`#Ice Sword`0"){
					$pay=10000-$value;
					$left=-($value-10000);
					if ($value<10000) output("'I'll give you `^%s gold`# for that %s`#, you give me `^%s gold`#, and in exchange you'll get this quality Ice Sword (attack 17).  Are you interested?'",$value,$session['user']['weapon'],$left); 
					else output("'I'll give it to you for an even exchange with your %s`#'",$session['user']['weapon']);
					addnav("Purchase Handgun","runmodule.php?module=dragoneggs&op=weapons13");
				}elseif ($session['user']['weapon']=="`#Ice Sword`0"){
					output("`7You show him your `#Ice Sword`7 and he smiles.  `#'Ahh, I see you already have one.'");
				}elseif ($session['user']['weapondmg']>=17){
					output("'Unfortunately, you're weapon is already better than this one, so there's no reason for you to buy it.'");
				}else{
					output("'Unfortunately, you don't have the funds to make a purchase like this.  I would've been willing to give you it at a great price of only `^10,000 gold`#. Too bad for you!'");
				}
			break;
			case 15: case 16:
				output("You are looking around when a robber comes into the shop.  He grabs one of the better weapons and you realize it's your chance to shine!");
				set_module_pref("monster",18);
				addnav("Fight the Robber","runmodule.php?module=dragoneggs&op=attack");
				blocknav("weapons.php");
				blocknav("village.php");
			break;
			case 17: case 18:
				output("You see something very interesting in the corner of the store on the ground.  You go closer and notice that it's very shiny.");
				output("`n`nExcitedly, you run over to pick it up and discover...`n`n");
				if (e_rand(1,2)==1){
					output("It's just a gum wrapper.  Nothing of value!");
				}else{
					output("A `^platinum rock`7! You find that it's worth `^100 gold`7!");
					$session['user']['gold']+=100;
					debuglog("found 100 gold in while researching in the MightyE's Weapons.");
				}
			break;
			case 19: case 20:
				output("You brush by a young boy and feel something drop. He picks up an envelope and hands it to you. `@'It seems like you dropped this,'`7 he says as he runs off.");
				output("`n`nYou open the envelope and something shiny plops out.");
				output("`n`nYou `%gain a gem`7.");
				$session['user']['gems']++;
				debuglog("found a gem while researching in the MightyE's Weapons.");
			break;
			case 21: case 22:
				output("You see a small jar full of some marbles on the counter.  A sign next to it posts a wager:`n");
				output("`n`c`^Guess how many marbles are in the jar: 1 gold per attempt");
				output("`nCorrectly guess and win %s gold!`c`n",250+get_module_setting("jar"));
				if ($session['user']['gold']>0) addnav("Guess","runmodule.php?module=dragoneggs&op=weapons21");
				else("`7Not having any gold, you decide not to brother.");
			break;
			case 23: case 24:
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if ((($level>10 && $chance<=4) || ($level<=10 && $chance<=3)) && $session['user']['weapon']!="`^Samurai Sword`0"){
					output("`#'I like your cause.  I tell you what, I'm going to give you a free upgrade to your weapon.'");
					output("`!MightyE`7 pulls out a `^Samurai Sword`7 and explains that it's better than your weapon and hands it to you.");
					$session['user']['weapon']="`^Samurai Sword`0";
					$session['user']['weapondmg']++;
					$session['user']['attack']++;
					output("`n`nYou take a couple of swipes with the Sword and like it... very much!");
					debuglog("received a Samurai Sword (+1 from old weapon) by researching at the MightyE's Weapons.");
				}else{
					output("`!MightyE`7 is about to mention something very helpful but then he doesn't.  You wonder what he was about to say.");
				}
			break;
			case 25: case 26: case 27: case 28:
				output("You strike up a conversation with `!MightyE`7 about the mysteries in the kingdom.  He asks about the gems you've found.");
				if ($session['user']['gems']>0){
					output("You mention that you'd be willing to sell some gems for a price. He agrees to pay you `^300 gold`7 for a`% gem`7.");
					addnav("Sell gems");
					addnav("Sell `%1 gem","runmodule.php?module=dragoneggs&op=weapons25&op2=1");
					if ($session['user']['gems']>1){
						output("You offer to sell him 2 gems for `^900 gold`7.");
						addnav("Sell `%2 gems","runmodule.php?module=dragoneggs&op=weapons25&op2=2");
						if ($session['user']['gems']>2){
							output("Finally, if he's really interested, you'll sell him 3 gems for `^1500 gold`7.");
							addnav("Sell `%3 gems","runmodule.php?module=dragoneggs&op=weapons25&op2=3");
						}
					}
					addnav("Leave");
				}else{
					output("You tell him you don't have any. After talking to you for a little while, he feels pity and gives you `%a gem`7!");
					$session['user']['gems']++;
					debuglog("gained a gem by researching at the MightyE's Weapons.");
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
	addnav("Return to MightyE's Weapons","weapons.php");
	villagenav();
}
?>