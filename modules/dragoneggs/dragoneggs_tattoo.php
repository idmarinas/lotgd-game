<?php
function dragoneggs_tattoo(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Tattoo Parlor");
	output("`c`b`&Petra, the Ink Artist`b`c`7");
	//If the player isn't in the right city, move them to it.
	if ($session['user']['location']!= get_module_setting("petraloc","petra")){
		$session['user']['location'] = get_module_setting("petraloc","petra");
	}
	$open=get_module_setting("tattooopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("tattoomin") && get_module_setting("tattoolodge")>0 && get_module_pref("tattooaccess")==0){
		output("You don't have enough `@Green Dragon Kills`7 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("tattoomin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("tattoolodge")>0 && get_module_pref("tattooaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("You're out of research turns for today.");
	}else{
		output("You decide to look for Dragon Eggs at Petra's Tattoo Parlor.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(3){
			case 1: case 2: 
				$chance=e_rand(1,5);
				if (($session['user']['level']>10 && $chance<4) || ($session['user']['level']<11 && $chance<3)){
					output("You notice a disheveled looking boy walk up to you and try to brush against you.");
					output("`n`nYou grab his hand and take your wallet back from him.  Darn pockpicket street rats!!");
				}else{
					output("You smile as one of the local children accidentally bumps against you. `#'Watch where you're going'`7 you comment with a smile.");
					output("`n`nThat is... until you realize that it was a pickpocket and your wallet is missing!");
					$gold=$session['user']['gold'];
					$session['user']['gold']=0;
					output("`n`nYou've lost all your money!");
					debuglog("lost $gold gold from a pickpocket while researching the Tattoo Parlor.");
				}
			break;
			case 3: case 4:
				if ($session['user']['gold']+$session['user']['goldinbank']>=2000){
					addnav("Purchase");
					output("A smarmy looking man stands next to you and whispers in your ear `4'You interested in a quality new weapon? I've got a couple that might interest you. By the way, I don't do exchanges or give you credit for your current items.'");
					output("`n`n`7He shows you what you can afford:`c`n");
					if ($session['user']['gold']+$session['user']['goldinbank']>=12000 && $session['user']['weapondmg']<=16){
						output("`#Silver Tipped Crossbow (`&Attack 17`#, `^Cost: 12,000 gold`#)");
						addnav("Buy Silver Tipped Crossbow","runmodule.php?module=dragoneggs&op=tattoo3&op2=3&op3=12000");
					}
					if ($session['user']['gold']+$session['user']['goldinbank']>=11000 && $session['user']['armordef']<=15){
						output("`n`#Reinforced Overcoat (`&Defense 16`#, `^Cost: 11,000 gold`#)");
						addnav("Buy Reinforced Overcoat","runmodule.php?module=dragoneggs&op=tattoo3&op2=2&op3=11000");
					}
					if (isset($session['bufflist']['throwingstar'])) {
						output("`n`#20 More Throwing Stars (`@Buff for 20 Rounds`#, `^Cost: 2000 gold`#)`c");
						addnav("Buy 20 More Throwing Stars","runmodule.php?module=dragoneggs&op=tattoo3&op2=1&op3=2000");
					}else{
						output("`n`#20 Throwing Stars (`@Buff for 20 Rounds`#, `^Cost: 2000 gold`#)`c");
						addnav("Buy 20 Throwing Stars","runmodule.php?module=dragoneggs&op=tattoo3&op2=1&op3=2000");
					}
					output("`n`n`7Are you interested?");
					addnav("Leave");
				}else{
					output("A smarmy looking man stands next to you and looks you over. He notices that you're looking a little low on cash and just walks away.  You wonder what he had to offer.");
				}
			break;
			case 5: case 6:
				output("As you're looking around, you accidentally hit the arm of a man getting a tattoo.  He doesn't look too kindly at you.");
				output("`n`n`#'Why would you want to mess with a lumberjack???' `7he asks you.  Oh.  It looks like he's a lumberjack. You really don't have a good answer, but it's too late to think of one anyway.");
				if (is_module_active("lumberyard")) output("`n`nYou suddenly recognize this guy from the Lumberyard.  Oh no!");
				set_module_pref("monster",8);
				addnav("Fight the Lumberjack","runmodule.php?module=dragoneggs&op=attack");
				blocknav("runmodule.php?module=petra");
				blocknav("village.php");
			break;
			case 7: case 8: case 17: case 18:
				output("Deciding to take advantage of the free ale available for patrons, you down a tankard and tip your hat to the brave men and women getting tattoos today.");
				apply_buff('buzz',array(
					"name"=>translate_inline("`#Buzz"),
					"rounds"=>10,
					"wearoff"=>translate_inline("Your buzz fades."),
					"atkmod"=>1.25,
					"roundmsg"=>translate_inline("You've got a nice buzz going."),
					"activate"=>"offense"
				));
				debuglog("received the ale buff (no drinking penalty) while researching dragon eggs at the Tattoo Parlor.");
			break;
			case 9: case 10:
				output("`#'Friendly card game?' `7offers one of the men waiting around to get a tattoo.");
				output("`#'The buy-in is only `^300 gold`#,'`7 he tells you. Before you have a chance to object, you find yourself sitting at the table with cards in your hand.");
				addnav("Play Cards","runmodule.php?module=dragoneggs&op=tattoo9");
				blocknav("runmodule.php?module=petra");
				blocknav("village.php");
			break;
			case 11: case 12:
				$chance=e_rand(1,5);
				if (($session['user']['level']>10 && $chance<4) || ($session['user']['level']<11 && $chance<3)){
					output("You have a pleasant conversation with several of the patrons and ask if they can help contribute to your cause of destroying Dragon Eggs. They decide to try to help you.");
					output("`n`nYou `%gain a gem`7!!");
					$session['user']['gems']++;
					debuglog("received a gem while researching dragon eggs at the Tattoo Parlor.");
				}else{
					output("You start asking questions to the wrong people.  They don't take kindly to you asking so many personal questions.");
					output("`n`nBefore you know what's going on, you find yourself kicked out of the Tattoo Parlor and all your money gone.");
					$gold=$session['user']['gold'];
					$session['user']['gold']=0;
					blocknav("runmodule.php?module=petra");
					debuglog("lost $gold gold while researching dragon eggs at the Tattoo Parlor.");
				}
			break;
			case 13: case 14:
				output("In the back room, by a pool table, a man challenges you to a game of pool.");
				output("You decide it's not worth your time, but he eggs you on and you can't refuse.`n`n");
				if ($session['user']['gold']>=200){
					output("As soon as the game begins, you realize you've been had.  He beats you and takes `^200 gold`7 from you.");
					$session['user']['gold']-=200;
					debuglog("lost 200 gold while researching dragon eggs at the Tattoo Parlor.");
				}elseif($session['user']['weapondmg']>1){
					output("You find you've lost your %s`7 in a bet. Feeling desperate, you grab the `#Pool Stick`7 to use instead, even though it's not as nice.",$session['user']['weapon']);
					$session['user']['attack']--;
					$session['user']['weapondmg']--;
					$value=round($session['user']['weaponvalue']*.9);
					$session['user']['weaponvalue']=$value;
					$session['user']['weapon']="Pool Stick";
					debuglog("got a pool stick (-1 to attack) while researching dragon eggs at the Tattoo Parlor.");
				}else{
					output("Turns out, he actually sucks at pool.  Unfortunately, he also sucks at paying up on bets and doesn't have any money to pay you.  You beat him up and leave him with only `\$1 hitpoint`7.");
				}
			break;
			case 15: case 16:
				$chance=e_rand(1,5);
				output("One of the people getting a tattoo starts yelling that it hurts too much.  Soon enough, punches are flying! You hear the sound of the sheriff coming...`n`n");
				if (($session['user']['level']>10 && $chance<=4) || ($session['user']['level']<11 && $chance<=3)){
					output("Luckily, you make it out the door before they arrive.");
					blocknav("runmodule.php?module=petra");
				}else{
					if (is_module_active("djail")){
						if (is_module_active("jail")){
							$jail="jail";
							$rand=9;
						}else{
							$jail="djail";
							$rand=5;
						}
					}else $rand=0;
					if (is_module_active("djail") && e_rand(1,$rand)==4){
						if (get_module_pref("deputy")==1){
							output("When the sheriff arrives, you explain that as deputy, you've taken care of things.`n`nHe thanks you for your good work and goes on his way.");
						}else{
							output("`@'Off to jail, all of you!'`7 says the sheriff.`n`nYou're hauled off in the patti-wagon.");
							blocknav("runmodule.php?module=petra");
							blocknav("village.php");
							set_module_pref("injail",1,$jail);
							addnav("To Jail", "runmodule.php?module=$jail");
							debuglog("was jailed while researching dragon eggs at the Tattoo Parlor.");
						}
					}else{
						output("When the sheriff arrives, you explain that you weren't involved. Not wanting to do the paperwork, he lets you go.");
					}
				}
			break;
			case 19: case 20:
				output("`#'Who's up for a game of darts?'`7 asks a small impish man, `#'Put down `^100 gold`# and if you can beat me you can win `^300 gold`#. Who's up for it?'");
				if ($session['user']['gold']>=100) addnav("Play Darts","runmodule.php?module=dragoneggs&op=tattoo19");
				else output("`n`n`7Not having enough money to play, you decline.");
			break;
			case 21: case 22: case 23: case 24:
				output("A man getting a tattoo of a star gets your attention. `!'So what's your story?'`7 he asks you.  You tell him your story.");
				output("`n`nAfter a while, he seems a bit intrigued. `!'I'll help you out if you'd like, but you'll have to make it worth my while,'`7 he says.");
				output("`n`nIf you'd like to give him `%3 gems`7, `QRyan Dean`7 will join you.");
				if ($session['user']['gems']>=3) addnav("Give gems","runmodule.php?module=dragoneggs&op=tattoo21");
				else output("`n`nUnfortunately, you don't have enough gems.");
			break;
			case 25: case 26: case 27: case 28:
				output("`#'Interested in some `%gems`#?'`7 asks one of the tattoo artists.");
				output("`n`nYou ask about the details, and he says he'll sell you some `%gems`7.`n`n");
				if ($session['user']['gold']+$session['user']['goldinbank']>=500){
					output("For `^500 gold`7 you can buy a `%gem`7.");
					addnav("Buy gems");
					addnav("Buy `%1 gem","runmodule.php?module=dragoneggs&op=tattoo25&op2=1&op3=500");
					if ($session['user']['gold']+$session['user']['goldinbank']>=900){
						output("For `^900 gold`7 you can buy `%2 gems`7.");
						addnav("Buy `%2 gems","runmodule.php?module=dragoneggs&op=tattoo25&op2=2&op3=900");
						if ($session['user']['gold']+$session['user']['goldinbank']>=1200){
							output("Finally, for `^1200 gold`7 you can buy `%3 gems`7.");
							addnav("Buy `%3 gems","runmodule.php?module=dragoneggs&op=tattoo25&op2=3&op3=1200");
						}
					}
					addnav("Leave");
				}else{
					output("You tell him that although you find his offer intriguing, you just don't have enough money to buy a `%gem`7. He feels bad for you and gives you a `^20 gold`7.");
					$session['user']['gold']+=20;
					debuglog("gained 20 gold by researching at the Tattoo Parlor.");
				}
			break;
			case 29: case 30:
				output("You are about to get a tattoo when you see a gem on the floor. You realize that perhaps picking up the `%gem`7 would be a better idea.");
				output("`n`nYou `%gain a gem`7!");
				$session['user']['gem']++;
				debuglog("gained a gem by researching at the Tattoo Parlor.");
			break;
			case 29: case 30: case 31: case 32: case 33: case 34: case 35:
				output("You don't find anything of value.");
			break;
			case 36:
				dragoneggs_case36();
			break;
		}
	}
	addnav("Return to the Tattoo Parlor","runmodule.php?module=petra");
	villagenav();
}
?>