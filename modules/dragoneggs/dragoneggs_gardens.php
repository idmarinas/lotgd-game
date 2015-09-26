<?php
function dragoneggs_gardens(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("The Gardens");
	output("`c`b`2The Gardens`b`c`7`n`n");
	$open=get_module_setting("gardensopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("gardensmin") && get_module_setting("gardenslodge")>0 && get_module_pref("gardensaccess")==0){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("gardensmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("gardenslodge")>0 && get_module_pref("gardensaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`7You're out of research turns for today.");
	}else{
		output("`7You decide to look for Dragon Eggs at the Gardens and head across the pond in a canoe to a small isle.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(17){
			case 1: case 2: 
				output("As you walk the trail on the isle, something strange brushes against you. Your arm goes cold... what is going on???`n`n");
				output("You feel a chill.");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if ($session['user']['hitpoints']>4){
					output("Your `\$hitpoints drop by 10%`7.");
					$session['user']['hitpoints']=round($session['user']['hitpoints']*.9);
					$debug="lost 10% of hitpoints while researching in the gardens.";
				}
				if (($level<=10 && $chance<5) || ($level>10 && $chance<4) && $session['user']['turns']>0){
					output("Your energy is drained. You `@lose a turn`7.");
					$debug.="lost 1 turn while researching in the gardens.";
					$session['user']['turns']--;
				}
				debuglog($debug);
			break;
			case 3: case 4:
				output("You round a corner and hear voices!");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $chance<4) || ($level<=10 && $chance<3)){
					output("Will you try to overhear what they're saying?");
					addnav("Listen","runmodule.php?module=dragoneggs&op=gardens3");
				}else output("You can't make out what they're saying though, so you move on.");
			break;
			case 5: case 6:
				output("You don't find anything interesting and decide to return to the Gardens. As you climb into the canoe you hear a terrible voice whisper `4`i'Roooseeee Buuuuudd!!'`i`7.`n`n");
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if (($level>6 && $chance<4) || ($level<=6 && $chance<3)){
					output("You realize that `4'Rosebud'`7 is the name of the canoe!! You row to shore as quickly as possible and get out.");
				}else{
					output("That just totally creeps you out. What could it mean??? You're `iCursed`i!!!");
					if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their curse by 5 rounds while researching dragon eggs at the Gardens.");
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
						debuglog("was cursed while researching dragon eggs at the Gardens.");
					}
				}
			break;
			case 7: case 8:
				output("As you wander around the isle, you look up to see the sky. A new constellation appears in the sky. In fact, everything is different.");
				output("`n`nIt's enough to drive someone insane.");
				if (isset($session['bufflist']['insanity'])) {
					output("However, you're already insane so it really doesn't bother you.");
				}else{
					output("`n`nYup, you go insane.");
					output("`n`nBut your insanity causes you to look at the ground a lot and you `%gain a gem`7.");
					$session['user']['gems']++;
					debuglog("gained a gem with a non-harmful insanity buff while researching dragon eggs at the Gardens.");
					apply_buff('insanity',
						array("name"=>translate_inline("`@I`%n`!s`#a`Qn`^i`4t`&y"),
							"rounds"=>-1,
							"wearoff"=>translate_inline("You're sanity returns."),
							"roundmsg"=>translate_inline("This is insane!!!"),
						)
					);
				}
			break;
			case 9: case 10:
				output("You step under a huge oak tree and a storm starts. It's difficult to hear anything and you see lightning strike! You suddenly go blind!`n`n");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
					output("Luckily, you maintain your sanity and survive until the storm passes and your sight returns.");
				}else{
					output("You can't take it! You attack the tree and a branch falls on your shoulder pinning you to the ground.");
					if ($session['user']['turns']>0){
						$session['user']['turns']--;
						$debug="lost a turn, ";
						output("You struggle and `@lose a turn`7.");
					}
					output("You find yourself with only `\$1 hitpoint`7 before you can escape the tree.");
					$session['user']['hitpoints']=1;
					$debug.="lost all hitpoints except 1 while researching dragon eggs at the Gardens.";
					debuglog($debug);
				}
			break;
			case 11: case 12:
				output("Finding a nice place to relax, you settle down under a weeping willow tree. As you sleep, you feel the branches start to close in on you.");
				output("`n`nThe tree strangles you! You can't breath. You're dieing.");
				output("`n`nSoon enough, your dead corpse is left beneath the tree. You go towards the light. Perhaps you'll learn something new there...");
				output("`n`nSuddenly, you wake with a start... something is stabbing you in the back. It was all a dream; wasn't it?");
				output("`n`nYou suddenly feel knowledge of the world flood through you. You look around and find that the pointy thing stabbing you was a gem. You `%gain 1 gem`7 and you `&improve your specialty`7!`n");
				require_once("lib/increment_specialty.php");
				increment_specialty("`&");
				$session['user']['gems']++;
				debuglog("gained a gem and incremented specialty while researching dragon eggs at the Gardens.");
			break;
			case 13: case 14:
				output("You find yourself standing in the middle of the isle facing a lake. You see a canoe. You get in the canoe and row across the lake.");
				rawoutput("<big><big>");
				output("`n`n`&You find yourself standing in the middle of a different isle facing a new lake. You see a canoe. You get in the canoe and row across the lake...");
				rawoutput("<small><small>");
				output("`n`n`7You find yourself standing in the middle of a different isle facing a new lake. You see a canoe. You get in the canoe and row across the lake...");
				rawoutput("<small><small>");
				output("`n`n`)You find yourself standing in the middle of a different isle facing a new lake. You see a canoe. You get in the canoe and row across the lake... Does this go on forever???`n`n`7");
				rawoutput("</small></small>");
				if ($session['user']['turns']>1){
					output("You find yourself turning around to go back, having spent `@2 turns`7 at this odd game of infinity. It's lucky; playing with infinity can often cost a lot more than just 2 turns!");
					$session['user']['turns']-=2;
					debuglog("lost 2 turns while researching dragon eggs at the Gardens.");
				}elseif ($session['user']['gems']>1){
					$session['user']['gems']-=2;
					output("You are gemless... with `%2 less gems`7.");
					debuglog("lost 2 gems while researching dragon eggs at the Gardens.");
				}else{
					if (isset($session['bufflist']['batty'])) {
						output("Oh, not this again! You're already batty from this last time! Oh well.");
					}else{
						output("You find yourself with extra time on your hands. In fact, you have `@5 extra turns`7!");
						output("`n`nUnfortunately, this little exercise has driven you a bit batty for the rest of the day. You probably shouldn't do any more fighting.");
						$session['user']['turns']+=5;
						apply_buff('batty',
							array("name"=>translate_inline("Batty"),
								"rounds"=>-1,
								"wearoff"=>translate_inline("You're no longer so batty."),
								"atkmod"=>0.3,
								"defmod"=>0.3,
								"roundmsg"=>translate_inline("You're too batty to fight in this condition!"),
							)
						);
						debuglog("gained 5 turns but became batty while researching dragon eggs at the Gardens.");
					}
				}
			break;
			case 15: case 16: 
				output("When you stick your hand into a crevice to pick out something shiny, you feel a sharp poke.");
				output("`n`nSeriously, who didn't see that coming? Anyone? I didn't think so.`n`n");
				if ($session['user']['turns']>1){
					output("You find yourself a little woozy. You pass out!");
					output("`n`nYou wake up `@2 turns later`7 with a mild headache. However, you also feel smarter!");
					output("`n`nYou increase your specialty.");
					$session['user']['turns']-=2;
					require_once("lib/increment_specialty.php");
					increment_specialty("`&");
					debuglog("lost 2 turns and incremented specialty while researching dragon eggs at the Gardens.");
				}else{
					output("You draw back your hand and stare at the blood. It's just a flesh wound. No real damage.");
				}
			break;
			case 17: case 18: case 19: case 20:
				output("Time and space collapse! Oh no! Something hideous comes out of the lake!");
				blocknav("gardens.php");
				blocknav("village.php");
				addnav("Fight the `\$Swamgrythph","runmodule.php?module=dragoneggs&op=attack");
				set_module_pref("monster",12);
			break;
			case 21: case 22: case 23: case 24:
				output("You come across the largest man you've ever seen; arms the size of tree trunks and a glare that would freeze a charging elephant. He's wearing a mask and a cape and you don't know why.`n`n");
				$chance=e_rand(1,6);
				if (isset($session['bufflist']['ally'])) {
					if ($session['bufflist']['ally']['type']=="jackdequin") $chance=9;
				}
				$level=$session['user']['level'];
				if (($level>10 && $chance<=2) || ($level<=10 && $chance<=1)){
					output("He looks dangerous but you decide to take a chance and introduce yourself.");
					output("`4'My name is `bJack DeQuinn`b. I can help you,'`7 he says.");
					if (isset($session['bufflist']['ally'])) {
						output("`n`nRealizing that you've found help from someone new, %s`7 decides to leave.",$session['bufflist']['ally']['name']);
					}
					apply_buff('ally',array(
						"name"=>translate_inline("`4Jack DeQuin"),
						"rounds"=>10,
						"wearoff"=>translate_inline("`4Jack DeQuin leaves."),
						"atkmod"=>3,
						"survivenewday"=>1,
						"type"=>"jackdequin",
					));
					output("`n`n`4Jack DeQuin`7 is truly a powerhouse; but you don't think he has the endurance to stay in a fight very long.");
					debuglog("gained the help of ally Jack DeQuin, 1 max hitpoint, and 3 turns by researching at the Gardens.");
					if (is_module_active("dlibrary")){
						if (get_module_setting("ally10","dlibrary")==0){
							set_module_setting("ally10",1,"dlibrary");
							addnews("%s`^ was the first person to meet `4Jack DeQuinn`^ at the Gardens.",$session['user']['name']);
						}
					}
					output("You feel energized by his presence. You `@gain 3 turns`7 and `\$1 permanent hitpoint.");
					$session['user']['turns']+=3;
					$session['user']['maxhitpoints']+=3;
					$session['user']['hitpoints']+=3;
				}else{
					output("He looks dangerous so you decide to move on.");
				}
			break;
			case 25: case 26: case 27: case 28:
				if (get_module_pref("dragoneggs","dragoneggpoints")>0){
					output("A strange man dressed in a flowing robe taps you on the shoulder. You shudder at the touch and prepare for battle.");
					output("`n`n`#'Hold your %s`#. I just wish to talk,'`7 he says. `#'I need a dragon egg point from you and I'm willing to pay you handsomely.'",$session['user']['weapon']);
					output("`n`n`7He offers you the following for `&One Dragon Egg Point`7:");
					output("`n`n`11: `^500 gold`7 and `@2 turns");
					output("`n`22: `\$1 permanent hitpoint point`7 and `@1 turn");
					output("`n`33: `@3 turns`7 and `%1 gem");
					output("`n`44: `&Increase Attack by 1");
					addnav("Dragon Egg Point Exchange");
					addnav("Money + 2 Turns","runmodule.php?module=dragoneggs&op=gardens25&op2=1");
					addnav("Hitpoint + Turn","runmodule.php?module=dragoneggs&op=gardens25&op2=2");
					addnav("3 Turns + Gem","runmodule.php?module=dragoneggs&op=gardens25&op2=3");
					addnav("Increase Attack","runmodule.php?module=dragoneggs&op=gardens25&op2=4");
					if (get_module_pref("retainer")==0) {
						output("`n`55: `^Get a Retainer");
						addnav("Get a Retainer","runmodule.php?module=dragoneggs&op=gardens25&op2=5");
					}
					addnav("Leave");
				}else{
					output("You find a strange glowing rock. As you touch it, you feel your energy increase!");
					output("`n`nYou `@gain 2 turns`7!!");
					$session['user']['turns']+=2;
					debuglog("gained 2 turns while researching dragon eggs at the Gardens.");
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
	addnav("Return to the Gardens","gardens.php");
	villagenav();
}
?>