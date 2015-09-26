<?php
function dragoneggs_bath(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("The Outhouse");
	output("`c`b`2The Outhouse`b`c`2`n");
	//This will TRY to fix their current location just in case they are being transported here from the capital city
	if (is_module_active("cities") && $session['user']['location']== getsetting("villagename", LOCATION_FIELDS)){
		if ($session['user']['location'] !=get_module_pref("homecity","cities")) $session['user']['location']=get_module_pref("homecity","cities");
		elseif (is_module_active("racehuman") && $session['user']['location'] != get_module_setting("villagename","human")) $session['user']['location']=get_module_setting("villagename","human");
		elseif (is_module_active("raceelf") && $session['user']['location'] != get_module_setting("villagename","elf")) $session['user']['location']=get_module_setting("villagename","elf");
		elseif (is_module_active("racedwarf") && $session['user']['location'] != get_module_setting("villagename","dwarf")) $session['user']['location']=get_module_setting("villagename","dwarf");
	}
	$open=get_module_setting("bathopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("bathmin") && get_module_setting("bathlodge")>0 && get_module_pref("bathaccess")==0){
		output("You don't have enough `@Green Dragon Kills`2 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("bathmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`2 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("bathlodge")>0 && get_module_pref("bathaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`2You're out of research turns for today.");
	}else{
		output("`2You decide to look for Dragon Eggs at the Outhouse.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(27){
			case 1: case 2:
				output("You are looking around the Outhouse when you see a pleasant group of ladies sitting on the patio in the back.");
				output("`n`nThey see you looking around and invite you over for tea.`n`n");
				if ($session['user']['turns']>1){
					output("`@'We'll only take 2 turns of your time!'`2 they say.  Would you like some tea?");
					addnav("Have Some Tea","runmodule.php?module=dragoneggs&op=bath1");
				}else{
					output("`#'I don't have any time for tea,'`2 you explain.  If only you had a couple of turns perhaps you could have learned something!");
				}
			break;
			case 3: case 4:
				output("`&'I got what ya need'`2 says a strange man hanging around the Outhouse.");
				output("`n`nWant to hear more?");
				addnav("Talk to the Stranger","runmodule.php?module=dragoneggs&op=bath3");
			break;
			case 5: case 6:
				output("You get hit with a soiled towel.  You `&lose 1 charm`2.");
				$session['user']['charm']--;
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
					output("You look around but can't figure out who did it.");
				}else{
					if ($session['user']['hitpoints']>1){
						output("You lose a hitpoint from the trauma.");
						$session['user']['hitpoints']--;
					}
				}
				debuglog("lost a charm while researching dragon eggs at the Outhouse.");
			break;
			case 7: case 8:
				output("There's an emergency in the Outhouse and you're asked to help clean things up.`n`n");
				if ($session['user']['turns']>0){
					output("Realizing that this is part of your civic duty, you `@spend a turn`2 pitching in to clean up.");
					$session['user']['turns']--;
					debuglog("spent a turn while researching dragon eggs at the Outhouse.");
				}else{
					output("You don't have time for this! You're showered in jeers from the other people helping out.");
					output("`n`nYou `&lose a charm point`2.");
					$session['user']['charm']--;
					debuglog("lost a charm while researching dragon eggs at the Outhouse.");
				}
			break;
			case 9: case 10:
				output("The Outhouse has hired a nice quartet to play while people come in and out.");
				output("`n`nIt's quite soothing! You `@gain a turn`2.");
				$session['user']['turns']++;
				debuglog("gained a turn while researching dragon eggs at the Outhouse.");
			break;
			case 11: case 12:
				output("A man in a trenchcoat holding an ancient book approaches you.");
				output("`n`n`5'There is a lot of good information in this book.  You can have it for only `^500 gold`5,'`2 he says.");
				if ($session['user']['gold']>=500) addnav("Buy the Book","runmodule.php?module=dragoneggs&op=bath11");
				else output("`n`n`#'I'm not interested,'`2 you say.");
			break;
			case 13: case 14:
				output("Someone left out some crackers.  Would you like one?");
				addnav("Take a Cracker","runmodule.php?module=dragoneggs&op=bath13");
			break;
			case 15: case 16:
				output("You go to look at the 'haunted' faucet. You've heard that there's blood all over it.`n`n");
				output("You look closely and realize there's something strange about the red 'blood' on the faucet.  It's just paint!");
				output("`n`nYou clean it off and notice something shiny.`n`n");
				$session['user']['gems']++;
				output("You `%gain a gem`2.");
				debuglog("gained a gem while researching dragon eggs at the Outhouse.");
			break;
			case 17: case 18:
				$previous= strpos($session['user']['weapon'],"Hanso Sword")!==false ? 1 : 0;
				if ($previous==0){//Never had this
					output("It looks like someone left their antique `#Hanso Sword`2 just laying around.");
					output("`n`nYou don't think anyone would mind if you took it for yourself since it's a little better than your current weapon.");
					$session['user']['weapon']="Hanso Sword";
					$session['user']['weapondmg']++;
					$session['user']['attack']++;
					debuglog("found a Hanso Sword (attack +1 of old weapon) while researching dragon eggs at the Outhouse.");
				}else{
					output("You find a wallet with `^420 gold`2 in it.  You ask around but nobody knows who it belongs to, so now it belongs to you!");
					$session['user']['gold']+=420;
					debuglog("gained 420 gold while researching dragon eggs at the Outhouse.");
				}
			break;
			case 19: case 20:
				output("Seems like everything is in order here.");
				output("`n`nYou `@gain a turn`2 due to the ease of your research.");
				$session['user']['turns']++;
				debuglog("gained a turn while researching dragon eggs at the Outhouse.");
			break;
			case 21: case 22:
				output("A painting on the wall tries to grab for you!`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
					output("No, it was just your imagination.");
				}else{
					output("Darn paintings! You take your %s`2 and cut the painting down.",$session['user']['weapon']);
					$cost=e_rand(100,450);
					if (is_module_active("jail") || is_module_active("djail")) $jail=1;
					else $jail=0;
					if ($session['user']['gold']>=$cost){
						output("You're fined `^%s gold`2 for vandalism.",$cost);
						debuglog("was fined $cost gold for vandalism while researching dragon eggs at the Outhouse.");
						$session['user']['gold']-=$cost;
					}elseif($jail==1){
						output("You're sent to jail for vandalism.");
						if (is_module_active("jail")) $injail="jail";
						else $injail="djail";
						set_module_pref("injail",1,$injail);
						debuglog("was jailed for vandalism while researching dragon eggs at the Outhouse.");
						addnav("To Jail", "runmodule.php?module=$injail");
						blocknav("runmodule.php?module=outhouse");
						blocknav("forest.php");
					
					}else{
						output("Luckily, nobody notices your vandalism.");
					}
				}
			break;
			case 23: case 24:
				output("You find an old piece of paper on the ground. You decide to read it and it seems a bit confusing.");
				if ($session['user']['turns']>0){
					output("You `@spend a turn`2 following the directions on it and `%find a gem`2.");
					$session['user']['turns']--;
					$session['user']['gems']++;
					debuglog("gained a gem and lost a turn while researching dragon eggs at the Outhouse.");
				}else{
					output("You can't make heads or tails out of it.");
				}
			break;
			case 25: case 26:
				output("You find a secret passageway! It's tough to say where it goes, but maybe it's worth it to find out.`n`n");
				if (get_module_pref("researches")>=2) output("If you decide to go, you may have 2 extra research turns for the day.");
				else output("If you decide to go, you may have an extra research for the day.");
				addnav("Yes","runmodule.php?module=dragoneggs&op=bath25&op2=yes");
				addnav("No","runmodule.php?module=dragoneggs&op=bath25&op2=no");
				blocknav("runmodule.php?module=outhouse");
				blocknav("forest.php");
			break;
			case 27: case 28:
				if ($session['user']['turns']>0){
					output("You `@spend a turn`2 chatting with a man who turns out to be an ancient shaman.  He teaches you how to increase your specialty.");
					$session['user']['turns']--;
					require_once("lib/increment_specialty.php");
					increment_specialty("`#");
					debuglog("incremented specialty for a turn while researching dragon eggs at the Outhouse.");
				}else{
					output("You meet a strange shaman and he's about to talk to you when you realize you don't have any time to talk to him.");
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
	addnav("Return to the Outhouse","runmodule.php?module=outhouse");
	addnav("Return to the Forest","forest.php");
}
?>