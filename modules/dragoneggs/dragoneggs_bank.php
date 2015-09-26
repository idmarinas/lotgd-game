<?php
function dragoneggs_bank(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Ye Olde Bank");
	output("`c`b`^Ye Olde Bank`b`c`6");
	$open=get_module_setting("bankopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("bankmin") && get_module_setting("banklodge")>0 && get_module_pref("bankaccess")==0){
		output("You don't have enough `@Green Dragon Kills`6 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("bankmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`6 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("banklodge")>0 && get_module_pref("bankaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`6You're out of research turns for today.");
	}else{
		output("`6You decide to look for Dragon Eggs at the Bank.`n`n");
		increment_module_pref("researches",1);
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		debuglog("used a research turn in the Bank.");
		switch($case){
		//switch(3){
			case 1: case 2:
				$bank=$session['user']['level']*40;
				output("You spend some time in the bank and ask to see some of your papers.");
				output("There's some confusion about your account and an error occurs somewhere with all the shuffling of papers.");
				output("`n`nYou notice that somehow your bank account suddenly has `^%s gold`6 more than it should... Nice!",$bank);
				$session['user']['goldinbank']+=$bank;
				debuglog("gained $bank gold by researching at the Bank.");
			break;
			case 3: case 4:
				output("`@'Hello.  Have you come to empty your safety deposit box?'`6 asks the teller.`n`n");
				output("Confused a bit, you walk with her to the back room.  She takes down a box, hands you a key, and exits. You find yourself alone with the box.`n`n");
				if (e_rand(1,3)<3){
					if (isset($session['bufflist']['power'])) {
						output("You open the box and find a glowing stone.  When you touch it, a power spreads across you.");
						apply_buff('power',array(
							"name"=>translate_inline("Mysterious Power"),
							"rounds"=>10,
							"wearoff"=>translate_inline("`4The mysterious power fades."),
							"atkmod"=>1.05,
						));
						debuglog("gained a mysterious buff by researching at the Bank.");
					}else{
						output("You open it and find nothing inside.  Oh well.");
					}
				}else{
					output("You open the box and a note slides out.  `\$'Leave Town Immediately'`6 is all it says.  It throws off your concentration.");
					if ($session['user']['gems']>0){
						$session['user']['gems']--;
						output("`n`nYou `%drop a gem`6 in your confusion.");
						debuglog("lost a gem by researching at the Bank.");
					}else{
						output("`n`nYou shake your head and leave.");
					}
				}
			break;
			case 5: case 6:
				output("You look around and suddenly spot some members of the `&Sheldon Gang`6 in line.");
				if (is_module_active("sheldon") && get_module_pref("member","sheldon")>0){
					output("Being a member, you have a chance to participate in the `iQuick Change`i con. Are you in?");
					blocknav("bank.php");
					blocknav("village.php");
					addnav("Yes, Count me in!","runmodule.php?module=dragoneggs&op=bank5&op2=1");
					addnav("No, I refuse.","runmodule.php?module=dragoneggs&op=bank5&op2=2");
				}else{
					output("You try to raise a warning and the gang members end the con. On the way out, one of the gang members punches you in the stomach.");
					if ($session['user']['hitpoints']>10){
						output("You crumple over in pain and lose `\$10 hitpoints`6.");
						$session['user']['hitpoints']-=10;
						debuglog("lost 10 hitpoints by researching at the Bank.");
					}else{
						output("You crumple over in pain and lose all your hitpoints except one.");
						$session['user']['hitpoints']=1;
						debuglog("lost all hitpoints except 1 by researching at the Bank.");
					}
				}
			break;
			case 7: case 8:
				output("An old man taps you on the shoulder holding a paper fortune teller.");
				output("`n`n`#'Pick a number!'`6 he tells you. Are you interested?");
				blocknav("bank.php");
				blocknav("village.php");
				addnav("Pick 1","runmodule.php?module=dragoneggs&op=bank7&op2=1");
				addnav("Pick 2","runmodule.php?module=dragoneggs&op=bank7&op2=2");
				addnav("Pick 3","runmodule.php?module=dragoneggs&op=bank7&op2=3");
				addnav("Pick 4","runmodule.php?module=dragoneggs&op=bank7&op2=4");
				addnav("No thank you","runmodule.php?module=dragoneggs&op=bank7&op2=5");
			break;
			case 9: case 10:
				$bank=$session['user']['level']*5;
				output("You ask to take out a loan. The bank gives you a loan at a great rate.  Through a quick investment, you make a quick `^%s gold`6.",$bank);
				$session['user']['gold']+=$bank;
				debuglog("gained $bank gold by researching at the Bank.");
			break;
			case 11: case 12:
				output("You notice a rich man dressed in the finest clothing withdraw a huge amount of money from the bank.");
				output("Curious, you follow him out of the bank. He looks at you as if he's trying to impress you and takes out a piece of silk and lights his cigar with it.");
				output("`n`nHe dramatically drops the silk cloth and wanders off.");
				output("You run up as quickly as you can...`n`n");
				if (e_rand(1,2)==1){
					output("But you fail to put out the flame in time.  Oh well, it was probably worthless anyway.");
				}else{
					$cash=e_rand(1,2)*50;
					output("And you succeed! You look down and see that you've found a rare silk scarf worth `^%s gold`6.",$cash);
					$session['user']['gold']+=$cash;
					debuglog("gained $bank gold by researching at the Bank.");
				}
			break;
			case 13: case 14:
				output("You see a penny on the ground and pick it up without even looking at it.`n`n");
				if (e_rand(1,2)==1){
					output("It was Heads-Up! You're `iBlessed`i!");
					if ($session['bufflist']['blesscurse']['atkmod']==1.2) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their blessing by 5 rounds by researching at the Bank.");
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
						debuglog("received a blessing by researching at the Bank.");
					}
				}else{
					output("It was Tails-Up! You're `iCursed`i!");
					if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their curse by 5 rounds by researching at the Bank.");
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
						debuglog("received a curse by researching at the Bank.");
					}
				}
			break;
			case 15: case 16:
				if ($session['user']['turns']>0){
					output("You find yourself standing in a long line.  Why are you standing in a line? I don't know, but for some reason you are.");
					output("`n`nYou settle in for a `@turn`6 and wait.  You overhear two people in front of you talking about how many gems they have.");
					output("`n`nOne pulls out a handful and a gem rolls by to your feet. You quickly pick it up and look around innocently.  You `%gain a gem`6 while you wait.");
					$session['user']['turns']--;
					$session['user']['gems']++;
					debuglog("received a gem but lost a turn by researching at the Bank.");
				}else{
					output("You notice a very long line in front of you and you just don't have the time to wait.");
				}
			break;
			case 17: case 18:
				output("You find yourself waiting in line and you're up next!");
				output("`n`nYou `@gain a turn in excitement`6!`n`n");
				output("Suddenly, the old lady in front of you pulls out a huge bag of copper coins and starts counting them out one at a time.  When she gets to 13,040 she loses place and has to start all over again!");
				output("`n`nYou `@lose a turn`6 and find yourself gnawing your fingernails down to the nubs. You lose all your hitpoints except one!");
				$session['user']['hitpoints']=1;
				debuglog("lost all hitpoints but 1 by researching at the Bank.");
			break;
			case 19: case 20:
				$bank=$session['user']['level']*e_rand(25,35);
				output("You arrive at the teller and start to ask her a question. She recognizes you and mentions that she was happy that you deposited `^%s gold`6 yesterday.",$bank);
				output("Knowing that you didn't deposit any money yesterday, you start to argue with her.`n`n");
				output("She pulls out a deposit slip with your name and your signature... How is it possible??");
				if ($session['user']['turns']>0){
					output("`n`nYou take the paper from her but spend a turn studying it and just accept the 'bonus deposit' without any more argument.");
					$session['user']['goldinbank']+=$bank;
					$session['user']['turns']--;
					debuglog("gained $bank gold but lost a turn by researching at the Bank.");
				}else{
					output("`n`nShe finally agrees that it must have been someone else and snatches the deposit slip back from you, opens her drawer, takes out `^%s gold`6, and puts it in her pocket.",$bank);
				}
			break;
			case 21: case 22:
				output("`\$'This is a stick-up! Everybody on the floor!'`6 cries out a bunch of armed men carrying Large Swords.");
				output("`n`nNot putting up with this, you decide to attack!");
				addnav("Fight Bank Robbers","runmodule.php?module=dragoneggs&op=bank21");
				blocknav("bank.php");
				blocknav("village.php");
			break;
			case 23: case 24:
				if (is_module_active("bakery")) $bakery=translate_inline("Hara's Bakery");
				else $bakery=translate_inline("the Bakery");
				output("A homeless man is standing in front of the bank asking for some money to get food at %s.",$bakery);
				output("Would you like to give `^100 gold`6 to the cause?");
				if ($session['user']['gold']>=100) addnav("Yes","runmodule.php?module=dragoneggs&op=bank23&op2=1");
				addnav("No","runmodule.php?module=dragoneggs&op=bank23&op2=2");
				blocknav("bank.php");
				blocknav("village.php");
			break;
			case 25: case 26:
				output("You find an old friend standing in line and strike up a conversation.");
				output("`#'Are you heading anywhere in particular?'`6 he asks.");
				output("`n`nIf you are, pick a location and head out.  You'll perform a research turn looking for dragon eggs at the new location.");
				output("`n`nWhere would you like to go?");
				increment_module_pref("researches",-1);
				addnav("Travel");
				dragoneggs_navs();
				blocknav("runmodule.php?module=dragoneggs&op=bank&op3=nav");
				addnav("Stay");
				addnav("Ye Olde Bank","runmodule.php?module=dragoneggs&op=bank25");
				blocknav("bank.php");
				blocknav("village.php");
			break;
			case 27: case 28:
				output("A man standing in front of the bank asks for some spare change. Will you give him some gold?");
				if ($session['user']['gold']>=50) addnav("Give 50 gold","runmodule.php?module=dragoneggs&op=bank27&op2=50");
				else output("`n`nNot having even `^50 gold`6 you realize the choice is already made for you.");
				if ($session['user']['gold']>=100) addnav("Give 100 gold","runmodule.php?module=dragoneggs&op=bank27&op2=100");
				if ($session['user']['gold']>=200) addnav("Give 200 gold","runmodule.php?module=dragoneggs&op=bank27&op2=200");
				if ($session['user']['gold']>=500) addnav("Give 500 gold","runmodule.php?module=dragoneggs&op=bank27&op2=500");
				addnav("No","runmodule.php?module=dragoneggs&op=bank27&op2=1");
				blocknav("bank.php");
				blocknav("village.php");
			break;
			case 29: case 30: case 31: case 32: case 33: case 34: case 35:
				output("You don't find anything of value.");
			break;
			case 36:
				dragoneggs_case36();
			break;
		}
	}
	addnav("Continue Banking","bank.php");
	villagenav();
}
?>