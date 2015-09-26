<?php
function rumors_getmoduleinfo(){
	$info = array(
		"name"=>"Rumors",
		"version"=>"1.01",
		"author"=>"DaveS",
		"category"=>"Dragon Expansion",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1362",
		"settings"=>array(
			"Rumors,title",
			"mindk"=>"Minimum number of dks above Base (from Dragon Eggs Module) before encountering rumors:,int|1",
			"level1"=>"ID Of Level 1 Dragon Sympathizer:,int|0",
			"level2"=>"ID Of Level 2 Dragon Sympathizer:,int|0",
			"level3"=>"ID Of Level 3 Dragon Sympathizer:,int|0",
			"level4"=>"ID Of Level 4 Dragon Sympathizer:,int|0",
			"level5"=>"ID Of Level 5 Dragon Sympathizer:,int|0",
			"level6"=>"ID Of Level 6 Dragon Sympathizer:,int|0",
			"level7"=>"ID Of Level 7 Dragon Sympathizer:,int|0",
			"level8"=>"ID Of Level 8 Dragon Sympathizer:,int|0",
			"level9"=>"ID Of Level 9 Dragon Sympathizer:,int|0",
			"level10"=>"ID Of Level 10 Dragon Sympathizer:,int|0",
			"level11"=>"ID Of Level 11 Dragon Sympathizer:,int|0",
			"level12"=>"ID Of Level 12 Dragon Sympathizer:,int|0",
			"level13"=>"ID Of Level 13 Dragon Sympathizer:,int|0",
			"level14"=>"ID Of Level 14 Dragon Sympathizer:,int|0",
			"level15"=>"ID Of Level 15 Dragon Sympathizer:,int|0",
			"level16"=>"ID Of Level 16 Dragon Sympathizer:,int|0",
		),
		"prefs"=>array(
			"Rumors,title",
			"rumors"=>"Which Rumor is the player investigating?,int|0",
			"solved"=>"Number of Rumors Solved:,int|0",
			"progress"=>"What is the progress on the rumor?,int|0",
		),
		"requires"=>array(
			"dragoneggs"=>"1.0|Dragon Eggs Expansion by DaveS",
		),
	);
	return $info;
}
function rumors_install(){
	require_once("modules/rumors/rumors_install.php");
}
function rumors_uninstall(){
	for ($i=1;$i<=16;$i++) {
		$creaturename=array("","Dragon Sympathizer Initiate","Dragon Sympathizer Entrant","Low Dragon Sympathizer","Apprentice Dragon Sympathizer","Steward Dragon Sympathizer","Warden Dragon Sympathizer","Master Dragon Sympathizer","Outer Circle Dragon Sympathizer","Inner Circle Dragon Sympathizer","Dragon Sympathizer Assassin","Bishop Dragon Sympathizer","Deacon Dragon Sympathizer","Dragon Sympathizer High Master","Dragon Sympathizer Grandmaster","Dragon Sympathizer High Priest","Dragon Sympathizer High Priestess");
		$id=get_module_setting("level".$i);
		$sql = "DELETE FROM ".db_prefix("creatures")." where creatureid=$id";
		db_query($sql);
		output("`i`4Uninstalled `\$%s`4; Creature ID = `^%s`n`i",$creaturename[$i],$id);
	}
	return true;
}
function rumors_dohook($hookname,$args){
	global $session;
	$op = httpget("op");
	$op2= httpget("op2");
	require("modules/rumors/dohook/$hookname.php");
	return $args;
}

function rumors_run(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Rumors");
if ($op!= "rumor") {
	require_once "lib/forcednavigation.php";
	do_forced_nav(false, false);
}
if ($op=="rumors"){
	output("`c`b`^Rumors`b`c`n");
	output("Because of the dangers from the increase in searching for `@Dragon Eggs`^, there almost always seems to be a rumor detailing different problems.  It's your job to dispel these rumors.`n`n");
	if (get_module_pref("rumors")==0 && $session['user']['dragonkills']>= get_module_setting("mindk")+get_module_setting("mindk","dragoneggs")==0){
		output("After you have defeated at least one `@Green Dragon`^ you may have a chance of overhearing a rumor while searching for dragon eggs.");
	}elseif (get_module_pref("rumors")==0){
		output("You have not overheard a rumor yet.  Keep searching for `&Dragon Eggs`^ at different locations and eventually you'll hear a rumor!");
	}elseif (get_module_pref("rumors")>0){
		output("Rumors need to be solved before you kill a `@Green Dragon`^ or else you will forget which rumor you were working on. You may only complete one rumor per `@Green Dragon`^ kill.`n`n");
		output("`QCurrently, you're investigating the following rumor:`\$`n`n");
		rumors_rumor();
	}elseif (get_module_pref("rumors")==-1){
		output("Congratulations on recently dispelling a rumor.  You may encounter another rumor after you kill the `@Green Dragon`^ but it's unlikely that you'll encounter a new rumor until then.");
	}
	villagenav();
}
if ($op=="rumor1"){
	page_header("Healer's Hut");
	output("`c`b`#Healer's Hut`b`c`n");
	if ($op2==""){
		output("You go to donate blood and the nurse asks you many very personal questions to screen you.");
		if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
			output("The nurse explains that you have to be at full health before you can donate.");
			output("`n`nYou are told that you're more than welcome to come back when you're feeling better.");
			require_once("lib/forest.php");
			forest(true);
		}else{
			$donate=$session['user']['maxhitpoints']-1;
			output("`n`nYou answer truthfully and she decides to accept you as a donor.");
			output("You can give `^%s`\$ hitpoints`# worth of blood to help end the blood shortage.`n`nAre you ready?",$donate);
			addnav("Donate","runmodule.php?module=rumors&op=rumor1&op2=donate&op3=$donate");
			addnav("Leave","runmodule.php?module=rumors&op=rumor1&op2=donate");
		}
	}elseif ($op2=="donate"){
		if ($op3>0){
			output("You are hooked up to a machine and you watch the blood leave you.`n`n");
			increment_module_pref("progress",$op3);
			$session['user']['hitpoints']=1;
			if (get_module_pref("progress")>=500){
				increment_module_pref("solved",1);
				set_module_pref("rumors",-1);
				set_module_pref("progress",0);
				output("The nurse disconnects you and smiles. `3'You've helped replenish the blood supply. Thank you!'`# she says.");
				output("`n`n`c`b`@Congratulations`b`c`n`#");
				output("For ending the rumor about the `\$Blood Supply`# you are rewarded `&5 Charm points`#.");
				$session['user']['charm']+=5;
				addnews("%s`\$ dispelled the rumors about the `#Healer's Hut`\$ running out of blood by giving Blood at the Hospital.",$session['user']['name']);
				debuglog("gained 5 charm by dispelling the Blood Rumor and giving a total of 500 hitpoints to the blood bank.");
			}else{
				$left=500-get_module_pref("progress");
				output("The nurse disconnects you and smiles. `3'Thank you for giving today.'`# She puts a little sticker on you that says `\$`iBe nice to me. I gave blood today`#`i.");
				output("`n`nOnly `^%s`# more hitpoints to give until you've dispelled the rumor!",$left);
				debuglog("gave $op3 hitpoints to help dispel the Blood Rumor.");
			}
		}else{
			output("You feel that giving just 'isn't your thing' and leave.");
		}
		require_once("lib/forest.php");
		forest(true);
	}
}
if ($op=="rumor2"){
	page_header("Bluspring's Warrior Training");
	output("`c`b`7Bluspring's Warrior Training`b`c");
	if ($op2==""){
		output("You arrive at Bluspring's Warrior Training to help with recruiting.");
		output("In order to show how much of a difference new warriors can make, you will need to donate `&1 Dragon Egg Point`7 and `%5 Gems`7.`n`n");
		if (get_module_pref("dragoneggs","dragoneggpoints")<=0 || $session['user']['gems']<5){
			if (get_module_pref("dragoneggs","dragoneggpoints")<=0) output("Unfortunately, you don't have a `&Dragon Egg Point`7 to give to Bluspring's Warrior Training.`n`n");
			if ($session['user']['gems']<5) output("You don't have the `%5 gems`7 needed to inspire the students.`n`n");
			output("You'll have to come back when you're ready to make a difference.");
		}else{
			addnav("Give a Dragon Egg Point and 5 Gems","runmodule.php?module=rumors&op=rumor2&op2=give");
		}
	}elseif ($op2=="give"){
		increment_module_pref("dragoneggs",-1,"dragoneggpoints");
		$session['user']['gems']-=5;
		increment_module_pref("solved",1);
		set_module_pref("rumors",-1);
		output("You give a grand lecture in front of the new students and receive a standing ovation.`n`n");
		output("Your donation of a `&Dragon Egg Point`7 and `%5 gems`7 inspires the students to new heights.");
		output("`n`n`c`b`@Congratulations`b`c`n`7");
		output("For ending the rumor about the `\$Detective Recruiting`7 you are rewarded `&5 Charm points`7.");
		$session['user']['charm']+=5;
		addnews("%s`\$ dispelled the rumor about the city running out of new recruits by giving a lecture proving otherwise to the students at Bluspring's Warrior Training.",$session['user']['name']);
		debuglog("gained 5 charm by dispelling the Recruiting Rumor by giving 5 gems and a dragon egg point.");
	}
	addnav("Return to Bluspring's Warrior Training","train.php");
	villagenav();
}
if ($op=="rumor3"){
	page_header("Ye Olde Bank");
	output("`c`b`^Ye Olde Bank`b`c`6");
	if ($op2==""){
		output("The bank manager greets you. `@'Thank you for coming to help'`6 says `@Elessa`6.");
		output("`@'By proving that someone can win by investing money here, you'll prove that the bank is safe.  The only problem is that you'll have to give us the money for this to work.'");
		output("`n`n`6In other words, you have to `igive`i the bank the money and not get anything back even if you win???`n`n`@'Err... yes, that's how it works'`@ says `@Elessa`6.`n`n");
		if ($session['user']['gold']>=500){
			output("You put `^500 gold`6 on the table and `@Elessa`6 looks at you.  `@'Are you ready to give this a try? You have a 50% chance of winning.  Successfully invest 3 times and we can dispel the rumor.'");
			addnav("Invest 500 gold","runmodule.php?module=rumors&op=rumor3&op2=invest");
		}else{
			output("Not having `^500 gold`6, you decline to 'invest' in this little scheme today.");
		}
	}elseif ($op2=="invest"){
		output("You hand over the `^500 gold`6 and hope for the best`n`n");
		output("`@Elessa`6 picks up a quill and writes to the main branch. She casts a spell and sends the letter and looks at you.`n`n");
		output("`@'This will only take a couple of minutes,'`6 she says.  You wait a couple of minutes and a letter magically appears. `@Elessa`6 picks it up.`n`n`@");
		$session['user']['gold']-=500;
		if (e_rand(1,5)<4){
			output("She reads the letter and looks at you. `@'Congratulations! The investment was a success!'`n`n");
			increment_module_pref("progress",1);
			if (get_module_pref("progress")>=3){
				output("`c`b`@Congratulations`b`c`n`6");
				output("For ending the rumor about the `\$Bank's Solvency`6 you are rewarded `&5 Charm points`6.");
				$session['user']['charm']+=5;
				increment_module_pref("solved",1);
				set_module_pref("rumors",-1);
				set_module_pref("progress",0);
				addnews("%s`\$ dispelled the rumor about the bank losing solvency by succesfully investing at the local branch.",$session['user']['name']);
				debuglog("gained 5 charm by dispelling the Banking Rumor and giving 500 gold.");
			}else{
				output("'We can dispel the rumor if you successfully invest `^%s`@ more times,' Elessa `6tells you.",3-get_module_pref("progress"));
				debuglog("gave 500 gold with a successful investment to dispel the rumor about the bank.");
			}
		}else{
			output("'Oh, I'm so sorry,'`6 says `@Elessa`6 as she puts down the letter.");
			output("`n`nYou already know that the investment failed.  You still need to invest `^%s`6 more times to dispel the rumor.",3-get_module_pref("progress"));
			debuglog("gave 500 gold but the investment failed trying to dispel the rumor about the bank.");
		}
	}
	addnav("Continue Banking","bank.php");
	villagenav();
}
if ($op=="rumor4"){
	page_header("Merick's Stables");
	output("`c`b`^Merick's Stables`b`c`7`n");
	$turns = httppost('turns');
	if ($op2!="give"){
		output("You arrive at Merick's Stables with hopes to dispel the myth about the kingdom falling into ruins.");
		output("How many turns would you like to spend taking care of the animals? You need to spend `@%s`7 more turns to end the rumor.",25-get_module_pref("progress"));
		output("<form action='runmodule.php?module=rumors&op=rumor4&op2=give' method='POST'><input name='turns' id='turns'><input type='submit' class='button' value='Volunteer'></form>",true);
		addnav("","runmodule.php?module=rumors&op=rumor4&op2=give");
	}else{
		if ($turns<=0) $turns=0;
		elseif ($turns>$session['user']['turns']) $turns=$session['user']['turns'];
		if ($turns>25-get_module_pref("progress")) $turns=25-get_module_pref("progress");
		$session['user']['turns']-=$turns;
		increment_module_pref("progress",$turns);
		if ($turns==""){
			output("Merick appreciates you lounging around doing nothing.  Perhaps you'd actually like to spend a turn helping?");
		}elseif (get_module_pref("progress")>=25){
			output("You spend `^%s `@turns`7 taking care of the abandoned animals. You are rewarded with meows and barks of appreciation.",$turns);
			output("`n`n`c`b`@Congratulations`b`c`n`7");
			output("For ending the rumor about the kingdom being overrun by `\$Abandoned Animals`7 you are rewarded `&5 Charm points`7.");
			$session['user']['charm']+=5;
			increment_module_pref("solved",1);
			set_module_pref("rumors",-1);
			set_module_pref("progress",0);
			addnews("%s`\$ dispelled the rumor about the kingdom being overrun by Abandoned Animals.",$session['user']['name']);
			debuglog("gained 5 charm by dispelling the Abandoned Animal rumor by donating $turn turns at the Animal Rescue.");
		}else{
			output("You spend `^%s `@turns`7 taking care of the abandoned animals. You are rewarded with meows and barks of appreciation.",$turns);
			debuglog("spent $turns turns at the Animal Rescue trying to dispel a rumor.");
			output("`n`nMerick is impressed with your work.  You only have to spend `^%s`@ more turns`7 to dispel the rumor.",25-get_module_pref("progress"));
		}
	}
	addnav("Return to Merick's Stables","stables.php");
	villagenav();
}
if ($op=="rumor5"){
	page_header("Hall of Fame");
	output("`c`b`^The Hall of Fame`b`c`@`n");
	$invest = httppost('invest');
	if ($op2!="give"){
		output("You arrive at the Hall of Fame anxious to review the documents of concern.");
		output("You explain that you're an expert on the Occult and the staff lead you to the archives. This may take some time; you need to spend `&%s`@ more Dragon Egg Search Turns to end the rumor.",6-get_module_pref("progress"));
		output("`n`nHow many Egg Search Turns would you like to spend researching today?");
		output("<form action='runmodule.php?module=rumors&op=rumor5&op2=give' method='POST'><input name='invest' id='invest'><input type='submit' class='button' value='Research'></form>",true);
		addnav("","runmodule.php?module=rumors&op=rumor5&op2=give");
	}else{
		if ($invest<=0) $invest=0;
		elseif ($invest>get_module_setting("research","dragoneggs")-get_module_pref("researches","dragoneggs")) $invest=get_module_setting("research","dragoneggs")-get_module_pref("researches","dragoneggs");
		if ($invest>6-get_module_pref("progress")) $invest=6-get_module_pref("progress");
		increment_module_pref("researches",$invest,"dragoneggs");
		increment_module_pref("progress",$invest);
		if ($invest==""){
			output("The archival staff watches you play with your pencil.  This really isn't accomplishing anything, you know?");
		}elseif (get_module_pref("progress")>=6){
			output("You spend `^%s `&Dragon Egg Search Turns`@ reading the papers very carefully. You take some notes and decipher the papers.",$invest);
			output("`n`n`c`b`@Congratulations`b`c`n`@");
			output("You realize that the documents are `iNOT`i about an impending invasion. Instead, they detail a possible site for a Dragon Egg Hatch Ritual.  You're able to prevent this from happening and `&gain a Dragon Egg Point`@.");
			increment_module_pref("dragoneggs",1,"dragoneggpoints");
			increment_module_pref("dragoneggshof",1,"dragoneggpoints");
			increment_module_pref("solved",1);
			set_module_pref("rumors",-1);
			set_module_pref("progress",0);
			addnews("%s`\$ dispelled the rumor about the kingdom being destroyed by a pending invasion.",$session['user']['name']);
			debuglog("gained a Dragon Egg Point by dispelling the Impending Invasion rumor by spending $invest Dragon Egg Searches at the Hall of Fame.");
		}else{
			output("You spend `^%s `&search turns`@ studying the papers.  You feel like you've almost figured it out but it will still take some time.",$invest);
			debuglog("spent $invest research turns at the Hall of Fame trying to dispel a rumor.");
			output("`n`nThe researchers are impressed with your work.  You only have to spend `^%s`& more research turns`@ to dispel the rumor about the Impending Invasion of the Kingdom.",6-get_module_pref("progress"));
		}
	}
	addnav("Return to the Historical Society","hof.php");
	villagenav();
}
if ($op=="rumor6"){
	page_header("Gypsy Seer's Tent");
	output("`c`b`3Gypsy Seer's Tent`b`c`5`n");
	if ($op2==""){
		output("`!'Doooooom!!! DOOOOOOOOMMMMMM!!!!'`3 cries out the Gypsy as soon as she sees you.`n`n");
		output("She takes your hand and reads your palm.  `!'You bring great danger to the city.'`n`n");
		output("`!'The people are losing their way, but I need your help,'`3 she says.`n`n");
		addnav("Help the Gypsy");
		if (get_module_pref("progress")==0){
			output("`!'I can cast a spell to prevent the end, but I need a weapon with an attack of at least 10 and armor with a defense of at least 10 to cast the spell.  Will you help?'");
		}elseif (get_module_pref("progress")==1){
			output("`!'I can cast a spell to prevent the end, but I still need a weapon with an attack of at least 10 to cast the spell.  Will you help?'");
			blocknav("runmodule.php?module=rumors&op=rumor6&op2=1");
		}else{
			output("`!'I can cast a spell, but I need an armor with a defense of at least 10 to cast the spell.  Will you help?'");
			blocknav("runmodule.php?module=rumors&op=rumor6&op2=2");
		}
		addnav("Give Armor","runmodule.php?module=rumors&op=rumor6&op2=1");
		addnav("Give Weapon","runmodule.php?module=rumors&op=rumor6&op2=2");
		addnav("Leave");
	}else{
		if (($op2==1 && $session['user']['armordef']<10) || ($op2==2 && $session['user']['weapondmg']<10)){
			if ($op2==1) $item=translate_inline("armor");
			else $item=translate_inline("weapon");
			output("`!'Why are you handing me this cheap %s?'`3 asks the Gypsy.",$armor);
			if ($op2==1) output("`!'It needs to have a defense of at least `^10`!!'`3 she says.");
			else output("`!'It needs to have a damage of at least `^10`!!'`3 she says.");
		}else{
			if ($op2==1){
				output("You decide to hand over your armor. Oh what a difficult decision!");
				$give=$session['user']['armor'];
				$def=$session['user']['armordef'];
				$session['user']['defense']-=$session['user']['armordef'];
				$session['user']['armordef']=1;
				$session['user']['defense']++;
				$session['user']['armor']="T-Shirt";
				$session['user']['armorvalue']=0;
				if (get_module_pref("progress")==0) set_module_pref("progress",1);
				else set_module_pref("progress",3);
				debuglog("gave up armor ($def defense) for a T-shirt to help dispel the Gypsy Seer's Rumor.");
			}elseif ($op2==2){
				output("You decide to hand over your weapon. Oh what a difficult decision!");
				$give=$session['user']['weapon'];
				$dmg=$session['user']['weapondmg'];
				$session['user']['attack']-=$session['user']['armordef'];
				$session['user']['weapondmg']=1;
				$session['user']['attack']++;
				$session['user']['weapon']="Fists";
				$session['user']['weaponvalue']=0;
				if (get_module_pref("progress")==0) set_module_pref("progress",2);
				else set_module_pref("progress",3);
				debuglog("gave up weapon ($dmg damage) for a Fists to help dispel the Gypsy Seer's Rumor.");
			}
			output("`n`nThe Gypsy sits behind her Crystal Ball and waves her hands. The lights dim. A shot rings out.  You hear a woman scream in the distance.");
			output("`n`nYour %s`5 disappears!`n`n",$give);
			if (get_module_pref("progress")==3){
				output("The lights turn on and the Gypsy looks at you. `!'It is done.  I have prevented the end from coming!'");
				increment_module_setting("level",1,"terror");
				output("`n`n`c`b`@Congratulations`b`c`n`@");
				output("You `&Gain 5 Charm`3 and you have dispelled the rumor!");
				addnews("%s`\$ dispelled a rumor about the end of world.  Don't worry! We're safe... for now.",$session['user']['name']);
				debuglog("gained 5 charm by dispelling the End of the World rumor at the Gypsy's Tent.");
				increment_module_pref("solved",1);
				set_module_pref("rumors",-1);
				set_module_pref("progress",0);
			}else{
				output("The lights turn on and the Gypsy looks at you. `!'Yes, that has helped!");
				if (get_module_pref("progress")==1) output("Now all you need to do is deliver a weapon with damage of at least 10!'`3 she says.");
				else output("Now all you need to do is deliver armor with defense of at least 10!'`3 she says.");
			}
		}
	}
	addnav("Return to the Gypsy's Tent","gypsy.php");
	villagenav();
}
if ($op=="rumor7"){
	page_header("The Outhouse");
	output("`c`b`2The Outhouse`b`c`2`n");
	if ($op2==""){
		output("You head over to the Outhouse and find the largest `&Dragon Egg`2 you've ever seen.  It seems there may have been a bit of truth to this rumor.");
		output("`n`nYou realize that in order to destroy the egg you'll have to sacrifice your life.  Your should be able to destroy it, but the acid will cause your end.");
		if ($session['user']['turns']>=7){
			output("You will need `@seven turns`2 to complete the spell of destruction. Are you ready??");
			addnav("Sacrifice Yourself","runmodule.php?module=rumors&op=rumor7&op2=yes");
		}else{
			output("You need at least seven turns in order to destroy the egg.  You just don't have the ability to do it today.");
		}
		addnav("Return to the Forest","forest.php");
		villagenav();
	}elseif ($op2=="yes"){
		increment_module_pref("dragoneggs",1,"dragoneggpoints");
		increment_module_pref("dragoneggshof",1,"dragoneggpoints");
		increment_module_pref("solved",1);
		set_module_pref("rumors",-1);
		$session['user']['turns']-=7;
		$session['user']['hitpoints']=0;
		$session['user']['alive']=false;
		$session['user']['charm']+=5;
		addnav("Continue","shades.php");
		output("You grab the egg and start to cast the spell of destruction. The egg is getting crushed and the yolk shoots out and envelopes your body!  You realize you're dieing.");
		output("`n`n`c`b`@Congratulations`b`c`n`2");
		output("You have destroyed the egg! You `&gain a Dragon Egg Point`2, `&gain 5 Charm`2 and have ended the rumor. Sadly, it has ended your life.  The citizens of the kingdom will read about your valor in the Daily News.");
		addnews("%s`\$ dispelled a rumor by sacrificing %s life and thereby destroying a Dragon Egg.",$session['user']['name'],translate_inline($session['user']['sex']?"her":"his"));
		debuglog("gained 5 charm and a dragon egg point by sacrificing their life and 7 turns to dispel the Outhouse rumor.");
	}
}
if ($op=="rumor8"){
	page_header(array("The Jail of %s", $session['user']['location']));
	output("`c`b`^The Jail of %s`b`c`2",$session['user']['location']);
	if (is_module_active("jail")) $jail="jail";
	else $jail="djail";
	if ($op2==""){
		output("You ask about the Vampire and the Sheriff points you down a long hallway with bright beams of sunlight shooting all around.");
		output("`n`nYou find yourself standing before a cell.  Crouched down in the shadows is the Vampire.");
		output("`n`n`#'Tell me your secrets, foul creature'`7 you cry out to it.");
		output("`n`n`4'Come closer and I'll fill your dreams with nightmares,'`7 it responds.");
		output("`n`nIn order to get some answers, you'll have to syphon `^2000 experience`7 from yourself to the Vampire.");
		if ($session['user']['experience']>2000){
			addnav("Allow the Syphoning","runmodule.php?module=rumors&op=rumor8&op2=yes");
		}else output("`n`nYou just don't have the skills or experience to perform the interrogation yet. You'll have to come back later.`n`nYou turn your back to the Vampire and leave.");
	}elseif ($op2=="yes"){
		output("Realizing that you'll have to give it something, you ask the Sheriff to assist you as you perform a syphoning ritual.");
		output("`n`nSoon enough, you are standing in a protective circle.  Using the greatest control, you allow the Vampire to drain `#2,000`^ experience`7 from you.");
		output("`n`n`c`b`@Congratulations`b`c`n`7");
		output("The Vampire sucks in the experience but you halt the spell as soon as you lose `^2,000 experience`7.  Then, the REAL interrogation begins as the Vampire, high with power, starts talking.");
		output("`n`nYou listen for a short period and the sheriff gives you `%a gem`7 before you decide to let the Sheriff do his job.");
		$session['user']['experience']-=2000;
		increment_module_pref("solved",1);
		set_module_pref("rumors",-1);
		$session['user']['gems']++;
		addnews("%s`\$ dispelled the rumor about a Vampire that would destory the Kingdom.",$session['user']['name']);
		debuglog("gained 1 gem by dispelling the Vampire rumor by spending 2000 experience at the Police Station.");
	}
	addnav("Return to the Jail","runmodule.php?module=$jail");
	villagenav();
}
if ($op=="rumor9"){
	if (is_module_active("jeweler")){
		page_header("Oliver, The Jeweler");
		output("`n`b`c`&Oliver's Jewelry`b`c`n`7");
		addnav("Return to Oliver's Jewelry","runmodule.php?module=jeweler");
	}else{
		page_header("Pegasus Armor");
		output("`c`b`%Pegasus Armor`b`c`5");
		addnav("Return to Pegasus Armor","armor.php");
	}
	if (get_module_pref("progress")>0){
		output("You are not ready to dispel this rumor.  You will still need to kill `^%s `\$Dragon Sympathizers`7 before you can alleviate the current fears regarding the Dragon Sympathizers.",get_module_pref("progress"));
	}else{
		output("You hand over the Talismans that you took from the Dragon Sympathizers.");
		output("`n`n`c`b`@Congratulations`b`c`n`7");
		output("You have defeated the Dragon Sympathizers and dispelled the rumor about how powerful they are. You are rewarded `&5 Charm points`7 and `%2 gems`7.");
		$session['user']['charm']+=5;
		$session['user']['gems']+=2;
		increment_module_pref("solved",1);
		set_module_pref("rumors",-1);
		set_module_pref("progress",0);
		addnews("%s`\$ dispelled the rumor about the Dragon Sympathizers suddenly rising in power.",$session['user']['name']);
		debuglog("gained 5 charm and 2 gems by dispelling the Dragon Sympathizer Rise rumor by donating killing Dragon Sympathizers in the streets.");
	}
	villagenav();
}
if ($op == "hof") {
	page_header("Hall of Fame");
	$page = httpget('page');
	$pp = 50;
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'rumors' AND setting = 'solved' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'rumors' AND setting = 'solved' AND value > 0 ORDER BY (value+0) DESC $limit";
	$result = db_query($sql);
	$rank = translate_inline("Rank");
	$name = translate_inline("Name");
	$hofdesc = translate_inline("Rumors Dispelled");
	$none = translate_inline("No Rumors Dispelled");
	output("`b`c`^Most Rumors Dispelled`c`n`b");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$hofdesc</td></tr>");
	if (db_num_rows($result)<=0) output_notl("<tr class='trlight'><td colspan='3' align='center'>`&$none`^</td></tr>",true);
	else{
		for($i = $pageoffset; $i < $cond && $count; $i++) {
			$row = db_fetch_assoc($result);
			if ($row['name']==$session['user']['name']){
				rawoutput("<tr class='trhilight'><td>");
			}else{
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
			}
			$j=$i+1;
			output_notl("$j.");
			rawoutput("</td><td>");
			output_notl("`&%s`^",$row['name']);
			rawoutput("</td><td>");
			output_notl("`c`b`Q%s`c`b`^",$row['value']);
			rawoutput("</td></tr>");
        }
	}
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=rumors&op=hof&page=".($p/$pp+1));
		}
	}
	addnav("Other");
	addnav("Back to Historical Society", "hof.php");
	villagenav();
}
page_footer();
}
function rumors_rumor(){
	$rumors=get_module_pref("rumors","rumors");
	$op = httpget("op");
	if ($rumors==1){
		output("`b`c`\$Blood Donation Rumor`b`c`n");
		output("A surge of monsters is coming.  The `\#Healer's Hut`\$ is going to run out of `bblood`b if someone doesn't help.");
		output("You need to donate `b500 hitpoints`b worth of blood to the `\#Healer's Hut`\$.`n`n");
		output("In order to do this, you will need to go to the hut with all of your hitpoints intact and choose to `idonate`i all of them except 1. Keep doing this until the hospital has enough blood.");
		if($op=="rumors") output("`n`nYou have donated %s points so far.",get_module_pref("progress","rumors"));
	}elseif ($rumors==2){
		output("`b`c`\$Warrior Recruiting Rumor`b`c`n");
		output("The `@Green Dragon `\$is becoming more and more powerful and we're failing at recruiting warriors.");
		output("`n`nIf you can give a `&Dragon Egg Point`\$ and `%5 Gems`\$ to `0Bluspring's Warrior Training`\$ there will be renewed interest in saving the Kingdom.");
	}elseif ($rumors==3){
		output("`b`c`\$Bank Solvency Rumor`b`c`n");
		output("There's a rumor that the bank is about to lose solvency. If this happens, the Kingdom will go into a huge panic.");
		output("`n`nYou must play the stock market to help `^Ye Olde Bank`\$.");
		output("Play the stock market by going to the bank and putting `^500 gold`\$ down.  Win `^3 times`\$ to dispel the rumor.");
		if($op=="rumors") output("`n`n`cSuccessful Investments So Far: `^%s`c",get_module_pref("progress","rumors"));
	}elseif ($rumors==4){
		output("`b`c`\$Abandoned Animals Rumor`b`c`n");
		output("There is a rumor that the city is falling into ruins and being overrun by abandoned animals.  You will need to give at least `@25 turns`\$ helping take care of abandoned animals.");
		output("`n`nMeet at `&Merick's Stables`\$ to prove that this isn't a problem.");
		if($op=="rumors") output("`n`n`cTurns Spent Caring for Animals So Far: `^%s`c",get_module_pref("progress","rumors"));
	}elseif ($rumors==5){
		output("`b`c`\$Impending Invasion Rumor`b`c`n");
		output("There seems to be some important new documents unearthed that are now located at the `^Hall of Fame`\$. It is rumored that they foretell of an impending invasion.  Spend `&6 Dragon Egg Search Turns`\$ at the `^Hall of Fame`\$ to decipher the information and dispel the rumor.");
		if($op=="rumors") output("`n`n`cResearch Turns Spent Deciphering Documents So Far: `^%s`c",get_module_pref("progress","rumors"));
	}elseif ($rumors==6){
		output("`b`c`\$End of the World Rumor`b`c`n");
		output("There is a rumor that the end is coming soon.  You will have to go to the `3Gypsy Seer's Tent`\$.  You will have to give her a `&Weapon`\$ with an attack of at least `&10 attack`\$ and an `&Armor`\$ of at least `&10 defense`\$.");
		if ($op=="rumors"){
			if (get_module_pref("progress")==0) $given=translate_inline("None");
			elseif (get_module_pref("progress")==1) $given=translate_inline("Armor");
			elseif (get_module_pref("progress")==2) $given=translate_inline("Weapon");
			output("`n`n`cItems Given So Far: `^%s`c",$given);
		}
	}elseif ($rumors==7){
		output("`b`c`\$Outhouse Gate Rumor`b`c`n");
		output("An `&Elder Dragon Egg`\$ will hatch soon at the `2Outhouse`\$, making it impossible for anyone to go to the bathroom.  The only way to destroy this egg is with a selfless sacrifice.");
		output("`n`nIf you go to the Outouse with at least `@7 turns`\$ you can destroy the gate by giving your life.");
	}elseif ($rumors==8){
		output("`b`c`\$Vampire Capture Rumor`b`c`n");
		output("A vampire has been captured in the Jail.  If you let it drain you of `#2,000`\$ experience the sheriff can finally get some answers from it.");
	}
	if ($rumors==9){
		output("`b`c`\$Dragon Sympathizers on the Rise Rumor`b`c`n");
		output("The Dragon Sympathizers are becoming more and more powerful. You must thin their ranks!");
		if ($session['user']['dragonkills']<10) $number=3;
		elseif ($session['user']['dragonkills']<20) $number=4;
		else $number=5;
		if (is_module_active("jeweler")) output("`n`nKill `^%s`\$ Dragon Sympathizers in the forest and return their talismans to `&Oliver's Jewelry`\$.",$number);
		else output("`n`nKill `^%s`\$ Dragon Sympathizers in the forest and return their talismans to `%Pegasus Armor`\$.",$number);
		if($op=="rumors"){
			if (is_module_active("jeweler")) $go=translate_inline("You should go to `&Oliver's Jewelry`\$ to dispel the rumor now.");
			else $go=translate_inline("You should go to `%Pegasus Armor`\$ to dispel the rumor now.");
			if (get_module_pref("progress","rumors")>0) output("`n`n`cDragon Sympathizers needed to defeat before dispelling the Rumor: `^%s`c",get_module_pref("progress","rumors"));
			else output("`n`n`cYou have defeated enough Dragon Sympathizers.  %s`c",$go);
		}
	}
}
?>