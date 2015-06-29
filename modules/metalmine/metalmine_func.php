<?php

function metalmine_storenavs(){
	$allprefs=unserialize(get_module_pref('allprefs'));
	addnav("Peruse Pickaxes","runmodule.php?module=metalmine&op=pickaxe");
	addnav("Examine Helmets","runmodule.php?module=metalmine&op=helmets");
	if ($allprefs['helmet']>0 && $allprefs['oil']>0) addnav("Purchase Helmet Oil","runmodule.php?module=metalmine&op=helmetoil");
	addnav("Inspect Canaries","runmodule.php?module=metalmine&op=canary");
	addnav("Ask About the XXX Bottle","runmodule.php?module=metalmine&op=bottle");
	if ($allprefs['toothy']>0) addnav("Ask About Toothy McPicker","runmodule.php?module=metalmine&op=storemcp");
	addnav("Leave the Store","runmodule.php?module=metalmine&op=enter");
}
function metalmine_clearmap(){
	$allprefs=unserialize(get_module_pref('allprefs'));
	$allprefs['mazeturn']="";
	set_module_pref('allprefs',serialize($allprefs));
	clear_module_pref("maze");
	clear_module_pref("pqtemp");
}
function metalmine_fight($op) {
	$temp=get_module_pref("pqtemp");
	page_header("Fight");
	global $session,$badguy;
	$op2 = httpget('op2');
	if ($op=='welltroll'){
		$name=translate_inline("Under the Well Troll");
		$weapon=translate_inline("Slimy hands");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$session['user']['level']+1,
			"creatureweapon"=>$weapon,
			"creatureattack"=>($session['user']['attack']+e_rand(0,2))*1.2,
			"creaturedefense"=>($session['user']['defense']+1)*1.1,
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.1),
			"diddamage"=>0,
			"type"=>"Troll1");
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=='toothy'){
		$name=translate_inline("Toothy McPicker");
		$weapon=translate_inline("Chomper the Tooth");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$session['user']['level'],
			"creatureweapon"=>$weapon,
			"creatureattack"=>($session['user']['attack']+e_rand(0,2))*.9,
			"creaturedefense"=>($session['user']['defense']-1),
			"creaturehealth"=>round($session['user']['maxhitpoints']*.88),
			"diddamage"=>0,
			"type"=>"Ghost");
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=='cavetroll'){
		$name=translate_inline("Cave Troll");
		$weapon=translate_inline("Infected Claws");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$session['user']['level'],
			"creatureweapon"=>$weapon,
			"creatureattack"=>($session['user']['attack']+e_rand(2,4))*.8,
			"creaturedefense"=>($session['user']['defense']+e_rand(1,2))*.8,
			"creaturehealth"=>round($session['user']['maxhitpoints']*.8),
			"diddamage"=>0,
			"type"=>"Troll2");
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=='mummy'){
		$name=translate_inline("a Grumpy Mummy");
		$weapon=translate_inline("Whipping Mummy Wrap");
		$badguy = array(
			"type"=>"",
			"creaturename"=>$name,
			"creaturelevel"=>$session['user']['level']+1,
			"creatureweapon"=>$weapon,
			"creatureattack"=>($session['user']['attack']+e_rand(1,3))*.87,
			"creaturedefense"=>($session['user']['attack']+e_rand(1,3))*.92,
			"creaturehealth"=>round($session['user']['maxhitpoints']*.96),
			"diddamage"=>0,
			"type"=>"Undead",
		);
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=='miner'){
		$name=translate_inline("a Burly Miner");
		$weapon=translate_inline("Flying Fists");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$session['user']['level'],
			"creatureweapon"=>$weapon,
			"creatureattack"=>$session['user']['attack']*.9,
			"creaturedefense"=>$session['user']['attack']*.9,
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.1),
			"diddamage"=>0,
			"type"=>"Human",
		);
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=="bear"){
		$name=translate_inline("Great Big Bear");
		$weapon=translate_inline("its Claws");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$session['user']['level']+1,
			"creatureweapon"=>$weapon,
			"creatureattack"=>round($session['user']['attack']*.85),
			"creaturedefense"=>round($session['user']['defense']*1.3),
			"creaturehealth"=>round($session['user']['maxhitpoints']*0.98),
			"diddamage"=>0,
			"type"=>"bear",
		);
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=="player"){
		$sql = "SELECT acctid,name,weapon,level,maxhitpoints,attack,defense FROM ".db_prefix("accounts")." WHERE acctid='$op2'";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		$name = $row['name'];
		if ($row['weapon']>"") $weapon = $row['weapon'];
		else $weapon=translate_inline("Sword");
		if ($row['level']>0) $level = $row['level'];
		else $level=$session['user']['level'];
		if ($row['attack']>0) $attack = $row['attack'];
		else $attack=$session['user']['attack'];
		if ($row['defense']>0) $defense = $row['defense'];
		else $defense=$session['user']['defense'];
		if ($row['hitpoints']>0) $hitpoints= $row['maxhitpoints'];
		else $hitpoints=$session['user']['maxhitpoints'];
		$id = $row['acctid'];
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['id']=$id;
		set_module_pref('allprefs',serialize($allprefs));
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$level,
			"creatureweapon"=>$weapon,
			"creatureattack"=>$attack,
			"creaturedefense"=>$defense,
			"creaturehealth"=>$hitpoints,
			"diddamage"=>0,
			"type"=>"player",
		);
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=="fight"){
		global $badguy;
		$battle=true;
		$fight=true;
		if ($battle){
			require_once("battle.php");
			if ($victory){
				$allprefs=unserialize(get_module_pref('allprefs'));
				$id = $allprefs['id'];
				$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$id'";
				$res = db_query($sql);
				$row = db_fetch_assoc($res);
				$name = $row['name'];
				$usedmts=$allprefs['usedmts'];
				$mineturnset=get_module_setting("mineturnset");
				$mineturns=$mineturnset-$usedmts;
				if ($badguy['type']=="Ghost"){
					if ($usedmts<$mineturnset) addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
					addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
					output("`n`0You make your final strike and `qToothy's`0 bones fall at your feet.");
					output("There's not much left that you can collect except his namesake, `qToothy's Tooth`0.");
					output("`n`nMaybe Grober would be interested in this pleasant little artifact.");
					$expmultiply = e_rand(7,12);
					$expbonus=$session['user']['dragonkills']+3;
					$expgain =($session['user']['level']*$expmultiply+$expbonus);
					$session['user']['experience']+=$expgain;
					output("`n`#You have gained `7%s `#experience.`n",$expgain);
				}elseif ($badguy['type']=="Troll1"){
					if ($usedmts<$mineturnset) addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
					addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
					output("`^`n`bYou have defeated the `%Under the Well Troll`^!`b`n");
					$expmultiply = e_rand(9,15);
					$expbonus=$session['user']['dragonkills']+2;
					$expgain =($session['user']['level']*$expmultiply+$expbonus);
					$session['user']['experience']+=$expgain;
					output("`n`#You have gained `7%s `#experience.`n",$expgain);
				}elseif ($badguy['type']=="Troll2"){
					addnav("Continue","runmodule.php?module=metalmine&op=contgd2");
					output("`^`n`bYou have defeated the `QCave Troll`^!`b`n");
					$gold=e_rand(50,350);
					$session['user']['gold']+=$gold;
					$expmultiply = e_rand(9,21);
					$expbonus=$session['user']['dragonkills']+2;
					$expgain =($session['user']['level']*$expmultiply+$expbonus);
					$session['user']['experience']+=$expgain;
					output("`n`@You search through the smelly corpse and find `^%s gold`@.`n",$gold);
					output("`n`#You have gained `7%s `#experience.`n",$expgain);
				}elseif ($badguy['type']=="Undead"){
					addnav("Continue","runmodule.php?module=metalmine&op=chamber&loc=".get_module_pref('pqtemp'));
					output("`^`n`bYou have defeated the `QGrumpy Mummy`^!`b`n");
					$gold=e_rand(50,350);
					$session['user']['gold']+=$gold;
					$expmultiply = e_rand(8,15);
					$expbonus=$session['user']['dragonkills'];
					$expgain =($session['user']['level']*$expmultiply+$expbonus);
					$session['user']['experience']+=$expgain;
					output("`n`@You search through the smelly corpse and find `^%s gold`@.`n",$gold);
					output("`n`#You have gained `7%s `#experience.`n",$expgain);
					output("`n`0As you smile at your success, you realize the room is filling with sand!");
					$allprefs['loc1']=1;
					set_module_pref('allprefs',serialize($allprefs));
				}elseif($badguy['type']=="Human"){
					if ($usedmts<$mineturnset) addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
					addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
					$gold=e_rand(200,400);
					$session['user']['gold']+=$gold;
					$expmultiply = e_rand(10,20);
					$expbonus=$session['user']['dragonkills']*2;
					$expgain =($session['user']['level']*$expmultiply+$expbonus);
					$session['user']['experience']+=$expgain;
					output("`n`@You decide leaving him beat to a pulp is good enough for you.`n`nYou search through his wallet and take `^%s gold`@.`n",$gold);
					output("`n`#You have gained `7%s `#experience.`n",$expgain);
				}elseif($badguy['type']=="bear"){
					$expbonus=$session['user']['dragonkills']*4;
					$expgain =($session['user']['level']*39+$expbonus);
					$session['user']['experience']+=$expgain;
					output("`n`%Silly `qB`^ear`%! When will you ever learn?`n");
					output("`n`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
					if(is_module_active("bearhof")) increment_module_pref("bearkills",1,"bearhof");
					if ($usedmts<$mineturnset) addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
					addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
				}elseif($badguy['type']=="player"){
					require_once("lib/systemmail.php");
					$subj = array("`^A Rumble in the Mine");
					$body = array("`0You were involved in a fight in the mine with %s`0.  Unfortunately, you were defeated in battle.`n`nLuckily, all that's been wounded is your pride.  Perhaps you should let %s know to be nicer to you next time.",$session['user']['name'],$session['user']['name']);
					systemmail($id,$subj,$body);
					$expbonus=$session['user']['dragonkills']*2;
					$expgain =($session['user']['level']*20+$expbonus);
					$session['user']['experience']+=$expgain;
					$session['user']['gold']+=250;
					output("`n`%You give a thorough thrashing to `^%s`%.  You only hope that people will learn to stop messing with you.`n",$name);
					output("`n`@`bYou've gained `#%s experience`@ and `^250 gold`@.`b`n`n",$expgain);
					addnews("%s`0 was defeated by %s in a fight deep in the mine.",$name,$session['user']['name']);
					if ($usedmts<$mineturnset) addnav("Work The Mine More","runmodule.php?module=metalmine&op=work");
					addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
				}
				$badguy=array();
				$session['user']['badguy']="";
			}elseif ($defeat){
				$allprefs=unserialize(get_module_pref('allprefs'));
				$id=$allprefs['id'];
				$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$id'";
				$res = db_query($sql);
				$row = db_fetch_assoc($res);
				$name = $row['name'];
				if ($badguy['type']=="Ghost"){
					$exploss = round($session['user']['experience']*.1);
					$session['user']['experience']-=$exploss;
					$session['user']['gold']=0;
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					$allprefs['inmine']=0;
					$allprefs['toothy']=$allprefs['toothy']-1;
					set_module_pref('allprefs',serialize($allprefs));
					addnav("The Shades","shades.php");
					addnews("`% %s`0 has been slain by a `qToothy McPicker`0 while in the Mine.",$session['user']['name']);
					output("`n`b`^Toothy grabs all your gold and dances, singing `q'I got my `^gold back!'`0`b`n");
					output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				}elseif($badguy['type']=="Troll2"){
					$exploss = round($session['user']['experience']*.1);
					$session['user']['experience']-=$exploss;
					$session['user']['gold']=0;
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					$allprefs['inmine']=0;
					set_module_pref('allprefs',serialize($allprefs));
					addnav("The Shades","shades.php");
					addnews("`% %s`0 has been slain by a `^Cave Troll`0 while in the Mine.",$session['user']['name']);
					output("`n`nYou feel the pages of time close over you.`n`n");
					output("`b`^All gold on hand has been lost!`b`n");
					output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				}elseif ($badguy['type']=="Undead"){
					output("`n`bThe `QMummy`4 gets one of its wrappings around your neck and you die.`b`n");
					output("`n`n`0You lose `^all your gold on hand`0.");
					output("`nYou lose `b`\$10 percent`0`b of your experience.");
					output("`nTry again tomorrow.");
					addnews("`% %s`0 has been slain by a `QMummy`0 trying to loot its sarcophogus.",$session['user']['name']);
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					$session['user']['gold']=0;
					$session['user']['experience']*=.9;
					addnav("The Shades","shades.php");
					$allprefs['usedmts']=get_module_setting("mineturnset");
					$allprefs['loc1']=0;
					$allprefs['loc2']=0;
					$allprefs['loc9']=0;
					$allprefs['loc11']=0;
					$allprefs['loc17']=0;
					$allprefs['loc24']=0;
					$allprefs['mazeturn']=0;
					$allprefs['inmine']=0;
					set_module_pref('allprefs',serialize($allprefs));
					clear_module_pref("maze",0);
					clear_module_pref("pqtemp",0);
				}elseif($badguy['type']=="Human"){
					output("`n`0The `@Burly Miner`0 gives you one last whack and leaves you on the brink of death. All the other miners cheer for `@Burly`0.");
					output("`n`nHe goes through your wallet and steals `^all your gold`0 and you `#lose 10 percent`0 of your experience.");
					output("`n`nYou're done working the mine for today.  Maybe you should just head home.");
					addnews("%s`0 was defeated by a Burly Miner that was itching for a fight in the mine.",$session['user']['name']);
					$session['user']['hitpoints']=1;
					$session['user']['gold']=0;
					$session['user']['experience']*=.9;
					$allprefs['usedmts']=get_module_setting("mineturnset");
					set_module_pref('allprefs',serialize($allprefs));
					addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
				}elseif($badguy['type']=="player"){
					require_once("lib/systemmail.php");
					output("`n`^%s`0 gives you one last whack and leaves you on the brink of death.`n",$name);
					output("`n`n%s goes through your wallet and steals `^all your gold`0 and you `#lose 10 percent`0 of your experience.",$name);
					output("`n`nYou're done working the mine for today.  Maybe you should just head home.");
					addnews("%s`0 was defeated by %s in a fight deep in the mine.",$session['user']['name'],$name);
					$session['user']['hitpoints']=1;
					$session['user']['gold']=0;
					$session['user']['experience']*=.9;
					$subj = array("`^A Rumble in the Mine");
					$body = array("`0You were involved in a fight in the mine with %s`0.  Luckily, you were victorious in battle!`n`nAlthough you don't gain much, you do have gloating rights.  Please feel free to exercise them at your convenience.",$session['user']['name']);
					systemmail($id,$subj,$body);
					$allprefs['usedmts']=get_module_setting("mineturnset");
					set_module_pref('allprefs',serialize($allprefs));
					addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
				}elseif ($badguy['type']=="Troll1"){
					$exploss = round($session['user']['experience']*.1);
					$session['user']['experience']-=$exploss;
					$session['user']['gold']=0;
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					$allprefs['inmine']=0;
					set_module_pref('allprefs',serialize($allprefs));
					addnav("The Shades","shades.php");
					addnews("`% %s`0 was slain by an `%Under the Well Troll`0 when trying to steal gold from a wishing well.",$session['user']['name']);
					output("`nYou have been killed by the `%Under the Well Troll`0.");
					output("`b`^All gold on hand has been lost!`b`n");
					output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				}elseif($badguy['type']=="bear"){
					$exploss = round($session['user']['experience']*.1);
					$session['user']['experience']-=$exploss;
					$session['user']['gold']=0;
					$session['user']['hitpoints']=0;
					$session['user']['alive']=false;
					$allprefs['inmine']=0;
					set_module_pref('allprefs',serialize($allprefs));
					addnav("The Shades","shades.php");
					addnews("`% %s`0 has been slain by a `qG`^reat `qB`^ig `qB`^ear`0 after rooting around in its den.",$session['user']['name']);
					output("`n`n`b`%You can't `Q'bear'`% to think how you got killed...`b`n");
					output("`b`%You shouldn't have `%'picked'`% on him!`b`n");
					output("`b`^All gold on hand has been lost!`b`n");
					output("`b`%You lose `#%s experience`4.`b`n`n",$exploss);
				}
				$badguy=array();
				$session['user']['badguy']="";
			}else{
				require_once("lib/fightnav.php");
				fightnav(true,false,"runmodule.php?module=metalmine");
			}
		}else{
			if ($badguy['type']=="Undead") redirect("runmodule.php?module=metalmine&op=chamber&loc=".get_module_pref('pqtemp'));	
			if ($badguy['type']=="Human" || $badguy['type']=="bear") redirect("runmodule.php?module=metalmine&op=leavemine");
		}
	}
	page_footer();
}
function metalmine_misc($op2) {
	page_header("Secret Chamber");
	output("`c`b`^Secret Chamber`0`b`c`n");
	global $session;
	$op3 = httpget('op3');
	$allprefs=unserialize(get_module_pref('allprefs'));
	$mineturnset=get_module_setting("mineturnset");
	$usedmts=$allprefs['usedmts'];
	$mineturns=$mineturnset-$usedmts;
	$hps=$session['user']['hitpoints'];
	if ($op2=="25a"){
		output("You step forward and suddenly see a huge black ball in the center of the room sitting on a tall pedestal.  It looks like the chamber is empty except for the strange item.");
		$allprefs['loc25a']=1;
		set_module_pref('allprefs',serialize($allprefs));
		addnav("Continue","runmodule.php?module=metalmine&op=chamber2&loc=".get_module_pref('pqtemp'));
	}
	if ($op2=="25t"){
		output("You step forward and suddenly you hear a huge grinding sound.  You turn around to leave but the exit crumbles.");
		output("`n`nThe walls are closing in on you!!!");
		output("`n`nIt looks like there might be an exit directly to the south.");
		$allprefs['loc25t']=1;
		set_module_pref('allprefs',serialize($allprefs));
		addnav("Continue","runmodule.php?module=metalmine&op=tunnel&loc=".get_module_pref('pqtemp'));
	}
	if ($op2=="8ball"){
		if ($allprefs['loc18a']==0){
			output("You look up at the mighty `)Black Ball`0 wondering what it is for.");
			output("`n`nFor some reason, you feel very insecure about your life and your future.  Perhaps the large `)Black Ball`0 can help you.");
			output("`n`nWould you like to ask it a question?");
			addnav("Magic Black Ball");
			addnav("Yes","runmodule.php?module=metalmine&op=chamber2&op2=aska");
			addnav("No","runmodule.php?module=metalmine&op=chamber2&loc=".get_module_pref('pqtemp'));
		}else{
			output("The Magic Black Ball refuses to hear your questions.");
			addnav("Continue","runmodule.php?module=metalmine&op=chamber2&loc=".get_module_pref('pqtemp'));
		}
	}
	if ($op2=="aska"){
		output("What would you like to ask the `)Magic Black Ball`0?`n`n");
		output_notl("`c");
		output("`^<a href=\"runmodule.php?module=metalmine&op=chamber2&op2=question&op3=1\">`^Will I be Rich?`n</a>",true);
		addnav("","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=1");
		output("`^<a href=\"runmodule.php?module=metalmine&op=chamber2&op2=question&op3=2\">`%Will I be Pretty?`n</a>",true);
		addnav("","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=2");
		output("`^<a href=\"runmodule.php?module=metalmine&op=chamber2&op2=question&op3=3\">`&Will I be Strong?`n</a>",true);
		addnav("","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=3");
		output("`^<a href=\"runmodule.php?module=metalmine&op=chamber2&op2=question&op3=4\">`QWill I be Fast?`n</a>",true);
		addnav("","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=4");
		output("`^<a href=\"runmodule.php?module=metalmine&op=chamber2&op2=question&op3=5\">`#Will I be Smart?`n</a>",true);
		addnav("","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=5");
		output_notl("`c");
		addnav("Magic Black Ball");
		addnav("`^Rich","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=1");
		addnav("`%Pretty","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=2");
		addnav("`&Strong","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=3");
		addnav("`QFast","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=4");
		addnav("`#Smart","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=5");
	}
	if ($op2=="question"){
		$allprefs['loc18a']=1;
		set_module_pref('allprefs',serialize($allprefs));
		output("You pose your question and wait for the `)Magic Black Ball`0 to respond.");
		output("You start to feel a little foolish talking to a `)black ball`0, but then suddenly the `)Black Ball`0 starts bouncing up and down as if it being shaken.  Before you have a chance to step back, the answer to your question comes to you:`n`n");
		$ball=translate_inline(array("","Outlook Good","You May Rely On It","Most Likely","Yes","Yes Definately","It Is Certain","It Is Decidedly So","Signs Point To Yes","Without A Doubt","As I See It, Yes","Outlook Not So Good","My Reply Is No","Don't Count On It","Very Doubtful","My Sources Say No","Ask Again Later","Concentrate and Ask Again","Reply Hazy,  Try Again","Better Not Tell You Now"));
		$rand=e_rand(1,20);
		output("`c`#%s`0`c`n",$ball[$rand]);
		if ($rand<=10){
			require_once("lib/titles.php");
			require_once("lib/names.php");
			addnav("Continue","runmodule.php?module=metalmine&op=chamber2&loc=".get_module_pref('pqtemp'));
			$joke=e_rand(1,5);
			if ($op3=="1"){
				if ($joke==1){
					$newtitle = "Rich";
					$newname = change_player_title($newtitle);
					$session['user']['title'] = $newtitle;
					$session['user']['name'] = $newname;
					output("Having asked if you would be `^Rich`0, you suddenly find that that's what your title is.");
					output("`n`nPerhaps next time you'll be a little more clear on what you're asking the `)Magic Black Ball`0.");
				}else{
					$gold=e_rand(250,500);
					output("You look down and see `^%s gold`0 at your feet.  It seems like you're Rich!",$gold);
					$session['user']['gold']+=$gold;
				}
			}elseif ($op3=="2"){
				if ($joke==1){
					$newtitle = "Pretty";
					$newname = change_player_title($newtitle);
					$session['user']['title'] = $newtitle;
					$session['user']['name'] = $newname;
					output("Having asked if you will be `^Pretty`0, you suddenly find that that's what your title is.");
					output("`n`nPerhaps next time you'll be a little more clear on what you're asking the `)Magic Black Ball`0.");
				}else{
					output("You suddenly feel more pretty!  You gain `%One Charm`0!");
					$session['user']['charm']++;
				}
			}elseif ($op3=="3"){
				if ($joke==1 || $joke==2 || $joke==3){
					$newtitle = "Strong";
					$newname = change_player_title($newtitle);
					$session['user']['title'] = $newtitle;
					$session['user']['name'] = $newname;
					output("Having asked if you will be `^Strong`0, you suddenly find that that's what your title is.");
					output("`n`nPerhaps next time you'll be a little more clear on what you're asking the `)Magic Black Ball`0.");
				}else{
					output("You suddenly feel stronger!  You gain `&One Attack`0!");
					$session['user']['attack']++;
				}			
			}elseif ($op3=="4"){
				if ($joke==1 || $joke==2 || $joke==3){
					$newtitle = "Fast";
					$newname = change_player_title($newtitle);
					$session['user']['title'] = $newtitle;
					$session['user']['name'] = $newname;
					output("Having asked if you will be `^Fast`0, you suddenly find that that's what your title is.");
					output("`n`nPerhaps next time you'll be a little more clear on what you're asking the `)Magic Black Ball`0.");
				}else{
					output("You suddenly feel faster!  You gain `&One Defense`0!");
					$session['user']['defense']++;
				}
			}else{
				if ($joke==1 || $joke==2 || $joke==3){
					$newtitle = "Smart";
					$newname = change_player_title($newtitle);
					$session['user']['title'] = $newtitle;
					$session['user']['name'] = $newname;
					output("Having asked if you will be `^Smart`0, you suddenly find that that's what your title is.");
					output("`n`nPerhaps next time you'll be a little more clear on what you're asking the `)Magic Black Ball`0.");
				}else{
					$expmultiply = e_rand(5,8);
					$expbonus=$session['user']['dragonkills'];
					$expgain =($session['user']['level']*$expmultiply+$expbonus);
					$session['user']['experience']+=$expgain;
					output("You suddenly feel smarter!  You gain `#%s experience`0!",$expgain);
				}
			}
		}elseif ($rand<=15){
			addnav("Continue","runmodule.php?module=metalmine&op=chamber2&loc=".get_module_pref('pqtemp'));
			if ($op3=="1"){
				$gold=$session['user']['gold'];
				if ($gold>100){
					if ($gold<300){
						$session['user']['gold']=0;
						output("You find all your `^gold`0 has disappeared.");
					}else{
						$session['user']['gold']-=250;
						output("You find that you've lost `^250 gold`0.");
					}
					output("If your plan on getting rich by talking to big `)black balls`0 in the middle of nowhere, you better think again.");
				}elseif ($session['user']['gems']>0){
					$session['user']['gems']--;
					output("You find that you've lost a `%gem`0.");
					output("You kick the `)Magic Black Ball`0 and grumble in frustration.");
				}else{
					output("You realize you don't have much gold, you don't have any gems, and you're talking to a large `)black ball`0 in the middle of a `^secret chamber`0.");
					output("If this is how you plan to get rich, you better think again.");
				}
			}elseif ($op3=="2"){
				output("Suddenly, a huge stick comes flying out of the `)Magic Black Ball`0 and hits you.");
				output("You see the word `&'Ugly'`0 on the stick before it disappears.");
				output("`n`nYou've been hit with the `&Ugly Stick`0! You `\$lose one charm`0.");
				$session['user']['charm']--;
			}elseif ($op3==3 || $op3==4){
				output("You feel sick to your stomach.  You feel weaker!");
				apply_buff('blackball',array(
					"name"=>"`)Black Ball Weakness",
					"rounds"=>10,
					"wearoff"=>"Outlook Good for you to start feeling better!",
					"atkmod"=>.75,
					"roundmsg"=>"Outlook Not So Good for you to feel better yet.",
				));
			}else{
				output("You feel so stupid! It's like you've forgotten things you've learned in the past.");
				if ($session['user']['experience']>100){
					output("You `\$lose 100 experience`0 points.");
					$session['user']['experience']-=100;
				}else{
					output("You `\$lose all your experience`0.");
					$session['user']['experience']=0;
				}
			}
		}elseif ($rand<=19){
			output("Okay, go ahead and ask again.");
			addnav("Magic Black Ball");
			addnav("Ask Again","runmodule.php?module=metalmine&op=chamber2&op2=question&op3=$op3");
		}else{
			addnav("Continue","runmodule.php?module=metalmine&op=chamber2&loc=".get_module_pref('pqtemp'));
			output("You can't believe that you didn't get an answer.  You ask again, but the `)Magic Black Ball`0 is ignoring you.");		
		}
	}
	if ($op2=="dead"){
		output("Oh No!!! The walls close in on you and you get crushed.");
		output("`n`nYou've died and lost all your gold in the chamber.");
		output("`nYou lose `b`\$10 percent`0`b of your experience.");
		output("`nTry again tomorrow.");
		$session['user']['hitpoints']=0;
		$session['user']['alive']=false;
		$session['user']['gold']=0;
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['loc25t']=0;
		$allprefs['mazeturn']="";
		set_module_pref('allprefs',serialize($allprefs));
		clear_module_pref("maze");
		clear_module_pref("pqtemp");
		$session['user']['experience']*=.9;
		addnav("The Shades","shades.php");
	}
	if ($op2=="24"){
		output("As soon as you step into the room, torches suddenly light up!");
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['loc24']=1;
		set_module_pref('allprefs',serialize($allprefs));
		addnav("Continue","runmodule.php?module=metalmine&op=chamber&loc=".get_module_pref('pqtemp'));
	}
	if ($op2=="17"){
		output("You enter into a side chamber and strangely, even more torches suddenly light up.");
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['loc17']=1;
		set_module_pref('allprefs',serialize($allprefs));
		addnav("Continue","runmodule.php?module=metalmine&op=chamber&loc=".get_module_pref('pqtemp'));
	}
	if ($op2=="9"){
		output("You enter into a side chamber and strangely, even more torches suddenly light up.");
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['loc9']=1;
		set_module_pref('allprefs',serialize($allprefs));
		addnav("Continue","runmodule.php?module=metalmine&op=chamber&loc=".get_module_pref('pqtemp'));
	}
	if ($op2=="2"){
		output("You've found a secret door! It leads to the west.  Are you brave enough to enter it??");
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['loc2']=1;
		set_module_pref('allprefs',serialize($allprefs));
		addnav("Continue","runmodule.php?module=metalmine&op=chamber&loc=".get_module_pref('pqtemp'));
	}
	if ($op2=="11"){
		output("It looks like there's a pile of rags here on the ground.  Will you search through them?");
		addnav("Leave","runmodule.php?module=metalmine&op=chamber&loc=".(get_module_pref('pqtemp')+5));
		addnav("Search","runmodule.php?module=metalmine&op=chamber&op=chamber&op2=search&op3=11");
	}
	if ($op2=="search"){
		output("You carefully search through the rags.`n`n");
		$allprefs=unserialize(get_module_pref('allprefs'));
		if (get_module_pref("pqtemp")==11) $allprefs['loc11']=1;
		else $allprefs['loc1']=1;;
		switch(e_rand(1,5)){
			case 1:
				output("After searching for a while, you can't find anything useful.");
				if ($mineturns>0){
					output("`n`nYou spend `^1 Mine Turn`0 looking through the rags.");
					$allprefs['usedmts']=$allprefs['usedmts']+1;
				}
			break;
			case 2:
				output("You find a `%gem`0!  When you bend to pick it up, you feel a sting.`n`n");
				if ($hps>1){
					output("You lose all your hitpoints except `\$1`0.  Luckily you keep your wits about you and keep the gem.");
					$session['user']['gems']++;
					$session['user']['hitpoints']=1;
				}else output("Although it doesn't harm you, you get disoriented and drop the gem.");
			break;
			case 3:
				$gold=e_rand(50,300);
				output("It's a pile of rags.  Why are you wasting your time?");
				output("`n`nWhy? Because there's gold in them there rags!!");
				output("You find `^%s gold`0!",$gold);
				$session['user']['gold']+=$gold;
			break;
			case 4:
				output("You don't find anything useful, and you quickly abandon your search.  No loss.");
			break;
			case 5:
				output("You find an ancient artifact!`n`n");
				if (get_module_setting("permhps")==1){
					if (is_module_active("jonescave") && get_module_pref("artfinder","jonescave")>0){
						output("Having mastered archaeology at the `%C`3ave `%o`3f  `\$Q`^u`@e`#t`!z`%l`\$z`^a`@c`#a`!t`%e`\$n`^a`@n`#g`!o`0, you deduce that this is quite a powerful artifact indeed!`n`n");
						if ($session['user']['turns']>0){
							output("You spend a turn analyzing the item and instead of just one permanent hitpoint, you gain `bTWO`b permanent hitpoints from the item before it dissolves into sand.");
							$session['user']['turns']--;
							$session['user']['maxhitpoints']+=2;
						}else{
							output("You realize you could probably get two permanent hitpoints from the item if only you had an extra turn to spend studying it.");
							output("With the time you have, you're only able to give enough study to the item to get `@one hitpoint`0 out of it. Next time you should come to mine with more free time.");
							$session['user']['maxhitpoints']++;
						}
					}else{
						$chance=e_rand(1,3);
						if ($chance==1) output("You pick up the artifact and it disintegrates in your hand.  You stop and wonder what it was for, but after a while decide to continue with your explorations.");
						elseif ($chance==2){
							if ($session['user']['turns']>0){
								output("You `\$spend a turn`0 studying the strange artifact and push a button on the back.  Suddenly, you're enveloped in a bright white light!");
								output("`n`nYou're changed and feel more powerful as you realize you've `@gained a permanent hitpoint`0! The artifact disintegrates in your hands.");
								$session['user']['turns']--;
								$session['user']['maxhitpoints']++;
							}else{
								output("You pick up the artifact and it disintegrates in your hand.  If only you had a turn to study it, you could probably figure out how to extract some magic from it.  Unfortunately, you don't have any turns left to study it.  It disintegrates before your very eyes as you sit back in exacerbation.");
							}
						}else{
							output("As you touch the artifact, a white light envelopes your body and you feel your body improving.`n`nYou `@gain a permanent hitpoint`0!");
							output("`n`nThe artifact disintegrates in your hands.");
							$session['user']['maxhitpoints']++;
						}
					}
				}else{
					output("As you pick up the artifact, you feel a healing power sweep over you!`n`n");
					if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
						output("You're healed back to normal!");
						$session['user']['hitpoints']=$session['user']['maxhitpoints'];
					}else{
						output("You gain `@10`0 hitpoints!");
						$session['user']['hitpoints']+=10;
					}
				}
			break;
		}
		if (get_module_pref("pqtemp")==1){
			output("`n`nYou finish searching around the sarcophogus and suddenly the chamber starts to fill with sand. I think it's time for you to leave!");
		}
		set_module_pref('allprefs',serialize($allprefs));
		addnav("Continue","runmodule.php?module=metalmine&op=chamber&loc=".get_module_pref('pqtemp'));
	}
	if ($op2=="1"){
		output("You find yourself staring and a beautifully ornate sarcaphogus.");
		output("You realize that you have several very interesting choices at this time.");
		output("You can leave without disturbing the rest of the deceased, you can search around the sarcophogus, or you can take the cover off the sarcophogus to look through it.");
		output("`n`nIt's your choice!");
		addnav("Search around the Sarcophogus","runmodule.php?module=metalmine&op=chamber&op=chamber&op2=search&op3=1");
		addnav("Search the Sarcophogus","runmodule.php?module=metalmine&op=chamber&op=chamber&op2=sarc");
		addnav("Leave","runmodule.php?module=metalmine&op=chamber&loc=".(get_module_pref('pqtemp')+1));
	}
	if ($op2=="sarc"){
		if (is_module_active("alignment")){
			output("This is an `\$Evil`0 act and your alignment suffers for it.");
			increment_module_pref("alignment",-5,"alignment");
		}else output("You're an explorer! You must explore!");
		output("`n`nYou carefully pry the lid of the sarcophogus off.");
		$allprefs=unserialize(get_module_pref('allprefs'));
		$allprefs['loc1']=1;
		switch(e_rand(5,5)){
			case 1: case 2:
				output("You don't find anything of value.  It looks like someone was able to loot the sarcophogus before you got a chance to.");
				output("`n`nYou sigh with frustration and suddenly feel the room shake.  It seems to be filling with sand!");
			break;
			case 3: case 4:
				output("You find a mummy embalmed and surrounded by gold!");
				$gold=e_rand(200,400);
				output("You find `^%s gold`0!",$gold);
				$session['user']['gold']+=$gold;
			break;
			case 5:
				output("You feel a bit frustrated because the sarcophogus is empty.  All that work for nothing.");
				output("`n`nYou decide to leave but you suddenly feel a tap on your shoulder.");
				output("`n`nThe owner of the sarcophogus, a very ragged looking `QMummy`0, looks at you and starts shaking its finger.");
				if (is_module_active("alignment")){
					if (get_module_pref("alignment","alignment")<get_module_setting("evilalign","alignment")) output("If you weren't so evil, you'd probably feel guilty right now.");
					else output("Not being very evil, you feel a bit guilty about disturbing the poor guy's sleep.");
				}
				output_notl("`n`n");
				$chance=e_rand(1,3);
				if ($chance==1){
					output("You decide that your best option now is to fight.  The `QMummy`0 seems to agree.");
					addnav("Fight the Mummy","runmodule.php?module=metalmine&op=mummy");
					blocknav("runmodule.php?module=metalmine&op=chamber&loc=".get_module_pref('pqtemp'));
					$allprefs['loc1']=2;
				}else{
					output("Before you get a chance to defend yourself, the mummy curses you and dissipates into dust.");
					output("`n`nThe room starts to fill with sand and you find that you're going to need to leave.");
					apply_buff('curse',array(
						"name"=>"`&Mummy's Curse",
						"rounds"=>15,
						"wearoff"=>"`&The Curse of the Mummy is over!",
						"atkmod"=>.75,
						"defmod"=>.75,
						"roundmsg"=>"`&The curse prevents you from performing at your best",
					));
				}
			break;
		}
		set_module_pref('allprefs',serialize($allprefs));
		addnav("Continue","runmodule.php?module=metalmine&op=chamber&loc=".get_module_pref('pqtemp'));
	}
	page_footer();
}
?>