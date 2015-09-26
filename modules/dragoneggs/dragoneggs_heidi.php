<?php
function dragoneggs_heidi(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Heidi's Place");
	output("`c`b`&Heidi, the Well-wisher`b`c`7`n");
	//If the player isn't in the right city, move them to it.
	if ($session['user']['location']!= get_module_setting("heidiloc","heidi")){
		$session['user']['location'] = get_module_setting("heidiloc","heidi");
	}
	$open=get_module_setting("heidiopen");
	if (get_module_pref("puzzlepiece")==2){
		redirect("runmodule.php?module=dragoneggs&op=heidi9");
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("heidimin") && get_module_setting("heidilodge")>0 && get_module_pref("heidiaccess")==0){
		output("You don't have enough `@Green Dragon Kills`7 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("heidimin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("heidilodge")>0 && get_module_pref("heidiaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`7You're out of research turns for today.");
	}else{
		output("`7You decide to look for Dragon Eggs at Heidi's Place.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(25){
			case 1: case 2:
				output("You find a powerful spell in a back room. Will you read it?");
				$rand=e_rand(1,4)+1;
				addnav("Read the Spell","runmodule.php?module=dragoneggs&op=heidi1&op2=$rand");
			break;
			case 3: case 4:
				output("You see 5 levers on the wall.  Will you pull one?");
				addnav("Levers");
				addnav("Lever 1","runmodule.php?module=dragoneggs&op=heidi3&op2=1");
				addnav("Lever 2","runmodule.php?module=dragoneggs&op=heidi3&op2=2");
				addnav("Lever 3","runmodule.php?module=dragoneggs&op=heidi3&op2=3");
				addnav("Lever 4","runmodule.php?module=dragoneggs&op=heidi3&op2=4");
				addnav("Lever 5","runmodule.php?module=dragoneggs&op=heidi3&op2=5");
				addnav("Leave");
			break;
			case 5: case 6:
				output("You hear some strange noises in the back room and ask Heidi what's going on.");
				output("`n`n`&'Research.  I'm going to figure out how to destroy the `@Green Dragon`& once and for all.  I wouldn't go back there if I were you. I don't think you can handle it,'`7 she explains.");
				output("`n`n`#'I know what I'm ready to see,'`7 you tell her a bit indignantly.");
				output("`n`nYou open the door and see a creature not of this earth on a dissection table.  Its abdomen is cut open and a surgeon is performing a vivisection as the creature struggles.`n`n");
				$session['user']['turns']+=2;
				switch(e_rand(1,3)){
					case 1:
						if (isset($session['bufflist']['nausea'])) {
							$session['bufflist']['nausea']['rounds'] += 5;
							output("You become even more `#nauseated`7 by the whole experience and your abilities will suffer because of it but also `@gain 1 turn`7.");
							debuglog("increased the nausea buff by 5 turns and gained 1 turn while researching dragon eggs at Heidi's Place.");
							$session['user']['turns']--;
						}else{
							apply_buff('nausea',array(
								"name"=>translate_inline("Nausea"),
								"rounds"=>10,
								"wearoff"=>translate_inline("`QThe nausea fades."),
								"roundmsg"=>translate_inline("`qYou taste bile in your mouth."),
								"defmod"=>.9,
							));
							output("You are extremely `#nauseated`7 by the whole experience and your abilities will suffer because of it but also `@gain 2 turns`7.");
							debuglog("gained a nausea buff and 2 turns while researching dragon eggs at Heidi's Place.");
						}
					break;
					case 2:
						if (isset($session['bufflist']['dizzy'])) {
							$session['bufflist']['dizzy']['rounds'] += 6;
							output("You become even more `#dizzy`7 by the whole experience and your abilities will suffer because of it but also `@gain 1 turn`7.");
							debuglog("increased the dizzy buff by 6 turns and gained 1 turn while researching dragon eggs at Heidi's Place.");
							$session['user']['turns']--;
						}else{
							apply_buff('dizzy',array(
								"name"=>translate_inline("Dizziness"),
								"rounds"=>12,
								"wearoff"=>translate_inline("`QThe dizziness fades."),
								"roundmsg"=>translate_inline("`qYou feel the world spinning."),
								"atkmod"=>.8,
							));
							output("You become extremely `#dizzy`7 from the whole experience and your abilities will suffer because of it but also `@gain 2 turns`7.");
							debuglog("gained a dizziness buff and 2 turns while researching dragon eggs at Heidi's Place.");
						}
					break;
					case 3:
						if (isset($session['bufflist']['fatigue'])) {
							$session['bufflist']['fatigue']['rounds'] += 4;
							output("You become even more `#fatigued`7 by the whole experience and your abilities will suffer because of it but also `@gain 1 turn`7.");
							debuglog("increased the fatigue buff by 4 turns and gained 1 turn while researching dragon eggs at Heidi's Place.");
							$session['user']['turns']--;
						}else{
							apply_buff('fatigue',array(
								"name"=>translate_inline("Fatigue"),
								"rounds"=>8,
								"wearoff"=>translate_inline("`QThe fatigue fades."),
								"roundmsg"=>translate_inline("`qYou feel physically exhausted."),
								"atkmod"=>.8,
								"defmod"=>.9,
							));
							output("You are extremely `#fatigued`7 by the whole experience and your abilities will suffer because of it but also `@gain 2 turns`7.");
							debuglog("gained a fatigue buff and 2 turns while researching dragon eggs at Heidi's Place.");
						}
					break;
				}
			break;
			case 7: case 8:
				output("You charge through Heidi's Place looking for something suspicious.  Before you have a chance to hear Heidi's warning, you enter the basement.");
				output("`n`nThe lights don't turn on.`n`nThen, the room is lit by two glowing red eyes.");
				output("`n`nYou find yourself face-to-face with a very grotesque `\$`bRat-Thing`b`7.");
				set_module_pref("monster",9);
				addnav("Fight the Rat-Thing","runmodule.php?module=dragoneggs&op=attack");
				blocknav("runmodule.php?module=heidi");
				blocknav("village.php");
			break;
			case 9: case 10:
				if (get_module_pref("puzzlepiece")==0){
					output("You examine one of the shelves in a back room and see a puzzle-box.");
					output("`n`nWould you like to spend a turn trying to assemble the puzzle?");
					if ($session['user']['turns']>0) addnav("Assemble Puzzle","runmodule.php?module=dragoneggs&op=heidi9");
					else output("`n`nUnfortunatetly, you don't have any turns left to spend completing the puzzle.");
				}else{
					if (e_rand(1,2)==1 && $session['user']['gems']>0){
						output("You `%drop one of your gems`7.");
						$session['user']['gems']--;
						debuglog("lost a gem while researching dragon eggs at Heidi's Place");
					}else{
						output("You find a `%gem`7!");
						$session['user']['gems']++;
						debuglog("got a gem while researching dragon eggs at Heidi's Place");
					}
				}
			break;
			case 11: case 12:
				output("One of the patrons in Heidi's Place takes interest in you and asks if you'd be interested in examining an artifact that was taken out of an Egyptian Pyramid. `n`nYou take the artifact in your hand and");
				if ($session['user']['specialty']!=""){
					$mods = modulehook("specialtymodules");
					$name = $mods[$session['user']['specialty']];
					if (get_module_pref("skill",$name)<=10){
						output("read some of the strange writing on the bottom.  A power flows through you.`n");
						debuglog("incremented specialty by reading an ancient artifact while researching dragon eggs at Heidi's Place.");
						require_once("lib/increment_specialty.php");
						increment_specialty("`@");
					}else{
						output("read the strange writing but it doesn't make sense to you.  You hand the artifact back and shrug.");
					}
				}else{
					output("try to read it.  You can't focus.`n`n");
					output("`7You have no direction in the world, you should rest and make some important decisions about your life.`0`n");
				}
			break;
			case 13: case 14:
				output("You ask Heidi about the workshop in the back of her shop and she leads you into the back room.");
				output("`n`nThere, you notice a student pounding on a strange device.  With a hint of scepticism, you ask to tell you more about it.");
				output("`n`n`@'It's a dimensional transmorgifier,'`7 he says, `@'Trust me, this will save our kingdom for sure!  The only problem is I'm struggling to figure out the solution to a little problem I'm having.  Will you help?'");
				addnav("Help the Student","runmodule.php?module=dragoneggs&op=heidi13");
			break;
			case 15: case 16:
				output("Ah, a new book that you've never seen before!");
				output("You become engrossed in it and feel focused like you've never been before.  You `@gain 1 turn`7 with the renewed energy!");
				output("`n`nUnfortunately, you notice one of the shoppers trying to steal from you. Not one to put up with these shenanigans, you turn to fight.");
				$session['user']['turns']++;
				addnav("Fight the Pick-Pocket","runmodule.php?module=dragoneggs&op=heidi15");
				blocknav("runmodule.php?module=heidi");
				blocknav("village.php");
			break;
			case 17: case 18:
				output("Heidi introduces you to her team of researchers. `&'We're trying to save the Kingdom our own way,'`7 she explains. You ask to see more and one of the researchers is about to hand you a very ugly statue.");
				output("`n`nDo you take the statue?");
				addnav("Take the Statue","runmodule.php?module=dragoneggs&op=heidi17");
			break;
			case 19: case 20:
				output("You see a sandwich on the counter.  Nobody is looking so you decide to eat it.");
				output("`n`nIt's the BEST SANDWICH EVER!!! You are `iBlessed`i.");
				if ($session['bufflist']['blesscurse']['atkmod']==1.2) {
					$session['bufflist']['blesscurse']['rounds'] += 5;
					debuglog("increased their blessing by 5 rounds by researching at Heidi's Place.");
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
					debuglog("received a blessing by researching at Heidi's Place.");					
				}
			break;
			case 21: case 22:
				output("You see a strange brew bubbling in a cup.  Will you drink it?");
				addnav("Drink the Brew","runmodule.php?module=dragoneggs&op=heidi21");
			break;
			case 23: case 24:
				output("You start looking in a closet and Heidi gets upset with you.");
				output("`n`n`&'Get out of my store!'`7 she says.");
				blocknav("runmodule.php?module=heidi");
			break;
			case 25: case 26:case 27: case 28:
				output("You bump into a VERY large gentleman and apologize.  He looks on you kindly.");
				output("`#'It's quite alright.  But I would like to offer you a challenge.  Would you like to arm wrestle?'`7 he asks.");
				addnav("Wrestle the Stranger","runmodule.php?module=dragoneggs&op=heidi25");
			break;
			case 29: case 30: case 31: case 32: case 33: case 34: case 35:
				output("You don't find anything of value.");
			break;
			case 36:
				dragoneggs_case36();
			break;
		}
	}
	addnav("Return to Heidi's Place","runmodule.php?module=heidi");
	villagenav();
}
?>